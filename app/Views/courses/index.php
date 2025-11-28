<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Courses</h2>
        <div>
            <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
            </a>
            <a href="<?= site_url('logout') ?>" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Search Form -->
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <form id="searchForm" class="d-flex">
                <div class="input-group">
                    <input 
                        type="text" 
                        id="searchInput" 
                        class="form-control form-control-lg" 
                        placeholder="Search courses by title or description..." 
                        name="search_term"
                        value="<?= esc($searchTerm ?? '') ?>"
                        autocomplete="off"
                    >
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                        <i class="fas fa-times me-1"></i>Clear
                    </button>
                </div>
            </form>
            <small class="text-muted mt-2 d-block">
                <i class="fas fa-info-circle me-1"></i>
                Type to filter instantly or click Search for server-side search
            </small>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Searching courses...</p>
    </div>

    <!-- Courses Container -->
    <div id="coursesContainer" class="row">
        <?php if (!empty($courses)): ?>
            <?php foreach ($courses as $course): ?>
                <div class="col-md-4 mb-4 course-card" 
                     data-course-title="<?= esc(strtolower($course['title'] ?? '')) ?>"
                     data-course-description="<?= esc(strtolower($course['description'] ?? '')) ?>">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary">
                                <?= esc($course['title'] ?? 'Untitled Course') ?>
                            </h5>
                            <p class="card-text text-muted">
                                <?php 
                                $description = $course['description'] ?? 'No description available';
                                echo esc(strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description);
                                ?>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-user-tie me-1"></i>
                                    Teacher: <?= esc($course['teacher_name'] ?? 'Unknown') ?>
                                </small>
                            </p>
                            <div class="d-flex gap-2">
                                <a href="<?= site_url('courses/view/' . $course['id']) ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>View Course
                                </a>
                                <?php if (!($course['is_enrolled'] ?? false)): ?>
                                    <button class="btn btn-success btn-sm enroll-btn" data-course-id="<?= esc($course['id']) ?>">
                                        <i class="fas fa-plus me-1"></i>Enroll
                                    </button>
                                <?php else: ?>
                                    <span class="badge bg-info">
                                        <i class="fas fa-check me-1"></i>Enrolled
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No courses found.</p>
                    <?php if (!empty($searchTerm ?? '')): ?>
                        <p class="text-muted">Try a different search term.</p>
                        <a href="<?= site_url('courses') ?>" class="btn btn-outline-primary">View All Courses</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- No Results Message (hidden by default) -->
    <div id="noResultsMessage" class="text-center py-5" style="display: none;">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <p class="text-muted">No courses found matching your search.</p>
        <button class="btn btn-outline-primary" onclick="clearSearch()">Clear Search</button>
    </div>
</div>

