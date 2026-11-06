<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Lista notificações in-app (filtro: all | unread | read).
     */
    public function index(Request $request)
    {
        $filter = $request->get('status', 'all');

        if (! in_array($filter, ['all', 'unread', 'read'], true)) {
            $filter = 'all';
        }

        $query = Auth::user()->appNotifications()->orderByDesc('created_at');

        $unreadCount = (clone $query)->where('is_read', false)->count();

        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($filter === 'read') {
            $query->where('is_read', true);
        }

        $notifications = $query->paginate(20)->withQueryString();

        return view('notifications.index', compact('notifications', 'filter', 'unreadCount'));
    }

    /**
     * Marca uma notificação como lida.
     *
     * Por padrão, redireciona para o destino da notificação (quando há).
     * Com action=stay, apenas marca e permanece na lista.
     */
    public function read(Request $request, Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true, 'read_at' => now()]);

        if ($request->input('action') === 'stay') {
            return redirect()->route('notifications.index');
        }

        $url = $notification->url();

        return $url ? redirect($url) : back();
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
