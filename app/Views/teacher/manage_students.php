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
                    <h4 class="text-muted">Course: <?= esc($course['course_code'] ?? 'N/A') ?> – <?= esc($course['title']) ?></h4>
                </div>
                <a href="<?= site_url('teacher/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by name, ID, or email...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="yearLevelFilter">
                                <option value="">All Year Levels</option>
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="programFilter">
                                <option value="">All Programs</option>
                                <option value="BSIT">BSIT</option>
                                <option value="BSCS">BSCS</option>
                                <option value="BSIS">BSIS</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" id="clearFilters">Clear Filters</button>
                        </div>
                    </div>
                </div>
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
                                    <th class="border-0">Program</th>
                                    <th class="border-0">Year Level</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                                <!-- Students will be loaded here via AJAX -->
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

<!-- Student Details Modal -->
<div class="modal fade" id="studentDetailsModal" tabindex="-1" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentDetailsModalLabel">Student Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Student Information</h6>
                        <p><strong>Student ID:</strong> <span id="detailStudentId"></span></p>
                        <p><strong>Full Name:</strong> <span id="detailFullName"></span></p>
                        <p><strong>Email:</strong> <span id="detailEmail"></span></p>
                        <p><strong>Program/Major:</strong> <span id="detailProgram"></span></p>
                        <p><strong>Year Level:</strong> <span id="detailYearLevel"></span></p>
                        <p><strong>Section:</strong> <span id="detailSection"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Enrollment Information</h6>
                        <p><strong>Enrollment Date:</strong> <span id="detailEnrollmentDate"></span></p>
                        <p><strong>Status:</strong> <span id="detailStatus" class="badge"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusUpdateModalLabel">Update Student Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusUpdateForm">
                <div class="modal-body">
                    <input type="hidden" id="statusUserId" name="user_id">
                    <input type="hidden" id="statusCourseId" name="course_id">

                    <div class="mb-3">
                        <label for="currentStatus" class="form-label">Current Status</label>
                        <input type="text" class="form-control" id="currentStatus" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="newStatus" class="form-label">New Status</label>
                        <select class="form-select" id="newStatus" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="statusRemarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="statusRemarks" name="remarks" rows="3" placeholder="Optional remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
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
                    <td class="align-middle">-</td>
                    <td class="align-middle">-</td>
                    <td class="align-middle">${statusBadge}</td>
                    <td class="align-middle">
                        <button class="btn btn-sm btn-outline-info view-details-btn" data-student-id="${student.user_id}">
                            View Details
                        </button>
                        <button class="btn btn-sm btn-outline-warning update-status-btn" data-student-id="${student.user_id}">
                            Update Status
                        </button>
                        <button class="btn btn-sm btn-outline-danger remove-student-btn" data-student-id="${student.user_id}">
                            Remove
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // Search functionality
    $('#searchInput').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        filterStudents();
    });

    // Filter functionality
    $('#yearLevelFilter, #statusFilter, #programFilter').on('change', filterStudents);

    $('#clearFilters').on('click', function() {
        $('#searchInput').val('');
        $('#yearLevelFilter').val('');
        $('#statusFilter').val('');
        $('#programFilter').val('');
        renderStudents(studentsData);
    });

    function filterStudents() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const yearLevel = $('#yearLevelFilter').val();
        const status = $('#statusFilter').val();
        const program = $('#programFilter').val();

        const filtered = studentsData.filter(function(student) {
            const matchesSearch = !searchTerm ||
                student.name.toLowerCase().includes(searchTerm) ||
                student.email.toLowerCase().includes(searchTerm) ||
                student.user_id.toString().includes(searchTerm);

            const matchesYear = !yearLevel || true; // Placeholder
            const matchesStatus = !status || student.user_status === status;
            const matchesProgram = !program || true; // Placeholder

            return matchesSearch && matchesYear && matchesStatus && matchesProgram;
        });

        renderStudents(filtered);
    }

    // View details button
    $(document).on('click', '.view-details-btn', function() {
        const studentId = $(this).data('student-id');
        const student = studentsData.find(s => s.user_id == studentId);

        if (student) {
            $('#detailStudentId').text(student.user_id);
            $('#detailFullName').text(student.name);
            $('#detailEmail').text(student.email);
            $('#detailProgram').text('-'); // Placeholder
            $('#detailYearLevel').text('-'); // Placeholder
            $('#detailSection').text('-'); // Placeholder
            $('#detailEnrollmentDate').text(student.enrollment_date);
            $('#detailStatus').removeClass('bg-success bg-secondary').addClass(student.user_status === 'active' ? 'bg-success' : 'bg-secondary').text(student.user_status === 'active' ? 'Active' : 'Inactive');

            $('#studentDetailsModal').modal('show');
        }
    });

    // Update status button
    $(document).on('click', '.update-status-btn', function() {
        const studentId = $(this).data('student-id');
        const student = studentsData.find(s => s.user_id == studentId);

        if (student) {
            $('#statusUserId').val(student.user_id);
            $('#statusCourseId').val(courseId);
            $('#currentStatus').val(student.user_status === 'active' ? 'Active' : 'Inactive');
            $('#newStatus').val(student.user_status);
            $('#statusRemarks').val('');

            $('#statusUpdateModal').modal('show');
        }
    });

    // Status update form submission
    $('#statusUpdateForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            user_id: $('#statusUserId').val(),
            course_id: $('#statusCourseId').val(),
            status: $('#newStatus').val(),
            remarks: $('#statusRemarks').val()
        };

        $.ajax({
            url: '<?= site_url('teacher/update-student-status') ?>',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                if (response.success) {
                    $('#statusUpdateModal').modal('hide');
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
    });

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

.btn-outline-info {
    border-color: #0dcaf0;
    color: #0dcaf0;
}

.btn-outline-info:hover {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
}

.btn-outline-warning {
    border-color: #ffc107;
    color: #ffc107;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-outline-danger {
    border-color: #dc3545;
    color: #dc3545;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}

.modal-content {
    border-radius: 10px;
}

.form-label {
    font-weight: 500;
}
</style>

<?= $this->endSection() ?>
