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
                    <h2>Manage Students (Simple View)</h2>
                    <h4 class="text-muted">Course: <?= esc($course['course_code'] ?? 'N/A') ?> – <?= esc($course['title']) ?></h4>
                </div>
                <a href="<?= site_url('teacher/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Student List Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Enrolled Students</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="studentsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">Student ID</th>
                                    <th class="border-0">Name</th>
                                    <th class="border-0">Email</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                                <!-- Students will be loaded here via AJAX -->
                            </tbody>
                            <tbody id="actionButtons" style="display: none;">
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <button class="btn btn-success me-2" id="activateAllBtn">Activate All</button>
                                        <button class="btn btn-secondary me-2" id="deactivateAllBtn">Deactivate All</button>
                                        <button class="btn btn-danger" id="removeAllBtn">Remove All</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center p-4" id="loadingSpinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="text-center p-4 d-none" id="noStudentsMessage">
                        <p class="text-muted">No students enrolled in this course yet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
$(document).ready(function() {
    const courseId = <?= $course['id'] ?>;
    let studentsData = [];

    // Load students
    loadStudents();

    function loadStudents() {
        $('#loadingSpinner').show();
        $('#noStudentsMessage').addClass('d-none');

        $.get('<?= site_url('teacher/get-students/') ?>' + courseId)
            .done(function(response) {
                $('#loadingSpinner').hide();

                if (response.error) {
                    alert(response.error);
                    return;
                }

                studentsData = response;
                renderStudents(studentsData);
            })
            .fail(function() {
                $('#loadingSpinner').hide();
                alert('Failed to load students');
            });
    }

    function renderStudents(students) {
        const tbody = $('#studentsTableBody');
        tbody.empty();

        if (students.length === 0) {
            $('#noStudentsMessage').removeClass('d-none');
            return;
        }

        $('#noStudentsMessage').addClass('d-none');

        students.forEach(function(student) {
            const statusBadge = student.user_status === 'active'
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-secondary">Inactive</span>';

            const row = `
                <tr data-student-id="${student.user_id}">
                    <td class="align-middle">${student.user_id}</td>
                    <td class="align-middle">${student.name}</td>
                    <td class="align-middle">${student.email}</td>
                    <td class="align-middle">${statusBadge}</td>
                    <td class="align-middle">
                        ${student.user_status === 'active'
                            ? `<button class="btn btn-sm btn-outline-secondary deactivate-btn" data-student-id="${student.user_id}">
                                Deactivate
                              </button>`
                            : `<button class="btn btn-sm btn-outline-success activate-btn" data-student-id="${student.user_id}">
                                Activate
                              </button>`
                        }
                        <button class="btn btn-sm btn-outline-danger remove-student-btn" data-student-id="${student.user_id}">
                            Remove
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // Activate button
    $(document).on('click', '.activate-btn', function() {
        const studentId = $(this).data('student-id');
        const student = studentsData.find(s => s.user_id == studentId);

        if (student && confirm(`Are you sure you want to activate ${student.name}?`)) {
            updateStudentStatus(studentId, 'active');
        }
    });

    // Deactivate button
    $(document).on('click', '.deactivate-btn', function() {
        const studentId = $(this).data('student-id');
        const student = studentsData.find(s => s.user_id == studentId);

        if (student && confirm(`Are you sure you want to deactivate ${student.name}?`)) {
            updateStudentStatus(studentId, 'inactive');
        }
    });

    function updateStudentStatus(userId, status) {
        const formData = {
            user_id: userId,
            course_id: courseId,
            status: status,
            remarks: ''
        };

        $.ajax({
            url: '<?= site_url('teacher/update-student-status') ?>',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                if (response.success) {
                    loadStudents(); // Reload the table
                    alert(response.message);
                } else {
                    alert(response.message || 'Failed to update status');
                }
            },
            error: function() {
                alert('An error occurred while updating the status');
            }
        });
    }

    // Remove student button
    $(document).on('click', '.remove-student-btn', function() {
        const studentId = $(this).data('student-id');
        const student = studentsData.find(s => s.user_id == studentId);

        if (student && confirm(`Are you sure you want to remove ${student.name} from this course?`)) {
            $.ajax({
                url: '<?= site_url('teacher/remove-student') ?>',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    user_id: studentId,
                    course_id: courseId
                }),
                success: function(response) {
                    if (response.success) {
                        loadStudents(); // Reload the table
                        alert(response.message);
                    } else {
                        alert(response.message || 'Failed to remove student');
                    }
                },
                error: function() {
                    alert('An error occurred while removing the student');
                }
            });
        }
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

.btn-outline-danger {
    border-color: #dc3545;
    color: #dc3545;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}
</style>

<?= $this->endSection() ?>
