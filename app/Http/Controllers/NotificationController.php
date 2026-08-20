<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Fetch notifications for the authenticated user (AJAX).
     * Returns unread count and the last 10 notifications.
     */
    public function fetch(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = 10;
        $page = max(1, (int) $request->query('page', 1));

        $unreadCount = $user->unreadNotifications()->count();

        $paginator = $user->notifications()
            ->simplePaginate($perPage, ['*'], 'page', $page);

        $notifications = $paginator->getCollection()->map(function ($notification) {
            return [
                'id'         => $notification->id,
                'title'      => $notification->title,
                'message'    => $notification->message,
                'type'       => $notification->type,
                'type_color' => $notification->type_color,
                'type_icon'  => $notification->type_icon,
                'is_read'    => !is_null($notification->pivot->read_at),
                'time_ago'   => $notification->pivot->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
            'has_more'      => $paginator->hasMorePages(),
            'next_page'     => $paginator->hasMorePages() ? $paginator->currentPage() + 1 : null,
            'current_page'  => $paginator->currentPage(),
        ]);
    }

    /**
     * Show a single notification detail and auto mark as read (AJAX).
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $notification = $user->notifications()
            ->where('notification_id', $id)
            ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found.'], 404);
        }

        // Auto mark as read
        if (is_null($notification->pivot->read_at)) {
            $user->notifications()->updateExistingPivot($id, [
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'id'         => $notification->id,
            'title'      => $notification->title,
            'message'    => $notification->message,
            'type'       => $notification->type,
            'type_color' => $notification->type_color,
            'type_icon'  => $notification->type_icon,
            'sender'     => $notification->createdBy->name ?? 'System',
            'time_ago'   => $notification->pivot->created_at->diffForHumans(),
            'created_at' => $notification->pivot->created_at->format('d M Y H:i'),
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $user->notifications()->updateExistingPivot($id, [
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->unreadNotifications()->updateExistingPivot(
            $user->unreadNotifications()->pluck('notification_id')->toArray(),
            ['read_at' => now()]
        );

        return response()->json(['success' => true]);
    }
}
