<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->appNotifications()->orderByDesc('created_at')->paginate(30);

        return response()->json(['success' => true, 'data' => $notifications]);
    }

    public function readAll(Request $request)
    {
        $request->user()->appNotifications()->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Todas marcadas como lidas']);
    }
}
