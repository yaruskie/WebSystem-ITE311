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

    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-3">Course Management</h2>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-1">Total Courses</h5>
                            <h3 class="mb-0 text-primary"><?= $totalCourses ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-1">Active Courses</h5>
                            <h3 class="mb-0 text-success"><?= $activeCourses ?></h3>
                        </div>
                    </div>
                </div>
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
                        <input type="text" class="form-control" id="courseSearch" placeholder="Search by title, course code, or teacher...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">All Courses</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="coursesTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">Course Code</th>
                                    <th class="border-0">Course Title</th>
                                    <th class="border-0">Description</th>
                                    <th class="border-0">School Year</th>
                                    <th class="border-0">Semester</th>
                                    <th class="border-0">Schedule</th>
                                    <th class="border-0">Teacher</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                <tr data-course-id="<?= esc($course['id']) ?>">
                                    <td class="align-middle"><?= esc($course['course_code'] ?? '') ?></td>
                                    <td class="align-middle">
                                        <strong><?= esc($course['title']) ?></strong>
                                    </td>
                                    <td class="align-middle">
                                        <span title="<?= esc($course['description']) ?>">
                                            <?= esc(strlen($course['description']) > 50 ? substr($course['description'], 0, 50) . '...' : $course['description']) ?>
                                        </span>
                                    </td>
                                    <td class="align-middle"><?= esc($course['school_year'] ?? '') ?></td>
                                    <td class="align-middle"><?= esc($course['semester'] ?? '') ?></td>
                                    <td class="align-middle"><?= esc($course['schedule'] ?? '') ?></td>
                                    <td class="align-middle"><?= esc($course['teacher_name'] ?? 'Unknown') ?></td>
                                    <td class="align-middle">
                                        <select class="form-select form-select-sm status-select" style="width:120px;" data-course-id="<?= esc($course['id']) ?>">
                                            <option value="Active" <?= ($course['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
                                            <option value="Inactive" <?= ($course['status'] ?? 'Active') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <button class="btn btn-sm btn-outline-primary edit-course-btn"
                                                data-course-id="<?= esc($course['id']) ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editCourseModal">
                                            Edit Details
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCourseModalLabel">Edit Course Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCourseForm">
                <div class="modal-body">
                    <input type="hidden" id="editCourseId" name="course_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editCourseCode" class="form-label">Course Code</label>
                            <input type="text" class="form-control" id="editCourseCode" name="course_code">
                        </div>
                        <div class="col-md-6">
                            <label for="editSchoolYear" class="form-label">School Year</label>
                            <input type="text" class="form-control" id="editSchoolYear" name="school_year" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editSemester" class="form-label">Semester</label>
                            <select class="form-select" id="editSemester" name="semester" required>
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editStartDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="editStartDate" name="start_date">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editEndDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="editEndDate" name="end_date">
                        </div>
                        <div class="col-md-6">
                            <label for="editTeacher" class="form-label">Teacher</label>
                            <select class="form-select" id="editTeacher" name="teacher_id" required>
                                <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= esc($teacher['id']) ?>"><?= esc($teacher['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Course Title</label>
                        <input type="text" class="form-control" id="editTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editSchedule" class="form-label">Schedule</label>
                            <input type="text" class="form-control" id="editSchedule" name="schedule">
                        </div>
                        <div class="col-md-6">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Search functionality
    $('#courseSearch').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#coursesTable tbody tr').each(function() {
            const row = $(this);
            const courseCode = row.find('td:nth-child(1)').text().toLowerCase();
            const title = row.find('td:nth-child(2)').text().toLowerCase();
            const teacher = row.find('td:nth-child(7)').text().toLowerCase();

            if (courseCode.includes(searchTerm) ||
                title.includes(searchTerm) ||
                teacher.includes(searchTerm)) {
                row.show();
            } else {
                row.hide();
            }
        });
    });

    // Status change
    $('.status-select').on('change', function() {
        const select = $(this);
        const courseId = select.data('course-id');
        const status = select.val();

        $.post('<?= site_url('admin/updateCourseStatus') ?>', {
            course_id: courseId,
            status: status
        })
        .done(function(response) {
            if (response.success) {
                // Optional: Show success message
            } else {
                alert(response.message || 'Failed to update status');
                // Revert selection
                select.val(select.data('original-value'));
            }
        })
        .fail(function() {
            alert('An error occurred while updating the status');
            // Revert selection
            select.val(select.data('original-value'));
        });
    });

    // Store original values for status selects
    $('.status-select').each(function() {
        $(this).data('original-value', $(this).val());
    });

    // Edit course button click
    $('.edit-course-btn').on('click', function() {
        const courseId = $(this).data('course-id');
        const row = $(this).closest('tr');

        // Populate modal with current data
        $('#editCourseId').val(courseId);
        $('#editCourseCode').val(row.find('td:nth-child(1)').text().trim());
        $('#editTitle').val(row.find('td:nth-child(2) strong').text().trim());
        $('#editDescription').val(row.attr('title') || row.find('td:nth-child(3) span').attr('title') || '');
        $('#editSchoolYear').val(row.find('td:nth-child(4)').text().trim());
        $('#editSemester').val(row.find('td:nth-child(5)').text().trim());
        $('#editSchedule').val(row.find('td:nth-child(6)').text().trim());
        $('#editStatus').val(row.find('.status-select').val());

        // Find teacher by name
        const teacherName = row.find('td:nth-child(7)').text().trim();
        $('#editTeacher option').each(function() {
            if ($(this).text() === teacherName) {
                $(this).prop('selected', true);
                return false;
            }
        });
    });

    // Form submission
    $('#editCourseForm').on('submit', function(e) {
        e.preventDefault();

        const startDate = $('#editStartDate').val();
        const endDate = $('#editEndDate').val();

        // Validate dates
        if (startDate && endDate && new Date(startDate) >= new Date(endDate)) {
            alert('Start date must be before end date');
            return;
        }

        const formData = new FormData(this);

        $.ajax({
            url: '<?= site_url('admin/updateCourse') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editCourseModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message || 'Failed to update course');
                }
            },
            error: function() {
                alert('An error occurred while updating the course');
            }
        });
    });
});
</script>

<style>
.card {
    border: none;
    border-radius: 10px;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
}

.btn-outline-primary {
    border-color: #0d6efd;
    color: #0d6efd;
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.status-select {
    font-size: 0.875rem;
}

.modal-content {
    border-radius: 10px;
}

.form-label {
    font-weight: 500;
}
</style>

<?= $this->endSection() ?>
