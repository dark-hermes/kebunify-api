<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsultationTransaction;

class PaymentController extends Controller
{
    public function show(string $type, string $snap_token)
    {
        return view('payments.index', [
            'type' => $type,
            'snap_token' => $snap_token
        ]);
    }

    public function updateConsultationStatus(Request $request, string $type, string $snap_token)
    {
        $request->validate([
            'status' => 'required|in:success,failed',
        ]);

            $status = $request->query('status');

            $transaction = ConsultationTransaction::where('snap_token', $snap_token)->firstOrFail();

            if ($transaction) {
                if ($status === 'success') {
                    $transaction->update([
                        'status' => 'success',
                        'payment_date' => now()
                    ]);

                    $message = 'Pembayaran berhasil! Konsultasi Anda akan segera dimulai.';
                } else {
                    $transaction->update([
                        'status' => 'failed'
                    ]);

                    $message = 'Pembayaran gagal! Silakan buat sesi konsultasi baru.';
                }
            }

            return view('payments.consultation', [
                'status' => $status,
                'message' => $message
            ]);
    }
}
