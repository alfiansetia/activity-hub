<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Display a listing of all notifications.
     */
    public function index()
    {
        $notifications = Notification::with('createdBy')
            ->latest()
            ->paginate(15);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * Store a newly created notification and broadcast to all users.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'type'    => 'required|in:info,warning,success,danger',
        ]);

        $validated['created_by'] = auth()->id();

        // Create the notification
        $notification = Notification::create($validated);

        // Broadcast to all users (exclude the admin who created it)
        $userIds = User::where('id', '!=', auth()->id())
            ->pluck('id')
            ->toArray();

        // Bulk insert pivot records
        $pivotData = [];
        $now = now();
        foreach ($userIds as $userId) {
            $pivotData[] = [
                'notification_id' => $notification->id,
                'user_id'         => $userId,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        if (!empty($pivotData)) {
            DB::table('notification_user')->insert($pivotData);
        }

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Notification sent to ' . count($userIds) . ' users successfully.');
    }

    /**
     * Remove the specified notification.
     */
    public function destroy(Notification $notification)
    {
        $notification->delete(); // Cascade deletes pivot records

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }
}
