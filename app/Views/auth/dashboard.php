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
    <?php else: ?>
        <div class="alert alert-info">This dashboard is currently implemented for admin role only.</div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>


