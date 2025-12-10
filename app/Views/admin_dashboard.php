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
            <h2>Welcome, Admin!</h2>
            <p>Welcome to your admin dashboard, <?= esc($user_name) ?>!</p>
            
            <div class="alert alert-success">
                <h5>Admin Dashboard</h5>
                <p>This is your administrative workspace. Here you can manage users, courses, announcements, and system settings.</p>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">User Management</h5>
                            <p class="card-text">Manage users, roles, and permissions.</p>
                            <a href="<?= site_url('/admin/users') ?>" class="btn btn-primary">Manage Users</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage Courses</h5>
                            <p class="card-text">Upload materials and manage course content.</p>
                            <a href="<?= site_url('/admin/courses') ?>" class="btn btn-success">Manage Courses</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Announcements</h5>
                            <p class="card-text">Create and manage system announcements.</p>
                            <a href="/announcements" class="btn btn-info">View Announcements</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
