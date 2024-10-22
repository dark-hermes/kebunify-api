<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationEmailController extends Controller
{
    public function send(Request $request)
    {
        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('auth.email.verification.failed')
            ], 500);
        }

        return response()->json([
            'message' => __('auth.email.verification.sent')
        ]);
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        // return view('auth.verify-email');

        return response()->json([
            'message' => __('auth.email.verification.verified')
        ]);
    }
}
