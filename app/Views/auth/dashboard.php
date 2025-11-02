<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Dashboard</h2>
        <a href="<?= site_url('logout') ?>" class="btn btn-outline-danger">Logout</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php $role = strtolower((string) ($role ?? 'student')); ?>
    
    <!-- Welcome Message -->
    <div class="alert alert-info mb-4">
        <h4 class="alert-heading">Welcome, <?= esc($user_name ?? 'User') ?>!</h4>
        <p class="mb-0">You are logged in as a <strong><?= esc(ucfirst($role)) ?></strong>. This is your personalized dashboard.</p>
    </div>

    <?php if ($role === 'admin'): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">Admin Overview</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="p-3 bg-light border rounded">
                            <div class="text-muted">Total Users</div>
                            <div class="fs-4 fw-bold"><?= esc($totalUsers ?? 0) ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light border rounded">
                            <div class="text-muted">Admins</div>
                            <div class="fs-4 fw-bold"><?= esc($totalAdmins ?? 0) ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light border rounded">
                            <div class="text-muted">Teachers</div>
                            <div class="fs-4 fw-bold"><?= esc($totalTeachers ?? 0) ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light border rounded">
                            <div class="text-muted">Students</div>
                            <div class="fs-4 fw-bold"><?= esc($totalStudents ?? 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">Recent Users</div>
            <div class="card-body">
                <?php if (!empty($recentUsers)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentUsers as $u): ?>
                                    <tr>
                                        <td><?= esc($u['id']) ?></td>
                                        <td><?= esc($u['name']) ?></td>
                                        <td><?= esc($u['email']) ?></td>
                                        <td><?= esc($u['role']) ?></td>
                                        <td><?= esc($u['created_at'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="mb-0">No recent users to display.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif ($role === 'teacher'): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">My Courses</div>
            <div class="card-body">
                <?php if (!empty($courses)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $c): ?>
                                    <tr>
                                        <td><?= esc($c['id'] ?? '') ?></td>
                                        <td><?= esc($c['title'] ?? '') ?></td>
                                        <td><?= esc($c['description'] ?? '') ?></td>
                                        <td><?= esc($c['created_at'] ?? '') ?></td>
                                        <td>
                                            <a href="<?php echo base_url('admin/course/' . esc($c['id']) . '/upload'); ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-upload"></i> Upload Materials
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="mb-0">You have no courses yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">Recent Submissions</div>
            <div class="card-body">
                <?php if (!empty($notifications)): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($notifications as $n): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <?= esc($n['student_name'] ?? 'A student') ?>
                                    submitted work for course #<?= esc($n['course_id'] ?? '') ?>
                                </span>
                                <span class="text-muted small">
                                    <?= esc($n['created_at'] ?? '') ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="mb-0">No recent submissions.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif ($role === 'student'): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">My Enrolled Courses & Materials</div>
            <div class="card-body">
                <?php if (!empty($enrolledCourses)): ?>
                    <div class="row">
                        <?php foreach ($enrolledCourses as $course): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary"><?php echo esc($course['title']); ?></h5>
                                        <p class="card-text"><?php echo esc($course['description']); ?></p>
                                        <p class="text-muted small">Enrolled: <?php echo date('M j, Y', strtotime($course['created_at'])); ?></p>

                                        <h6 class="mt-3">Course Materials</h6>
                                        <?php if (!empty($course['materials'])): ?>
                                            <ul class="list-group list-group-flush">
                                                <?php foreach ($course['materials'] as $material): ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span><?php echo esc($material['file_name']); ?></span>
                                                        <a href="<?php echo base_url('materials/download/' . $material['id']); ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="text-muted small">No materials available yet.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="mb-0">You are not enrolled in any courses yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">Available Courses</div>
            <div class="card-body">
                <div id="available-courses">
                    <?php
                    try {
                        // Get available courses using EnrollmentModel
                        $enrollmentModel = new \App\Models\EnrollmentModel();
                        $availableCourses = $enrollmentModel->getAvailableCourses($user_id);

                        if (!empty($availableCourses)) {
                            echo '<div class="row g-3">';
                            foreach ($availableCourses as $course) {
                                echo '<div class="col-md-6 col-lg-4">';
                                echo '<div class="card h-100 border-0 shadow-sm">';
                                echo '<div class="card-body">';
                                echo '<h6 class="card-title text-primary">' . esc($course['title'] ?? 'Untitled Course') . '</h6>';
                                echo '<p class="card-text text-muted small">' . esc(substr($course['description'] ?? 'No description available', 0, 100));
                                if (strlen($course['description'] ?? '') > 100) {
                                    echo '...';
                                }
                                echo '</p>';
                                echo '<p class="card-text"><small class="text-muted">Teacher: ' . esc($course['teacher_name'] ?? 'Unknown') . '</small></p>';
                                echo '<button class="btn btn-success enroll-btn" data-course-id="' . esc($course['id']) . '">';
                                echo '<i class="fas fa-plus me-1"></i>Enroll</button>';
                                echo '</div></div></div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<div class="text-center py-4">';
                            echo '<i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>';
                            echo '<p class="mb-0 text-muted">No available courses to enroll in at the moment.</p>';
                            echo '</div>';
                        }
                    } catch (\Exception $e) {
                        // Fallback if EnrollmentModel fails
                        echo '<div class="text-center py-4">';
                        echo '<i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>';
                        echo '<p class="mb-0 text-muted">Unable to load courses at the moment.</p>';
                        echo '<small class="text-muted">Error: ' . esc($e->getMessage()) . '</small>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-secondary text-white">Upcoming Deadlines</div>
                    <div class="card-body">
                        <?php if (!empty($upcomingDeadlines)): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($upcomingDeadlines as $d): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold"><?= esc($d['title'] ?? 'Assignment') ?></div>
                                            <div class="text-muted small">Course: <?= esc($d['course_title'] ?? '') ?></div>
                                        </div>
                                        <span class="badge bg-dark"><?= esc($d['due_date'] ?? '') ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="mb-0">No upcoming deadlines.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-secondary text-white">Recent Grades</div>
                    <div class="card-body">
                        <?php if (!empty($recentGrades)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Assignment</th>
                                            <th>Course</th>
                                            <th>Score</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentGrades as $g): ?>
                                            <tr>
                                                <td><?= esc($g['assignment_title'] ?? '') ?></td>
                                                <td><?= esc($g['course_title'] ?? '') ?></td>
                                                <td><?= esc($g['score'] ?? '') ?></td>
                                                <td><?= esc($g['created_at'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="mb-0">No recent grades.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">This dashboard currently has custom content for admin, teacher, and student roles.</div>
    <?php endif; ?>
</div>

<!-- AJAX Enrollment Script -->
<?php if ($role === 'student'): ?>
<script>
// Wait for jQuery to be loaded
(function($) {
    $(document).ready(function() {
        console.log('jQuery loaded, setting up enrollment handlers...');

    // Handle enrollment button clicks
    $(document).on('click', '.enroll-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        console.log('Enroll button clicked');

        var button = $(this);
        var courseId = button.data('course-id');
        var originalText = button.html();

        console.log('Course ID:', courseId);

        if (!courseId) {
            console.error('No course ID found');
            return;
        }

        // Disable button and show loading state
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin me-1"></i>Enrolling...');

        console.log('Sending AJAX request to:', '<?= base_url('course/enroll') ?>');

        // Send AJAX request
        $.ajax({
            url: '<?= base_url('course/enroll') ?>',
            type: 'POST',
            data: {
                course_id: courseId
            },
            dataType: 'json',
            success: function(response) {
                console.log('AJAX success response:', response);

                if (response.success) {
                    // Show success message
                    showAlert(response.message, 'success');

                    // Update button to enrolled state
                    button.removeClass('btn-success').addClass('btn-secondary');
                    button.html('<i class="fas fa-check me-1"></i>Enrolled');
                    button.prop('disabled', true);

                    // Remove the course card from available courses
                    button.closest('.col-md-6').fadeOut(300, function() {
                        $(this).remove();
                        console.log('Course card removed');

                        // Check if no courses left
                        if ($('#available-courses .col-md-6').length === 0) {
                            $('#available-courses').html(`
                                <div class="text-center py-4">
                                    <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                    <p class="mb-0 text-muted">No available courses to enroll in at the moment.</p>
                                </div>
                            `);
                            console.log('No courses left message shown');
                        }
                    });

                    // Update enrolled courses section with data from enrollment response
                    updateEnrolledCoursesSection(response);
                } else {
                    // Show error message
                    showAlert(response.message, 'danger');

                    // Re-enable button
                    button.prop('disabled', false);
                    button.html(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', xhr, status, error);

                // Show error message
                showAlert('An error occurred while enrolling. Please try again.', 'danger');

                // Re-enable button
                button.prop('disabled', false);
                button.html(originalText);
            }
        });
    });

    function showAlert(message, type) {
        console.log('Showing alert:', message, type);

        var alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        // Remove existing alerts
        $('.alert:not(.alert-info)').remove();

        // Add new alert after the dashboard title
        $('.container .d-flex').after(alertHtml);

        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('.alert.alert-dismissible').fadeOut();
        }, 5000);
    }

    function updateEnrolledCoursesSection(response) {
        console.log('Updating enrolled courses section with response:', response);

        if (response.enrolled_courses && response.enrolled_courses.length > 0) {
            updateEnrolledCoursesDisplay(response.enrolled_courses);
            showAlert('Course enrolled successfully! Enrolled courses section updated.', 'success');
        } else {
            showAlert('Course enrolled but failed to update courses section. Please refresh the page.', 'warning');
        }
    }

    function updateEnrolledCoursesDisplay(courses) {
        console.log('Updating enrolled courses display with:', courses);

        var enrolledSection = $('.card-header:contains("My Enrolled Courses")').next('.card-body');

        if (courses.length > 0) {
            var tableHtml = `
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            courses.forEach(function(course) {
                tableHtml += `
                    <tr>
                        <td>${escapeHtml(course.id || '')}</td>
                        <td>${escapeHtml(course.title || '')}</td>
                        <td>${escapeHtml(course.description || '')}</td>
                        <td>${escapeHtml(course.created_at || '')}</td>
                    </tr>
                `;
            });

            tableHtml += `
                        </tbody>
                    </table>
                </div>
            `;

            enrolledSection.html(tableHtml);
        } else {
            enrolledSection.html('<p class="mb-0">You are not enrolled in any courses yet.</p>');
        }

        console.log('Enrolled courses section updated successfully');
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
})(jQuery);
</script>
<?php endif; ?>

<?= $this->endSection() ?>
