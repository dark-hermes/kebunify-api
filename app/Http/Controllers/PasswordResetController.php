<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordLink;
use App\Models\User;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class PasswordResetController extends Controller
{
    public function setResetLinkEmail(Request $request)
    {
        $data = $request->validate(['email' => 'required']);


        $url = URL::temporarySignedRoute('password.reset', now()->addMinute(30), ['email' => $request->email]);
        # $url = str_replace(env('app_url', env('FRONTEND_URL')));

        Mail::to($request->email)->send(new ResetPasswordLink($url));

        return response()->json([
            'message' => 'Link reset password sudah dikirim ke email-mu!'
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'password' => 'required'
        ]);
        $cari = User::where('email', $request->email)->first();

        $cari->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Password telah diubah'
        ]);
    }
}
