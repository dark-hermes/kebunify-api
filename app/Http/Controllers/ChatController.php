<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $consultation_id)
    {
        $search = $request->input('search');

        try {
            $query = Chat::query();
            $query->where('consultation_id', $consultation_id);
            if ($search) {
                $query->where('message', 'LIKE', "%{$search}%");
            }
            $chats = $query->get();
            return response()->json([
                'message' => 'Chats retrieved',
                'data' => $chats
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $consultation_id)
    {
        try {
            $consultation = Consultation::find($consultation_id);
            if ($consultation->status === 'closed') {
                return response()->json(['message' => 'Consultation is closed'], 400);
            }
            if ($consultation->is_paid === false) {
                return response()->json(['message' => 'Finish payment first'], 400);
            }

            $chat = null;
            DB::transaction(function () use ($request, $consultation_id, &$chat) {
                $chat = Chat::create([
                    'message' => $request->message,
                    'user_id' => Auth::id(),
                    'consultation_id' => $consultation_id,
                ]);
            });
            return response()->json([
                'message' => 'Chat created',
                'data' => $chat
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $chat = Chat::find($id);
            if (!$chat) {
                return response()->json(['message' => 'Chat not found'], 404);
            }
            return response()->json([
                'message' => 'Chat retrieved',
                'data' => $chat
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        try {
            $chat = Chat::find($id);
            if (!$chat) {
                return response()->json(['message' => 'Chat not found'], 404);
            }
            DB::transaction(function () use ($request, $chat) {
                $chat->update($request->only(['message']));
            });

            return response()->json([
                'message' => 'Chat updated',
                'data' => $chat
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $chat = Chat::find($id);
            if (!$chat) {
                return response()->json(['message' => 'Chat not found'], 404);
            }
            DB::transaction(function () use ($chat) {
                $chat->delete();
            });
            return response()->json(['message' => 'Chat deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function unreadCount(string $consultation_id)
    {
        try {
            $unreadCount = Chat::where('consultation_id', $consultation_id)
                ->where('is_read', false)
                ->where('user_id', '!=', Auth::id())
                ->count();
            return response()->json(['unread_count' => $unreadCount], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function markAsRead(string $consultation_id)
    {
        try {
            $chats = Chat::where('consultation_id', $consultation_id)
                ->where('is_read', false)
                ->where('user_id', '!=', Auth::id())
                ->get();
            foreach ($chats as $chat) {
                DB::transaction(function () use ($chat) {
                    $chat->update(['is_read' => true]);
                });
            }
            return response()->json(['message' => 'Chats marked as read'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
