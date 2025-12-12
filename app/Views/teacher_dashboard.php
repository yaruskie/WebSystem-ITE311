<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-12">
            <h2>Welcome, Teacher!</h2>
            <p>Welcome to your teacher dashboard, <?= esc($user_name) ?>!</p>
            
            <div class="alert alert-info">
                <h5>Teacher Dashboard</h5>
                <p>This is your dedicated teacher workspace. Here you can manage your courses, view student submissions, and access teaching tools.</p>
            </div>
            
            <!-- Your Courses Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="mb-0">Your Courses</h3>
                        <a href="<?= site_url('courses') ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-search me-1"></i>Browse All Courses
                        </a>
                    </div>

                    <?php if (empty($courses)): ?>
                        <div class="alert alert-info">
                            <p>You have not been assigned to any courses yet. Please contact an administrator.</p>
                        </div>
                    <?php else: ?>
                        <!-- Search Bar -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="courseSearch" placeholder="Search your courses...">
                                </div>
                            </div>
                        </div>

                        <div class="row" id="coursesContainer">
                            <?php foreach ($courses as $course): ?>
                                <div class="col-md-4 mb-3 course-card"
                                     data-course-title="<?= esc(strtolower($course['title'] ?? '')) ?>"
                                     data-course-code="<?= esc(strtolower($course['course_code'] ?? '')) ?>"
                                     data-course-description="<?= esc(strtolower($course['description'] ?? '')) ?>">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <?= esc($course['course_code'] ?? 'N/A') ?> - <?= esc($course['title']) ?>
                                            </h5>
                                            <p class="card-text">
                                                <?= esc($course['description']) ?><br>
                                                <small class="text-muted">
                                                    <?= esc($course['school_year'] ?? '') ?> - <?= esc($course['semester'] ?? '') ?><br>
                                                    Status: <span class="badge bg-<?= $course['status'] === 'Active' ? 'success' : 'secondary' ?>"><?= esc($course['status']) ?></span>
                                                </small>
                                            </p>
                                            <a href="<?= site_url('teacher/manage-students/' . $course['id']) ?>" class="btn btn-primary btn-sm">Manage Students</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- No Results Message -->
                        <div id="noResultsMessage" class="text-center py-4" style="display: none;">
                            <i class="fas fa-search fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No courses found matching your search.</p>
                            <button class="btn btn-outline-primary btn-sm" onclick="clearSearch()">Clear Search</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Quick Actions</h5>
                            <p class="card-text">Access your teaching tools and resources.</p>
                            <a href="#" class="btn btn-primary">Manage Courses</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Recent Activity</h5>
                            <p class="card-text">View your recent teaching activities and updates.</p>
                            <a href="#" class="btn btn-secondary">View Activity</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Search functionality for teacher's courses
    $('#courseSearch').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        const $courseCards = $('.course-card');
        let visibleCount = 0;

        if (searchTerm === '') {
            // Show all courses if search is empty
            $courseCards.show();
            $('#noResultsMessage').hide();
            return;
        }

        // Filter courses client-side
        $courseCards.each(function() {
            const $card = $(this);
            const title = $card.data('course-title') || '';
            const code = $card.data('course-code') || '';
            const description = $card.data('course-description') || '';

            if (title.includes(searchTerm) ||
                code.includes(searchTerm) ||
                description.includes(searchTerm)) {
                $card.show();
                visibleCount++;
            } else {
                $card.hide();
            }
        });

        // Show/hide no results message
        if (visibleCount === 0) {
            $('#noResultsMessage').show();
        } else {
            $('#noResultsMessage').hide();
        }
    });
});

// Clear search function
function clearSearch() {
    $('#courseSearch').val('');
    $('#courseSearch').trigger('keyup');
    $('#noResultsMessage').hide();
}
</script>

<?= $this->endSection() ?>
