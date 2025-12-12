<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">
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

    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Manage Students</h2>
                    <p class="text-muted">Select a course to manage enrolled students</p>
                </div>
                <a href="<?= site_url('teacher/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="courseSearch" placeholder="Search courses by title, code, or description...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses List -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">My Courses</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($courses)): ?>
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-book-open fa-3x mb-3"></i>
                                <h5>No Courses Assigned</h5>
                                <p>You have not been assigned to any courses yet. Please contact an administrator.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row" id="coursesContainer">
                            <?php foreach ($courses as $course): ?>
                                <div class="col-md-6 col-lg-4 mb-4 course-card"
                                     data-course-title="<?= esc(strtolower($course['title'] ?? '')) ?>"
                                     data-course-code="<?= esc(strtolower($course['course_code'] ?? '')) ?>"
                                     data-course-description="<?= esc(strtolower($course['description'] ?? '')) ?>">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body d-flex flex-column">
                                            <div class="mb-3">
                                                <h6 class="card-title text-primary">
                                                    <i class="fas fa-book me-2"></i>
                                                    <?= esc($course['course_code'] ?? 'N/A') ?>
                                                </h6>
                                                <h5 class="card-subtitle mb-2 text-muted">
                                                    <?= esc($course['title']) ?>
                                                </h5>
                                                <p class="card-text small text-muted mb-2">
                                                    <?= esc($course['description']) ?>
                                                </p>
                                                <div class="mb-2">
                                                    <span class="badge bg-info">
                                                        <?= esc($course['school_year'] ?? '') ?> - <?= esc($course['semester'] ?? '') ?>
                                                    </span>
                                                    <span class="badge bg-<?= $course['status'] === 'Active' ? 'success' : 'secondary' ?>">
                                                        <?= esc($course['status']) ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mt-auto">
                                                <div class="d-grid gap-2">
                                                    <a href="<?= site_url('teacher/manage-students-simple/' . $course['id']) ?>" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-users me-1"></i>Manage Students
                                                    </a>
                                                    <a href="<?= site_url('teacher/manage-students/' . $course['id']) ?>" class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-list me-1"></i>Advanced View
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- No Results Message -->
                        <div id="noResultsMessage" class="text-center py-5" style="display: none;">
                            <i class="fas fa-search fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No courses found matching your search.</p>
                            <button class="btn btn-outline-primary btn-sm" onclick="clearCourseSearch()">Clear Search</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
}

.card-body {
    border-radius: 10px;
}

.btn {
    border-radius: 6px;
}

.badge {
    font-size: 0.75em;
}
</style>

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

    // Clear search button
    $('#clearSearch').on('click', function() {
        clearCourseSearch();
    });
});

// Clear search function
function clearCourseSearch() {
    $('#courseSearch').val('');
    $('#courseSearch').trigger('keyup');
    $('#noResultsMessage').hide();
}
</script>

<?= $this->endSection() ?>
