<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notifications,
    ) {}

    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);

        return view('portal.notifikasi', compact('notifications'));
    }

    /**
     * Tandai dibaca lalu arahkan ke link notifikasi.
     */
    public function read(Request $request, $id)
    {
        $notif = $this->notifications->markAsRead((int) $id, $request->user()->id);

        return redirect($notif->link ?: route('portal.beranda'));
    }

    public function readAll(Request $request)
    {
        $this->notifications->markAllAsRead($request->user()->id);

        return back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }
}
