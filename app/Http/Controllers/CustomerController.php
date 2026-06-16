<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService,
    ) {}

    public function index(Request $request): View
    {
        $query = Customer::with('membership');
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }
        $customers = $query->latest()->paginate(20)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        $memberships = \App\Models\Membership::active()->orderBy('min_spent')->get();
        return view('customers.create', compact('memberships'));
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = $this->customerService->createCustomer($request->validated());
        return redirect()->route('customers.index')->with('success', "Pelanggan {$customer->name} berhasil ditambahkan");
    }

    public function show(Customer $customer): View
    {
        $customer->load(['membership', 'sales' => fn($q) => $q->latest()->limit(20)]);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        $memberships = \App\Models\Membership::active()->orderBy('min_spent')->get();
        return view('customers.edit', compact('customer', 'memberships'));
    }

    public function update(StoreCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->customerService->updateCustomer($customer, $request->validated());
        return redirect()->route('customers.index')->with('success', 'Data pelanggan diperbarui');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->customerService->deleteCustomer($customer);
        return redirect()->route('customers.index')->with('success', 'Pelanggan dihapus');
    }

    public function searchApi(Request $request)
    {
        $request->validate(['q' => 'nullable|string|max:100']);
        $customers = $this->customerService->search($request->string('q')->toString() ?? '', 30);
        return response()->json(['data' => $customers]);
    }
}
