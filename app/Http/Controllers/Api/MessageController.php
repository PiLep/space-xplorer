<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteMessageRequest;
use App\Http\Requests\MarkMessageReadRequest;
use App\Http\Requests\MarkMessageUnreadRequest;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Get paginated list of messages for the authenticated user.
     *
     * Query parameters:
     * - filter: 'all', 'unread', 'read' (default: 'all')
     * - type: message type filter (optional)
     * - page: page number for pagination (default: 1)
     */
    public function index(Request $request, MessageService $messageService): JsonResponse
    {
        $filter = $request->query('filter', 'all');
        $type = $request->query('type');
        $perPage = 20;

        $messages = $messageService->getMessagesForUser(
            Auth::user(),
            $filter,
            $type,
            $perPage
        );

        return response()->json([
            'data' => [
                'messages' => $messages->items(),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                ],
            ],
            'status' => 'success',
        ]);
    }

    /**
     * Get a specific message by ID.
     *
     * Automatically marks the message as read when opened.
     */
    public function show(string $id): JsonResponse
    {
        // Use scope forUser() to ensure security - user can only access their own messages
        $message = Message::forUser(Auth::user())->findOrFail($id);

        // Mark as read when opened
        if (! $message->is_read) {
            $message->markAsRead();
        }

        return response()->json([
            'data' => [
                'message' => [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'recipient_id' => $message->recipient_id,
                    'type' => $message->type,
                    'subject' => $message->subject,
                    'content' => $message->content,
                    'is_read' => $message->is_read,
                    'read_at' => $message->read_at?->toIso8601String(),
                    'is_important' => $message->is_important,
                    'metadata' => $message->metadata,
                    'created_at' => $message->created_at->toIso8601String(),
                ],
            ],
            'status' => 'success',
        ]);
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(MarkMessageReadRequest $request, string $id): JsonResponse
    {
        // Use scope forUser() to ensure security
        $message = Message::forUser(Auth::user())->findOrFail($id);

        $message->markAsRead();

        return response()->json([
            'data' => [
                'message' => [
                    'id' => $message->id,
                    'is_read' => $message->is_read,
                    'read_at' => $message->read_at?->toIso8601String(),
                ],
            ],
            'message' => 'Message marked as read',
            'status' => 'success',
        ]);
    }

    /**
     * Mark a message as unread.
     */
    public function markAsUnread(MarkMessageUnreadRequest $request, string $id): JsonResponse
    {
        // Use scope forUser() to ensure security
        $message = Message::forUser(Auth::user())->findOrFail($id);

        $message->markAsUnread();

        return response()->json([
            'data' => [
                'message' => [
                    'id' => $message->id,
                    'is_read' => $message->is_read,
                    'read_at' => $message->read_at?->toIso8601String(),
                ],
            ],
            'message' => 'Message marked as unread',
            'status' => 'success',
        ]);
    }

    /**
     * Delete a message.
     */
    public function destroy(DeleteMessageRequest $request, string $id): JsonResponse
    {
        // Use scope forUser() to ensure security
        $message = Message::forUser(Auth::user())->findOrFail($id);

        $message->forceDelete();

        return response()->json([
            'message' => 'Message deleted successfully',
            'status' => 'success',
        ]);
    }
}
