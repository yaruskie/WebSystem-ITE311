<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Manage Courses</h2>
        <a href="<?= site_url('/admin/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>

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

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">All Courses</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($courses)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 25%;">Course Title</th>
                                <th style="width: 35%;">Description</th>
                                <th style="width: 15%;">Instructor</th>
                                <th style="width: 15%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><strong><?= esc($course['id']) ?></strong></td>
                                    <td><?= esc($course['title']) ?></td>
                                    <td>
                                        <small><?= esc(substr($course['description'] ?? '', 0, 60)) ?><?= strlen($course['description'] ?? '') > 60 ? '...' : '' ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= esc($course['teacher_name'] ?? 'N/A') ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('admin/course/' . $course['id'] . '/upload') ?>" class="btn btn-sm btn-success" title="Upload Materials">
                                            <i class="fas fa-upload"></i> Upload
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No courses available yet. Create a course to get started.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
