<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Exports\UserExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('create_user,sanctum'), only: ['store']),
            new Middleware(PermissionMiddleware::using('view_user,sanctum'), only: ['index', 'show']),
            new Middleware(PermissionMiddleware::using('update_user,sanctum'), only: ['update, storeAvatar']),
            new Middleware(PermissionMiddleware::using('delete_user,sanctum'), only: ['destroy']),
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
            $users = User::query()
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                })
                ->with('roles');

            $users = $users->paginate($limit);

            return response()->json([
                'message' => __('responses.index.success', ['resource' => __('resources.user')]),
                'data' => $users,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('responses.index.failed', ['resource' => __('resources.user')]),
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
            'roles' => 'array|nullable',
            'phone' => 'string|nullable',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
        ]);

        try {
            $user = null;

            DB::transaction(function () use ($request, &$user) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'phone' => $request->phone,
                    'avatar' => $request->avatar ?? null,
                ]);

                if ($request->has('roles')) {
                    $user->assignRole($request->roles);
                }
            });

            return response()->json([
                'message' => __('responses.store.success', ['resource' => __('resources.user')]),
                'data' => $user,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('responses.store.failed', ['resource' => __('resources.user')]),
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
            $user = User::with('roles')->find($id);

            if (!$user) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            return response()->json([
                'message' => __('responses.show.success', ['resource' => __('resources.user')]),
                'data' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('responses.show.failed', ['resource' => __('resources.user')]),
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
            'email' => 'required|email|unique:users,email,' . $id,
            'roles' => 'array|nullable',
            'phone' => 'string|nullable',
        ]);

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            DB::transaction(function () use ($request, $user) {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                ]);

                if ($request->has('roles')) {
                    $user->syncRoles($request->roles);
                }
            });

            return response()->json([
                'message' => __('responses.update.success', ['resource' => __('resources.user')]),
                'data' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('responses.update.failed', ['resource' => __('resources.user')]),
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
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            DB::transaction(function () use ($user) {
                $user->delete();
            });

            return response()->json([
                'message' => __('responses.destroy.success', ['resource' => __('resources.user')]),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('responses.destroy.failed', ['resource' => __('resources.user')]),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function followers(string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            $followers = $user->followers;

            return response()->json([
                'message' => __('responses.index.success', ['resource' => __('resources.follower')]),
                'data' => $followers,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('responses.index.failed', ['resource' => __('resources.follower')]),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function following(string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            $following = $user->following;

            return response()->json([
                'message' => __('responses.index.success', ['resource' => __('resources.following')]),
                'data' => $following,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('responses.index.failed', ['resource' => __('resources.following')]),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function follow(Request $request, string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            $user->followers()->attach(Auth::id());

            return response()->json([
                'message' => __('responses.follow.success', ['resource' => __('resources.user')]),
                'data' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('responses.follow.failed', ['resource' => __('resources.user')]),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function unfollow(Request $request, string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            $user->followers()->detach(Auth::id());

            return response()->json([
                'message' => __('responses.unfollow.success', ['resource' => __('resources.user')]),
                'data' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('responses.unfollow.failed', ['resource' => __('resources.user')]),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function storeAvatar(Request $request, string $id)
    {
        $request->validate([
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
                $avatar->storeAs('avatars', $avatarName, 'public');

                $user->update([
                    'avatar' => 'storage/avatars/' . $avatarName,
                ]);
            }

            return response()->json([
                'message' => __('responses.upload.success', ['resource' => __('resources.avatar')]),
                'data' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('responses.upload.failed', ['resource' => __('resources.avatar')]),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function removeAvatar(string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            $user->update([
                'avatar' => null,
            ]);

            return response()->json([
                'message' => __('responses.remove.success', ['resource' => __('resources.avatar')]),
                'data' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('responses.remove.failed', ['resource' => __('resources.avatar')]),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function switchStatus(string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            $user->update([
                'is_active' => ! $user->is_active,
            ]);

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function export()
    {
        try {
            return Excel::download(new UserExport, 'users.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }
}