<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
                $document = $request->file('document');
                $documentName = time() . '_' . $document->getClientOriginalName();
                $document->storeAs('documents', $documentName, 'public');

                $document = Document::create([
                    'user_id' => Auth::id(),
                    'role_applied' => $request->role_applied,
                    'document_path' => 'storage/documents/' . $documentName,
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
        try {
            $application = Document::findOrFail($id);

            if ($application->status !== 'PENDING') {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            $application->update(['status' => 'APPROVED']);
            return response()->json([
                'message' => __('responses.approve.success', [
                    'resource' => __('resources.application')
                ]),
                'data' => $application
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve application'
            ], 500);
        }
    }

    public function rejectApplication(Request $request, $id)
    {
        try {
            $application = Document::findOrFail($id);

            if ($application->status !== 'PENDING') {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            $application->update(['status' => 'REJECTED']);
            return response()->json([
                'message' => __('responses.reject.success', [
                    'resource' => __('resources.application')
                ]),
                'data' => $application
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to reject application'
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $role = $request->query('role');
        $status = strtoupper($request->query('status'));
        $limit = $request->query('limit') ?? 10;
        $search = $request->query('search');
        $sort = $request->query('sort') ?? '-created_at';

        $role = in_array($role, ['seller', 'expert']) ? $role : null;
        $status = in_array($status, ['PENDING', 'APPROVED', 'REJECTED']) ? $status : null;

        // Determine sort direction and column
        $sortDirection = $sort[0] === '-' ? 'desc' : 'asc';
        $sort = ltrim($sort, '-');
        $isSortRelation = strpos($sort, '.') !== false;

        try {
            $applications = Document::query()
                ->when($role, function ($query, $role) {
                    return $query->where('role_applied', $role);
                })
                ->when($status, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->when($search, function ($query, $search) {
                    return $query->where('role_applied', 'like', '%' . $search . '%')
                        ->orWhere('status', 'like', '%' . $search . '%')
                        ->orWhereRelation('user', 'name', 'like', '%' . $search . '%');
                })
                ->when($isSortRelation, function ($query) use ($sort, $sortDirection) {
                    // Extract relation and column if sorting on a related model
                    [$relation, $column] = explode('.', $sort);
                    return $query->join('users', 'documents.user_id', '=', 'users.id')
                        ->orderBy("users.$column", $sortDirection);
                }, function ($query) use ($sort, $sortDirection) {
                    // Default order on the main model column
                    return $query->orderBy($sort, $sortDirection);
                })
                ->with('user')
                ->paginate($limit);

            return response()->json([
                'message' => __('responses.index.success', [
                    'resource' => __('resources.application')
                ]),
                'data' => $applications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('responses.index.failed', [
                    'resource' => __('resources.application')
                ]),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $application = Document::findOrFail($id);
            return response()->json([
                'message' => __('responses.show.success', [
                    'resource' => __('resources.application')
                ]),
                'data' => $application
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('responses.show.failed', [
                    'resource' => __('resources.application')
                ]),
            ], 404);
        }
    }
}
