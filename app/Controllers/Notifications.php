<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\RESTful\ResourceController;

class Notifications extends ResourceController
{
    protected $modelName = 'App\Models\NotificationModel';
    protected $format    = 'json';

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Returns JSON with unread count and list of notifications
     * GET /notifications
     */
    public function get()
    {
        if (!session('isLoggedIn')) {
            return $this->failUnauthorized('Not logged in');
        }

        $userId = session('user_id');
        $unreadCount = $this->notificationModel->getUnreadCount($userId);
        $notifications = $this->notificationModel->getNotificationsForUser($userId);

        return $this->respond([
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark notification as read
     * POST /notifications/mark_read/123
     */
    public function mark_as_read($id)
    {
        if (!session('isLoggedIn')) {
            return $this->failUnauthorized('Not logged in');
        }

        $id = (int) $id;
        $userId = (int) session('user_id');

        // Verify ownership
        $notification = $this->notificationModel->find($id);
        if (!$notification || $notification['user_id'] != $userId) {
            return $this->failNotFound('Notification not found');
        }

        if ($this->notificationModel->markAsRead($id)) {
            return $this->respond(['message' => 'Notification marked as read']);
        } else {
            return $this->fail('Failed to mark as read');
        }
    }
}
