<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_user', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_user' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email|max:100',
            'no_hp' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,resepsionis,tamu',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('users', 'public');
        }

        $user = User::create($data);

        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'data' => $user,
        ], 201);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_user' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:users,email,' . $id . '|max:100',
            'no_hp' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'role' => 'sometimes|in:admin,resepsionis,tamu',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['foto', 'password']);

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('users', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return response()->json([
            'message' => 'User berhasil diupdate',
            'data' => $user->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus']);
    }

    // Profile management
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'nama_user' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:users,email,' . $user->id . '|max:100',
            'no_hp' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'current_password' => 'required_with:password|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => 'Password lama salah'], 422);
            }
        }

        $data = $request->except(['foto', 'password', 'current_password', 'password_confirmation']);

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('users', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profil berhasil diupdate',
            'data' => $user->fresh(),
        ]);
    }
}
