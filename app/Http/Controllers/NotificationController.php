<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Lista notificações in-app.
     */
    public function index()
    {
        $notifications = Auth::user()->appNotifications()
            ->orderByDesc('created_at')->paginate(30);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Marca uma notificação como lida.
     */
    public function read(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true, 'read_at' => now()]);

        return back();
    }

    /**
     * Marca todas como lidas.
     */
    public function readAll()
    {
        Auth::user()->appNotifications()
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'Todas as notificações marcadas como lidas.');
    }
}
