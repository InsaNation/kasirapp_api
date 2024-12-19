<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input dari request
        $credentials = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Cari pengguna berdasarkan nama
        $user = User::where('name', $credentials['name'])->first();

        if ($user) {
            // Cek apakah pengguna telah dihapus
            if ($user->is_deleted == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'User account is deleted.',
                ], 403);
            }

            // Verifikasi password
            if (Hash::check($credentials['password'], $user->password)) {
                // Generate a personal access token using Sanctum
                $token = $user->createToken('authToken')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful.',
                    'token' => $token,  // Return the token here
                    'user' => [
                        'name' => $user->name,
                        'role' => $user->role,
                        'is_deleted' => $user->is_deleted,
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect password.',
                ], 401);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }
    }

}
