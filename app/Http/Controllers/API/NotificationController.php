<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('status', 'all');

        if (! in_array($filter, ['all', 'unread', 'read'], true)) {
            $filter = 'all';
        }

        $query = $request->user()->appNotifications()->orderByDesc('created_at');

        $unreadCount = (clone $query)->where('is_read', false)->count();

        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($filter === 'read') {
            $query->where('is_read', true);
        }

        $notifications = $query->paginate(30);

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $unreadCount,
            'filter' => $filter,
        ]);
    }

    public function read(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Notificação não encontrada.',
            ], 404);
        }

        $notification->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Notificação marcada como lida.',
            'url' => $notification->url(),
        ]);
    }

    public function readAll(Request $request)
    {
        $request->user()->appNotifications()->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Todas marcadas como lidas']);
    }
}
