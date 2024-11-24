<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationTransaction;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $query = Consultation::query();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('topic', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
        }

        return response()->json($query->get(), 200);
    }

    public function getByUserId($userId)
    {
        try {
            $consultations = Consultation::where('user_id', $userId)->get();
            return response()->json([
                'message' => 'Konsultasi Ditemukan',
                'data' => $consultations
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Konsultasi Tidak Ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function show($id)
    {
        try {
            $consultation = Consultation::findOrFail($id);
            return response()->json([
                'message' => 'Konsultasi Ditemukan',
                'data' => $consultation
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Konsultasi Tidak Ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'expert_id' => 'required|exists:experts,id',
        ]);

        try {
            $consultation = Consultation::create([
                'user_id' => Auth::id(),
                'expert_id' => $request->expert_id,
            ]);
            return response()->json([
                'message' => 'Sesi Konultasi Berhasil Dibuat',
                'data' => $consultation
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sesi Konsultasi Gagal Dibuat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeTransaction(Request $request, $id)
    {
        try {
            $consultation = Consultation::findOrFail($id);
            $consultation->transaction()->create([
                'amount' => $consultation->expert->fee_after_discount,
                'payment_receipt' => 'C' . $consultation->id . '-' . now()->format('YmdHis')
            ]);

            $consultation = Consultation::findOrFail($id);

            // Set your Merchant Server Key
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            // Set sanitization on (default)
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            // Set 3DS transaction for credit card to true
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            $params = [
                'transaction_details' => [
                    'order_id' => $consultation->transaction->payment_receipt,
                    'gross_amount' => $consultation->transaction->amount,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $transaction = ConsultationTransaction::where('consultation_id', $consultation->id)->first();
            $transaction->snap_token = $snapToken;
            $transaction->save();

            return response()->json([
                'message' => 'Pembayaran Berhasil',
                'data' => Consultation::findOrFail($id)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Pembayaran Gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function close($id)
    {
        try {
            $consultation = Consultation::findOrFail($id);
            $consultation->status = 'closed';
            $consultation->save();

            return response()->json([
                'message' => 'Konsultasi Berhasil Ditutup',
                'data' => $consultation
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Konsultasi Gagal Ditutup',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $consultation = Consultation::findOrFail($id);
            $consultation->update($request->only(['topic', 'description']));

            return response()->json([
                'message' => 'Konsultasi Berhasil Diupdate',
                'data' => $consultation
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Konsultasi Gagal Diupdate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->status = $request->status;
        $consultation->content_payment_status = $request->content_payment_status;
        $consultation->save();

        return response()->json($consultation, 200);
    }

    public function destroy($id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->delete();

        return response()->json(['message' => 'Consultation deleted'], 200);
    }
}
