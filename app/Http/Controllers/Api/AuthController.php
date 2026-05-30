<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $user = User::create([
                'username' => $request->username,
                'name' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'message' => 'Register berhasil',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Register gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
{
    $admin = Admin::where(
        'email',
        $request->email
    )->first();

    if (!$admin) {
        return response()->json([
            'message' => 'Admin tidak ditemukan'
        ], 404);
    }

    if (!Hash::check(
        $request->password,
        $admin->password
    )) {
        return response()->json([
            'message' => 'Password salah'
        ], 401);
    }

    return response()->json([
        'message' => 'Login berhasil',
        'user' => $admin
    ]);
}
}