<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SalesService
{
    public function __construct(
        private StockService $stockService,
    ) {}

    /**
     * Create a new sale with items and payments.
     */
    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $sale = Sale::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => $data['customer_id'] ?? null,
                'user_id' => auth()->id(),
                'sale_date' => $data['sale_date'] ?? now()->toDateString(),
                'status' => 'completed',
                'subtotal' => 0,
                'discount' => 0,
                'tax' => 0,
                'total' => 0,
                'paid_amount' => 0,
                'change_amount' => 0,
                'notes' => $data['notes'] ?? null,
            ]);

            $subtotal = 0;
            $totalDiscount = 0;
            $totalTax = 0;

            foreach ($data['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $quantity = (int) $itemData['quantity'];
                $unitPrice = (float) $itemData['unit_price'];
                $discount = (float) ($itemData['discount'] ?? 0);
                $lineSubtotal = ($unitPrice * $quantity) - $discount;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'cost_price' => $product->purchase_price,
                    'discount' => $discount,
                    'subtotal' => $lineSubtotal,
                ]);

                $subtotal += $unitPrice * $quantity;
                $totalDiscount += $discount;
                if ($product->is_taxable) {
                    $totalTax += $lineSubtotal * (config('pos.tax_percent', 11) / 100);
                }

                // Deduct stock
                $this->stockService->record(
                    product: $product,
                    type: 'sale',
                    quantity: $quantity,
                    notes: "Penjualan {$sale->invoice_number}",
                    unitCost: $product->purchase_price,
                    referenceType: Sale::class,
                    referenceId: $sale->id,
                );
            }

            $sale->subtotal = $subtotal;
            $sale->discount = $totalDiscount;
            $sale->tax = $totalTax;
            $sale->total = $subtotal - $totalDiscount + $totalTax;
            $sale->save();

            // Process payments
            $paidAmount = 0;
            foreach ($data['payments'] as $paymentData) {
                $amount = (float) $paymentData['amount'];
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'method' => $paymentData['method'],
                    'amount' => $amount,
                    'reference' => $paymentData['reference'] ?? null,
                    'notes' => $paymentData['notes'] ?? null,
                ]);
                $paidAmount += $amount;
            }

            $sale->paid_amount = $paidAmount;
            $sale->change_amount = max(0, $paidAmount - $sale->total);
            $sale->save();

            // Process customer points & membership
            if ($sale->customer_id && $customer = Customer::find($sale->customer_id)) {
                $this->processCustomerPoints($sale, $customer);
            }

            return $sale->fresh(['items', 'payments', 'customer', 'user']);
        });
    }

    /**
     * Generate a unique invoice number.
     */
    private function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Sale::whereDate('created_at', today())->count() + 1;
        return 'INV-' . $date . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Process points earning/redemption for a customer.
     */
    private function processCustomerPoints(Sale $sale, Customer $customer): void
    {
        $pointsPerAmount = (int) config('pos.points_per_amount', 1000);
        $multiplier = $customer->membership?->points_multiplier ?? 1;

        // Earn points: floor(total / pointsPerAmount) * multiplier
        $pointsEarned = (int) floor($sale->total / $pointsPerAmount) * $multiplier;
        $sale->points_earned = $pointsEarned;
        $sale->save();

        $customer->addPoints($pointsEarned);
        $customer->recordTransaction((float) $sale->total);
    }

    /**
     * Cancel/refund a sale (full refund).
     */
    public function refundSale(Sale $sale, string $reason, ?string $notes = null): Sale
    {
        return DB::transaction(function () use ($sale, $reason, $notes) {
            if ($sale->status === 'cancelled' || $sale->status === 'refunded') {
                throw new RuntimeException('Penjualan sudah dibatalkan');
            }

            // Return all items to stock
            foreach ($sale->items as $item) {
                $this->stockService->record(
                    product: $item->product,
                    type: 'return',
                    quantity: $item->quantity,
                    notes: "Retur {$sale->invoice_number}: $reason",
                    referenceType: Sale::class,
                    referenceId: $sale->id,
                );
            }

            $sale->status = 'refunded';
            $sale->notes = ($sale->notes ? $sale->notes . "\n" : '') . "Refund: $reason" . ($notes ? " — $notes" : '');
            $sale->save();

            // Refund customer points if earned
            if ($sale->customer_id && $sale->points_earned > 0) {
                $customer = Customer::find($sale->customer_id);
                if ($customer && $customer->points >= $sale->points_earned) {
                    $customer->decrement('points', $sale->points_earned);
                    $customer->decrement('total_transactions');
                    $customer->decrement('total_spent', (float) $sale->total);
                }
            }

            return $sale;
        });
    }

    /**
     * Calculate cart preview (subtotal, tax, total) for given items.
     */
    public function calculateCart(array $items, float $globalDiscount = 0): array
    {
        $subtotal = 0;
        $discount = $globalDiscount;
        $tax = 0;

        foreach ($items as $item) {
            $product = Product::find($item['product_id'] ?? null);
            if (!$product) continue;

            $quantity = (int) ($item['quantity'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? $product->selling_price);
            $itemDiscount = (float) ($item['discount'] ?? 0);

            $lineTotal = ($unitPrice * $quantity) - $itemDiscount;
            $subtotal += $unitPrice * $quantity;
            $discount += $itemDiscount;

            if ($product->is_taxable) {
                $tax += $lineTotal * (config('pos.tax_percent', 11) / 100);
            }
        }

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'tax' => round($tax, 2),
            'total' => round($subtotal - $discount + $tax, 2),
        ];
    }
}
