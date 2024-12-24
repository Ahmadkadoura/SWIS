<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
        $this->middleware(['permission:Admin']);
    }
    /**
     * Get all notifications for the authenticated admin.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('User not authenticated'),
            ], 401);
        }

        $notifications = $user->notifications;

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
        ]);
    }


    /**
     * Mark a specific notification as read.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => __('Notification marked as read'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('Notification not found'),
        ], 404);
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => __('All notifications marked as read'),
        ]);
    }
}
