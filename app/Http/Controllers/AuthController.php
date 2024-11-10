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
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => __('auth.failed')
            ], 401);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;
        return response()->json([
            'message' => __('auth.success'),
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('roles'));
    }
<<<<<<< HEAD

    public function register(Request $request){
    
        $data = Validator::make($request->all(), [
        
            'name' => 'required',
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        if($data->fails()){
            return response()->json('Formmu belum sesuai', 406);
        }
        else{
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            return response()->json('Akun anda telah dibuat',200);
        }
    }
=======
>>>>>>> f1c1db2ce0245910a7e28a1c02f5d82cb4a3f977
}
