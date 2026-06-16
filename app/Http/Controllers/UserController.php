<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('roles');
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }
        $users = $query->latest()->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'boolean',
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $data['is_active'] ?? true;
        $user = User::create($data);
        $user->syncRoles($request->array('roles'));
        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user): View
    {
        $roles = Role::all();
        $user->load('roles');
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'boolean',
        ]);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        $user->syncRoles($request->array('roles'));
        return redirect()->route('users.index')->with('success', 'User diperbarui');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User dihapus');
    }
}
