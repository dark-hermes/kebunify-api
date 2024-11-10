<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DocumentController extends Controller
{
    public function applyForRole(Request $request)
    {
        Log::info('applyForRole method called');

        $request->validate([
            'role_applied' => 'required|in:seller,expert',
            'document' => 'required|file|mimes:pdf,jpg,png|max:10240'
        ]);

        Log::info('Validation passed');

        try {
            if ($request->hasFile('document') && $request->file('document')->isValid()) {
                $documentPath = $request->file('document')->store('documents');
                Log::info('Document stored at: ' . $documentPath);

                $document = Document::create([
                    'user_id' => Auth::id(),
                    'role_applied' => $request->role_applied,
                    'document_path' => $documentPath,
                    'status' => 'pending',
                ]);

                Log::info('Role application submitted', [
                    'user_id' => Auth::id(),
                    'role' => $request->role_applied,
                    'document_id' => $document->id
                ]);

                return response()->json([
                    'message' => 'Application submitted successfully',
                    'data' => $document
                ], 201);
            } else {
                return response()->json(['error' => 'Invalid document uploaded'], 400);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Role application failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Failed to submit application'
            ], 500);
        }
    }

    public function approveApplication(Request $request, $id)
    {
        $application = Document::findOrFail($id);

        if ($application->status !== 'PENDING') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $application->update(['status' => 'APPROVED']);
        return response()->json($application);
    }

    public function rejectApplication(Request $request, $id)
    {
        $application = Document::findOrFail($id);

        if ($application->status !== 'PENDING') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $application->update(['status' => 'REJECTED']);
        return response()->json($application);
    }

    public function index(Request $request)
    {
        $applications = Document::all();
        return response()->json($applications);
    }
}