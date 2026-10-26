<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function notify(int $userId, string $judul, string $pesan, ?string $link = null, string $icon = 'bell'): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'link' => $link,
            'icon' => $icon,
        ]);
    }

    public function unreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)->unread()->count();
    }

    public function latest(int $userId, int $limit = 8)
    {
        return Notification::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function markAsRead(int $id, int $userId): ?Notification
    {
        $notif = Notification::where('id', $id)->where('user_id', $userId)->firstOrFail();
        $notif->update(['dibaca' => true]);

        return $notif;
    }

    public function markAllAsRead(int $userId): void
    {
        Notification::where('user_id', $userId)->unread()->update(['dibaca' => true]);
    }
}
