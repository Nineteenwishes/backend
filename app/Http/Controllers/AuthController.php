<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register user baru
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'sometimes|string|in:admin,staff,user',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Default role adalah pengguna
            $role = $request->input('role', 'user');

            // Jika user yang request bukan admin, maka tidak bisa membuat akun admin atau staff
            if (Auth::check() && Auth::user()->role !== 'admin' && in_array($role, ['admin', 'staff'])) {
                return response()->json([
                    'message' => 'Anda tidak memiliki izin untuk membuat akun dengan role ini'
                ], 403);
            }

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $role,
            ]);

            return response()->json([
                'message' => 'Registrasi berhasil',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat registrasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Mencoba login dengan username dan password
            if (!Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
                return response()->json([
                    'message' => 'Username atau password salah'
                ], 401);
            }

            $user = User::where('username', $request->username)->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;

            // Menambahkan informasi role dalam response
            return response()->json([
                'message' => 'Login berhasil',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'role' => $user->role,
                    'isAdmin' => $user->isAdmin(),
                    'isStaff' => $user->isStaff(),
                    'isUser' => $user->isUser()
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * Mendapatkan data user yang sedang login
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Mengubah password user
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password saat ini tidak sesuai'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Password berhasil diubah'
        ]);
    }

    /**
     * Get all users (admin only)
     */
    public function index(Request $request)
    {
        // Hanya admin yang bisa melihat semua user
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk mengakses data ini'
            ], 403);
        }

        $users = User::all();
        return response()->json($users);
    }

    /**
     * Update user data
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = $request->user();

        // Validasi role update
        if ($request->has('role') && !$currentUser->isAdmin()) {
            return response()->json([
                'message' => 'Hanya admin yang bisa mengubah role user'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,'.$id,
            'role' => 'sometimes|string|in:admin,staff,user',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->all());
        return response()->json([
            'message' => 'Data user berhasil diperbarui',
            'user' => $user
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser(Request $request, $id)
{
    try {
        $currentUser = $request->user();
        $userToDelete = User::findOrFail($id);

        // Hanya admin yang bisa menghapus user
        if (!$currentUser->isAdmin()) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk menghapus user ini'
            ], 403);
        }

        // Tidak boleh menghapus diri sendiri
        if ($currentUser->id == $userToDelete->id) {
            return response()->json([
                'message' => 'Anda tidak dapat menghapus akun sendiri'
            ], 403);
        }

        $userToDelete->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan saat menghapus user',
            'error' => $e->getMessage()
        ], 500);
    }
}
}