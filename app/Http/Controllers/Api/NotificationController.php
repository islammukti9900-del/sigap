<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notifications,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);

        return response()->json([
            'success' => true,
            'Message' => 'Berhasil mengambil notifikasi',
            'data' => $notifications,
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        try {
            $notif = $this->notifications->markAsRead((int) $id, $request->user()->id);

            return response()->json([
                'success' => true,
                'Message' => 'Notifikasi ditandai dibaca',
                'data' => $notif,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Notifikasi tidak ditemukan',
            ], 404);
        }
    }

    public function markAllAsRead(Request $request)
    {
        $this->notifications->markAllAsRead($request->user()->id);

        return response()->json([
            'success' => true,
            'Message' => 'Semua notifikasi ditandai dibaca',
        ]);
    }
}
