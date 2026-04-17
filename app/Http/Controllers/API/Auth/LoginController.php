<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
  
    public function store(Request $request)
    {
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

       
        $admin = Admin::where('email', $request->email)->first();

       
        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Email atau password salah',
            ], 401);
        }

      
        $admin->tokens()->delete();

       
        $token = $admin->createToken('auth_token')->plainTextToken;

       
        return response()->json([
            'message' => 'Login berhasil',
            'user' => [
                'id' => $admin->id,
                'email' => $admin->email,
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

   
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ], 200);
    }

   
    public function destroy(Request $request)
    {
        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ], 200);
    }
}