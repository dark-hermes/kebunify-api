<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('create_role,sanctum'), only: ['store']),
            new Middleware(PermissionMiddleware::using('view_role,sanctum'), only: ['index', 'show']),
            new Middleware(PermissionMiddleware::using('update_role,sanctum'), only: ['update']),
            new Middleware(PermissionMiddleware::using('delete_role,sanctum'), only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $limit = $request->query('limit') ?? 10;

        try {
            $roles = Role::query()
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'like', '%' . $search . '%');
                })->with('permissions')->paginate($limit);

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $roles,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function list(Request $request)
    {
        $search = $request->query('search');
        try {
            $roles = Role::query()
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'like', '%' . $search . '%');
                })->get();

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $roles,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'permissions' => 'array|nullable',
        ]);

        try {
            $role = null;

            DB::transaction(function () use ($request, &$role) {
                $role = Role::create([
                    'name' => $request->name,
                ]);

                if ($request->has('permissions')) {
                    $role->givePermissionTo($request->permissions);
                }
            });

            return response()->json([
                'message' => __('http-statuses.201'),
                'data' => $role,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $role = Role::with('permissions')->find($id);

            if (!$role) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $role,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string',
            'permissions' => 'array|nullable',
        ]);

        try {
            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            DB::transaction(function () use ($request, $role) {
                $role->update([
                    'name' => $request->name,
                ]);

                if ($request->has('permissions')) {
                    $role->syncPermissions($request->permissions);
                }
            });

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $role,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            DB::transaction(function () use ($role) {
                $role->permissions()->detach();
                $role->delete();
            });

            return response()->json([
                'message' => __('http-statuses.200'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }
}
