<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PurchaseService
{
    public function __construct(
        private StockService $stockService,
    ) {}

    public function createPurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $purchase = Purchase::create([
                'invoice_number' => $this->generatePoNumber(),
                'supplier_id' => $data['supplier_id'],
                'user_id' => auth()->id(),
                'purchase_date' => $data['purchase_date'],
                'expected_date' => $data['expected_date'] ?? null,
                'status' => 'ordered',
                'subtotal' => 0,
                'discount' => $data['discount'] ?? 0,
                'tax' => $data['tax'] ?? 0,
                'shipping_cost' => $data['shipping_cost'] ?? 0,
                'total' => 0,
                'paid_amount' => $data['paid_amount'] ?? 0,
                'notes' => $data['notes'] ?? null,
            ]);

            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $itemSubtotal = ($unitPrice * $quantity) - $discount;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'received_quantity' => 0,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'subtotal' => $itemSubtotal,
                ]);

                $subtotal += $itemSubtotal;
            }

            $purchase->subtotal = $subtotal;
            $purchase->total = $subtotal - $purchase->discount + $purchase->tax + $purchase->shipping_cost;
            $purchase->due_amount = max(0, $purchase->total - $purchase->paid_amount);
            $purchase->save();

            return $purchase->fresh(['items.product', 'supplier', 'user']);
        });
    }

    /**
     * Mark a purchase as received and add items to stock.
     */
    public function receivePurchase(Purchase $purchase, ?array $receiveQuantities = null): Purchase
    {
        if (!in_array($purchase->status, ['ordered', 'partial'])) {
            throw new RuntimeException('Purchase cannot be received in its current state');
        }

        return DB::transaction(function () use ($purchase, $receiveQuantities) {
            $allReceived = true;
            foreach ($purchase->items as $item) {
                $receivedQty = $receiveQuantities[$item->id] ?? $item->pendingQuantity();
                $newReceived = $item->received_quantity + $receivedQty;
                $item->received_quantity = min($newReceived, $item->quantity);
                $item->save();

                if ($item->received_quantity < $item->quantity) {
                    $allReceived = false;
                }

                if ($receivedQty > 0) {
                    $this->stockService->record(
                        product: $item->product,
                        type: 'purchase',
                        quantity: $receivedQty,
                        notes: "Penerimaan PO {$purchase->invoice_number}",
                        unitCost: $item->unit_price,
                        referenceType: Purchase::class,
                        referenceId: $purchase->id,
                    );

                    // Update last purchase price
                    $item->product->purchase_price = $item->unit_price;
                    $item->product->save();
                }
            }

            $purchase->status = $allReceived ? 'received' : 'partial';
            $purchase->received_date = $allReceived ? now()->toDateString() : null;
            $purchase->save();

            return $purchase;
        });
    }

    public function cancelPurchase(Purchase $purchase): Purchase
    {
        if ($purchase->status === 'received') {
            throw new RuntimeException('Cannot cancel a fully received purchase. Create a return instead.');
        }
        $purchase->status = 'cancelled';
        $purchase->save();
        return $purchase;
    }

    private function generatePoNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Purchase::whereDate('created_at', today())->count() + 1;
        return 'PO-' . $date . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