<script>
(function($) {
    'use strict';

    $(document).ready(function() {
        let searchTimeout;
        let isServerSearch = false;

        // Client-side filtering (instant feedback)
        $('#searchInput').on('keyup', function() {
            const searchValue = $(this).val().toLowerCase().trim();
            const $courseCards = $('.course-card');
            let visibleCount = 0;

            // Clear any existing timeout
            clearTimeout(searchTimeout);

            // Debounce the search for better performance
            searchTimeout = setTimeout(function() {
                if (searchValue === '') {
                    // Show all courses if search is empty
                    $courseCards.show();
                    $('#noResultsMessage').hide();
                    return;
                }

                // Filter courses client-side
                $courseCards.each(function() {
                    const $card = $(this);
                    const title = $card.data('course-title') || '';
                    const description = $card.data('course-description') || '';
                    
                    if (title.includes(searchValue) || description.includes(searchValue)) {
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
            }, 300); // 300ms debounce
        });

        // Server-side search with AJAX
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            isServerSearch = true;

            const searchTerm = $('#searchInput').val().trim();
            const $loadingIndicator = $('#loadingIndicator');
            const $coursesContainer = $('#coursesContainer');
            const $noResultsMessage = $('#noResultsMessage');

            // Show loading indicator
            $loadingIndicator.show();
            $coursesContainer.hide();
            $noResultsMessage.hide();

            // Make AJAX request
            $.ajax({
                url: '<?= site_url('courses/search') ?>',
                method: 'GET',
                data: { search_term: searchTerm },
                dataType: 'json',
                success: function(response) {
                    $loadingIndicator.hide();

                    if (response.success && response.courses && response.courses.length > 0) {
                        // Build HTML for courses
                        let coursesHtml = '<div class="row">';
                        
                        $.each(response.courses, function(index, course) {
                            const description = course.description || 'No description available';
                            const truncatedDesc = description.length > 100 
                                ? description.substring(0, 100) + '...' 
                                : description;
                            
                            const enrollButton = course.is_enrolled 
                                ? '<span class="badge bg-info"><i class="fas fa-check me-1"></i>Enrolled</span>'
                                : '<button class="btn btn-success btn-sm enroll-btn" data-course-id="' + escapeHtml(course.id) + '"><i class="fas fa-plus me-1"></i>Enroll</button>';

                            coursesHtml += `
                                <div class="col-md-4 mb-4 course-card" 
                                     data-course-title="${escapeHtml(course.title || '').toLowerCase()}"
                                     data-course-description="${escapeHtml(description).toLowerCase()}">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title text-primary">${escapeHtml(course.title || 'Untitled Course')}</h5>
                                            <p class="card-text text-muted">${escapeHtml(truncatedDesc)}</p>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-user-tie me-1"></i>
                                                    Teacher: ${escapeHtml(course.teacher_name || 'Unknown')}
                                                </small>
                                            </p>
                                            <div class="d-flex gap-2">
                                                <a href="<?= site_url('courses/view/') ?>${course.id}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>View Course
                                                </a>
                                                ${enrollButton}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        coursesHtml += '</div>';
                        $coursesContainer.html(coursesHtml);
                        $coursesContainer.show();
                        $noResultsMessage.hide();

                        // Re-attach enroll button handlers
                        attachEnrollHandlers();
                    } else {
                        $coursesContainer.hide();
                        $noResultsMessage.show();
                    }
                },
                error: function(xhr, status, error) {
                    $loadingIndicator.hide();
                    console.error('Search error:', error);
                    
                    // Show error message
                    const errorHtml = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            An error occurred while searching. Please try again.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    $coursesContainer.before(errorHtml);
                    $coursesContainer.show();
                }
            });
        });

        // Clear search button
        $('#clearSearch').on('click', function() {
            clearSearch();
        });

        function clearSearch() {
            $('#searchInput').val('');
            $('#searchInput').trigger('keyup');
            $('#noResultsMessage').hide();
            
            // Reload page to show all courses
            if (isServerSearch) {
                window.location.href = '<?= site_url('courses') ?>';
            }
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Attach enroll button handlers
        function attachEnrollHandlers() {
            $('.enroll-btn').off('click').on('click', function() {
                const courseId = $(this).data('course-id');
                const $btn = $(this);

                if (!courseId) {
                    alert('Invalid course ID');
                    return;
                }

                // Disable button during request
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Enrolling...');

                $.ajax({
                    url: '<?= site_url('course/enroll') ?>',
                    method: 'POST',
                    data: { course_id: courseId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Replace button with enrolled badge
                            $btn.replaceWith(
                                '<span class="badge bg-info"><i class="fas fa-check me-1"></i>Enrolled</span>'
                            );
                            
                            // Show success message
                            showAlert('Successfully enrolled in course!', 'success');
                        } else {
                            $btn.prop('disabled', false).html('<i class="fas fa-plus me-1"></i>Enroll');
                            showAlert(response.message || 'Failed to enroll in course', 'danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        $btn.prop('disabled', false).html('<i class="fas fa-plus me-1"></i>Enroll');
                        showAlert('An error occurred. Please try again.', 'danger');
                        console.error('Enrollment error:', error);
                    }
                });
            });
        }

        // Show alert message
        function showAlert(message, type) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${escapeHtml(message)}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Remove existing alerts
            $('.alert:not(.alert-info)').remove();
            
            // Add new alert
            $('.container').first().prepend(alertHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $('.alert.alert-dismissible').fadeOut();
            }, 5000);
        }

        // Initialize enroll handlers for existing buttons
        attachEnrollHandlers();
    });
})(jQuery);
</script>

<?= $this->endSection() ?>

