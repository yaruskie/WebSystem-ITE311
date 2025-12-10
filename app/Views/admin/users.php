<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Manage Users</h2>
        <div>
            <a href="<?= site_url('/admin/dashboard') ?>" class="btn btn-secondary me-2">Back</a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
        </div>
    </div>

    <div class="alert alert-info">
        <h5>User Management System</h5>
        <p>Here you can view all registered users, change their roles, activate/deactivate accounts, and add new users. The main admin account is protected and cannot be modified except for password changes.</p>
        <ul class="mb-0">
            <li><strong>Role Changes:</strong> Use the dropdown to instantly update user roles (Student, Teacher, Admin).</li>
            <li><strong>Account Status:</strong> Deactivate accounts to prevent login without deleting data.</li>
            <li><strong>Add Users:</strong> Click "Add User" to create new accounts with validation.</li>
        </ul>
    </div>

    <div id="alert-placeholder"></div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-users"></i> No users found. Use the "Add User" button to create your first user.
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <?php $isProtected = strtolower($u['email'] ?? '') === strtolower(PROTECTED_ADMIN_EMAIL); ?>
                                <tr data-user-id="<?= esc($u['id']) ?>">
                                    <td><?= esc($u['id']) ?></td>
                                    <td><?= esc($u['name']) ?></td>
                                    <td><?= esc($u['email']) ?></td>
                                    <td>
                                        <?php if ($isProtected): ?>
                                            <span class="badge bg-dark"><?= esc(ucfirst($u['role'])) ?></span>
                                        <?php else: ?>
                                            <select class="form-select form-select-sm role-select" style="width:160px;">
                                                <option value="student" <?= $u['role']==='student' ? 'selected' : '' ?>>Student</option>
                                                <option value="teacher" <?= $u['role']==='teacher' ? 'selected' : '' ?>>Teacher</option>
                                                <option value="admin" <?= $u['role']==='admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status = $u['status'] ?? 'active';
                                        $badgeClass = 'bg-success';
                                        $statusText = 'Active';
                                        if ($status === 'inactive') {
                                            $badgeClass = 'bg-secondary';
                                            $statusText = 'Inactive';
                                        } elseif ($status === 'deleted') {
                                            $badgeClass = 'bg-danger';
                                            $statusText = 'Deleted';
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?> status-badge"><?= esc($statusText) ?></span>
                                    </td>
                                <td>
                                    <?php if ($isProtected): ?>
                                        <button class="btn btn-sm btn-outline-secondary me-1" disabled>Edit</button>
                                        <button class="btn btn-sm btn-outline-warning me-1" disabled>Deactivate</button>
                                        <button class="btn btn-sm btn-outline-danger" disabled>Delete</button>
                                    <?php elseif (($u['status'] ?? 'active') === 'deleted'): ?>
                                        <button class="btn btn-sm btn-outline-secondary me-1" disabled>Edit</button>
                                        <button class="btn btn-sm btn-outline-warning me-1" disabled>Reactivate</button>
                                        <button class="btn btn-sm btn-outline-danger" disabled>Deleted</button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary me-1 edit-user-btn" data-user-id="<?= esc($u['id']) ?>" data-name="<?= esc($u['name']) ?>" data-email="<?= esc($u['email']) ?>">Edit</button>
                                        <button class="btn btn-sm btn-outline-warning me-1 toggle-status-btn" data-status="<?= ($u['status'] ?? 'active') === 'active' ? 'inactive' : 'active' ?>">
                                            <?= ($u['status'] ?? 'active') === 'active' ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-user-btn" data-user-id="<?= esc($u['id']) ?>" data-name="<?= esc($u['name']) ?>">Delete</button>
                                    <?php endif; ?>
                                </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addUserForm">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
            <div class="form-text">At least 8 characters, include letters and numbers.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
              <option value="student">Student</option>
              <option value="teacher">Teacher</option>
              <option value="admin">Admin</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" id="submitAddUser" class="btn btn-primary">Add User</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editUserForm">
          <input type="hidden" name="user_id" id="editUserId">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" id="editName" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="editEmail" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">New Password (leave blank to keep current)</label>
            <input type="password" name="password" id="editPassword" class="form-control">
            <div class="form-text">At least 8 characters, include letters and numbers if changing.</div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" id="submitEditUser" class="btn btn-primary">Update User</button>
      </div>
    </div>
  </div>
</div>

<script>
;(function($){
    function showAlert(message, type) {
        var html = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
            message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        $('#alert-placeholder').html(html);
        setTimeout(function(){ $('.alert').fadeOut(); }, 5000);
    }

    $(document).ready(function(){
        // Role change
        $('.role-select').on('change', function(){
            var select = $(this);
            var tr = select.closest('tr');
            var userId = tr.data('user-id');
            var role = select.val();
            
            console.log('Updating role for user ' + userId + ' to ' + role);

            $.post('<?= site_url('/admin/users/update_role') ?>', { user_id: userId, role: role })
                .done(function(res){
                    console.log('Role update response:', res);
                    if (res.success) {
                        showAlert(res.message, 'success');
                    } else {
                        showAlert(res.message, 'danger');
                    }
                })
                .fail(function(xhr, status, error){ 
                    console.error('Role update error:', xhr, status, error);
                    showAlert('Server error while updating role: ' + error, 'danger'); 
                });
        });

        // Toggle status
        $('.toggle-status-btn').on('click', function(){
            var btn = $(this);
            var tr = btn.closest('tr');
            var userId = tr.data('user-id');
            var newStatus = btn.data('status');
            
            console.log('Toggling status for user ' + userId + ' to ' + newStatus);

            $.post('<?= site_url('/admin/users/toggle_status') ?>', { user_id: userId, status: newStatus })
                .done(function(res){
                    console.log('Status toggle response:', res);
                    if (res.success) {
                        showAlert(res.message, 'success');
                        // Update row
                        var badge = tr.find('.status-badge');
                        if (newStatus === 'inactive') {
                            badge.removeClass('bg-success').addClass('bg-secondary').text('Inactive');
                            btn.text('Activate');
                            btn.data('status', 'active');
                        } else {
                            badge.removeClass('bg-secondary').addClass('bg-success').text('Active');
                            btn.text('Deactivate');
                            btn.data('status', 'inactive');
                        }
                    } else {
                        showAlert(res.message, 'danger');
                    }
                })
                .fail(function(xhr, status, error){ 
                    console.error('Status toggle error:', xhr, status, error);
                    showAlert('Server error while changing status: ' + error, 'danger'); 
                });
        });

        // Add user submit
        $('#submitAddUser').on('click', function(){
            var form = $('#addUserForm');
            var data = form.serialize();
            
            console.log('Submitting new user with data:', data);
            
            $.post('<?= site_url('/admin/users/add') ?>', data)
                .done(function(res){
                    console.log('Add user response:', res);
                    if (res.success) {
                        showAlert(res.message, 'success');
                        form[0].reset();
                        var modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
                        if (modal) modal.hide();
                        setTimeout(function(){ location.reload(); }, 1000);
                    } else {
                        showAlert(res.message, 'danger');
                    }
                })
                .fail(function(xhr, status, error){
                    console.error('Add user error:', xhr, status, error);
                    console.log('Response text:', xhr.responseText);
                    showAlert('Server error while adding user: ' + error, 'danger');
                });
        });

        // Edit user button
        $('.edit-user-btn').on('click', function(){
            var btn = $(this);
            var userId = btn.data('user-id');
            var name = btn.data('name');
            var email = btn.data('email');

            $('#editUserId').val(userId);
            $('#editName').val(name);
            $('#editEmail').val(email);
            $('#editPassword').val('');

            var modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            modal.show();
        });

        // Edit user submit
        $('#submitEditUser').on('click', function(){
            var form = $('#editUserForm');
            var data = form.serialize();

            console.log('Submitting edit user with data:', data);

            $.post('<?= site_url('/admin/users/edit') ?>', data)
                .done(function(res){
                    console.log('Edit user response:', res);
                    if (res.success) {
                        showAlert(res.message, 'success');
                        form[0].reset();
                        var modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                        if (modal) modal.hide();
                        setTimeout(function(){ location.reload(); }, 1000);
                    } else {
                        showAlert(res.message, 'danger');
                    }
                })
                .fail(function(xhr, status, error){
                    console.error('Edit user error:', xhr, status, error);
                    console.log('Response text:', xhr.responseText);
                    showAlert('Server error while editing user: ' + error, 'danger');
                });
        });

        // Delete user button
        $('.delete-user-btn').on('click', function(){
            var btn = $(this);
            var userId = btn.data('user-id');
            var userName = btn.data('name');

            if (confirm('Are you sure you want to delete user "' + userName + '"? This action cannot be undone.')) {
                console.log('Deleting user ' + userId);

                $.post('<?= site_url('/admin/users/delete') ?>', { user_id: userId })
                    .done(function(res){
                        console.log('Delete user response:', res);
                        if (res.success) {
                            showAlert(res.message, 'success');
                            btn.closest('tr').remove();
                        } else {
                            showAlert(res.message, 'danger');
                        }
                    })
                    .fail(function(xhr, status, error){
                        console.error('Delete user error:', xhr, status, error);
                        showAlert('Server error while deleting user: ' + error, 'danger');
                    });
            }
        });
    });
})(jQuery);
</script>

<?= $this->endSection() ?>
