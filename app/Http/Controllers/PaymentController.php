<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ConsultationTransaction;
use App\Models\Transaction;

class PaymentController extends Controller
{
    public function show(string $type, string $snap_token)
    {
        return view('payments.index', [
            'type' => $type,
            'snap_token' => $snap_token
        ]);
    }

    public function updateConsultationStatus(Request $request, string $snap_token)
    {
        $request->validate([
            'status' => 'required|in:success,failed',
        ]);

            $status = $request->query('status');

            $transaction = ConsultationTransaction::where('snap_token', $snap_token)->firstOrFail();
            $consultation = Consultation::where('id', $transaction->consultation_id)->firstOrFail();

            if ($transaction) {
                if ($status === 'success') {
                    DB::transaction(function () use ($transaction, $consultation) {
                        $transaction->update([
                            'status' => 'success',
                            'payment_date' => now()
                        ]);

                        $consultation->update([
                            'status' => 'open'
                        ]);
                    });

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

    public function updateProductPaymentStatus(Request $request, string $type, string $snap_token)
    {
        $request->validate([
            'status' => 'required|in:success,failed',
        ]);

        $status = $request->query('payment_status');

        $transaction = Transaction::where('snap_token', $snap_token)->firstOrFail();

        if ($transaction) {
            if ($status === 'success') {
                $transaction->update([
                    'payment_status' => 'paid'
                ]);

                $message = 'Pembayaran berhasil! Pesanan Anda akan segera diproses.';
            } else {
                $transaction->update([
                    'payment_status' => 'failed'
                ]);

                $message = 'Pembayaran gagal! Silakan coba lagi.';
            }
        }

        return view('payments.product', [
            'status' => $status,
            'message' => $message
        ]);
    }
}
