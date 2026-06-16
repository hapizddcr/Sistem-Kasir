<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterDataController extends Controller
{
    // === CATEGORIES ===
    public function categories(Request $request): View
    {
        $query = Category::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->string('search')}%");
        }
        $categories = $query->latest()->paginate(20);
        return view('master.categories', compact('categories'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        Category::create($data);
        return back()->with('success', 'Kategori berhasil ditambahkan');
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        $category->update($data);
        return back()->with('success', 'Kategori diperbarui');
    }

    public function destroyCategory(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori memiliki produk, tidak dapat dihapus');
        }
        $category->delete();
        return back()->with('success', 'Kategori dihapus');
    }

    // === UNITS ===
    public function units(Request $request): View
    {
        $query = Unit::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->string('search')}%");
        }
        $units = $query->latest()->paginate(20);
        return view('master.units', compact('units'));
    }

    public function storeUnit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'short_name' => 'required|string|max:10',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        Unit::create($data);
        return back()->with('success', 'Satuan berhasil ditambahkan');
    }

    public function updateUnit(Request $request, Unit $unit): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'short_name' => 'required|string|max:10',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $unit->update($data);
        return back()->with('success', 'Satuan diperbarui');
    }

    public function destroyUnit(Unit $unit): RedirectResponse
    {
        if ($unit->products()->exists()) {
            return back()->with('error', 'Satuan digunakan oleh produk, tidak dapat dihapus');
        }
        $unit->delete();
        return back()->with('success', 'Satuan dihapus');
    }

    // === MEMBERSHIPS ===
    public function memberships(): View
    {
        $memberships = \App\Models\Membership::orderBy('min_spent')->get();
        return view('master.memberships', compact('memberships'));
    }

    public function storeMembership(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'min_spent' => 'required|numeric|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'points_multiplier' => 'required|integer|min:1',
            'color' => 'required|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        \App\Models\Membership::create($data);
        return back()->with('success', 'Membership berhasil ditambahkan');
    }

    public function updateMembership(Request $request, \App\Models\Membership $membership): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'min_spent' => 'required|numeric|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'points_multiplier' => 'required|integer|min:1',
            'color' => 'required|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $membership->update($data);
        return back()->with('success', 'Membership diperbarui');
    }

    public function destroyMembership(\App\Models\Membership $membership): RedirectResponse
    {
        $membership->delete();
        return back()->with('success', 'Membership dihapus');
    }
}
