<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
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
        $consultations = Consultation::where('user_id', $userId)->get();
        return response()->json($consultations, 200);
    }

    public function show($id)
    {
        $consultation = Consultation::findOrFail($id);
        return response()->json($consultation, 200);
    }

    public function store(Request $request)
    {
        $consultation = Consultation::create([
            'user_id' => Auth::id(),
            'topic' => $request->topic,
            'description' => $request->description,
            'status' => 'open',
            'content_payment_status' => 'unpaid',
        ]);

        return response()->json($consultation, 201);
    }

    public function update(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->update($request->only(['topic', 'description']));

        return response()->json($consultation, 200);
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
