<?php $isLoggedIn = (bool) session('isLoggedIn'); $role = strtolower((string) session('role')); ?>
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('/') ?>">MySite</a>
        <div class="d-flex align-items-center gap-2">
            <a class="btn btn-outline-light" href="<?= site_url('/') ?>">Home</a>
            <a class="btn btn-outline-light" href="<?= site_url('about') ?>">About</a>
            <a class="btn btn-outline-light" href="<?= site_url('contact') ?>">Contact</a>

            <?php if ($isLoggedIn): ?>
                <a class="btn btn-outline-light" href="<?= site_url('announcements') ?>">Announcements</a>
                <a class="btn btn-light" href="<?= site_url('dashboard') ?>">Dashboard</a>

                <?php if ($role === 'admin'): ?>
                    <a class="btn btn-success fw-bold" href="<?= site_url('admin/users') ?>">👥 Manage Users</a>
                    <a class="btn btn-info fw-bold" href="<?= site_url('admin/courses') ?>">📚 Manage Courses</a>
                <?php endif; ?>

                <?php $unreadCount = session('unread_notification_count') ?? 0; ?>
                <div class="dropdown">
                    <button class="btn btn-outline-success dropdown-toggle position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Notifications
                        <?php if ($unreadCount > 0): ?>
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle"><?php echo $unreadCount; ?></span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" id="notificationList">
                        <!-- Notifications will be populated here via AJAX -->
                    </ul>
                </div>

                <a class="btn btn-outline-danger" href="<?= site_url('logout') ?>">Logout</a>
            <?php else: ?>
                <a class="btn btn-outline-light" href="<?= site_url('login') ?>">Login</a>

            <?php endif; ?>
        </div>
    </div>
</nav>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Load notifications on page load
    loadNotifications();

    // Function to load notifications via AJAX
    function loadNotifications() {
        $.get('<?= site_url('notifications') ?>')
            .done(function(data) {
                $('#notificationList').empty();
                $('.badge').remove(); // Remove existing badge

                var badge = '';
                if (data.unread_count > 0) {
                    badge = '<span class="badge bg-danger position-absolute top-0 start-100 translate-middle">' + data.unread_count + '</span>';
                }
                $('#notificationDropdown').append(badge);

                if (data.notifications.length === 0) {
                    $('#notificationList').append('<li class="dropdown-item">No notifications</li>');
                } else {
                    data.notifications.forEach(function(notification) {
                        var readClass = notification.is_read == 1 ? 'alert-secondary' : 'alert-info';
                        var item = '<li class="p-2">' +
                            '<div class="alert ' + readClass + ' mb-1 small">' + notification.message + '</div>' +
                            '<button class="btn btn-sm btn-success mark-read-btn" data-id="' + notification.id + '">Mark as Read</button>' +
                            '</li>';
                        $('#notificationList').append(item);
                    });
                }
            });
    }

    // Handle mark as read
    $(document).on('click', '.mark-read-btn', function() {
        var notificationId = $(this).data('id');
        var $btn = $(this);
        $.post('<?= site_url('notifications/mark_read') ?>/' + notificationId)
            .done(function() {
                $btn.closest('li').remove();
                // Update badge count
                var currentCount = parseInt($('.badge').text()) || 0;
                if (currentCount > 0) {
                    $('.badge').text(currentCount - 1);
                    if (currentCount - 1 <= 0) {
                        $('.badge').remove();
                    }
                }
            });
    });

    // Load notifications every 10 seconds
    setInterval(loadNotifications, 10000);
});
</script>
