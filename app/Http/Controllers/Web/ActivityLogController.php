<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

/**
 * Log aktivitas (audit trail) — khusus admin.
 * Filter: semua aksi, per kelompok aksi, atau khusus keamanan.
 */
class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', '');

        $logs = ActivityLog::with('user:id,name,role')
            ->when($filter === 'keamanan', fn ($q) => $q->where('action', 'like', 'keamanan.%'))
            ->when($filter !== '' && $filter !== 'keamanan', fn ($q) => $q->where('action', $filter))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $aksiTersedia = ActivityLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('aktivitas.index', compact('logs', 'filter', 'aksiTersedia'));
    }
}
