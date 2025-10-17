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
<?= $this->endSection() ?>
