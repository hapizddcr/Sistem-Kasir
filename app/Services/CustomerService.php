<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function createCustomer(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = $data['code'] ?? $this->generateCustomerCode();
            return Customer::create($data);
        });
    }

    public function updateCustomer(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        return $customer;
    }

    public function deleteCustomer(Customer $customer): bool
    {
        if ($customer->sales()->exists()) {
            // Soft delete only
            return $customer->delete();
        }
        return $customer->delete();
    }

    public function search(string $query, int $limit = 20)
    {
        return Customer::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->orWhere('code', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%");
        })
            ->with('membership')
            ->limit($limit)
            ->get();
    }

    public function recalculateMembership(Customer $customer): void
    {
        $membership = \App\Models\Membership::where('min_spent', '<=', $customer->total_spent)
            ->orderByDesc('min_spent')
            ->first();

        if ($membership && $customer->membership_id !== $membership->id) {
            $customer->membership_id = $membership->id;
            $customer->save();
        }
    }

    private function generateCustomerCode(): string
    {
        $last = Customer::withTrashed()->orderByDesc('id')->first();
        $next = $last ? ((int) substr($last->code, 4)) + 1 : 1;
        return 'CUST' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
