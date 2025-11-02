<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-5">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white text-center">
                <h3>Upload Material for <?php echo esc($course_title); ?></h3>
            </div>
            <div class="card-body p-4">
                <!-- Flash Messages -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success" role="alert">
                        <?= esc(session()->getFlashdata('success')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <!-- Upload Form -->
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="material" class="form-label">Select File</label>
                        <input type="file" class="form-control" id="material" name="material" required>
                        <div class="form-text">Allowed file types: PDF, PPT, DOC, etc.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Material</button>
                    <a href="<?php echo base_url('/dashboard'); ?>" class="btn btn-secondary">Back to Courses</a>
                </form>
            </div>
        </div>

        <!-- Uploaded Materials -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-primary text-white">
                <h4>Uploaded Materials</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($materials)): ?>
                    <div class="list-group">
                        <?php foreach ($materials as $material): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo esc($material['file_name']); ?></strong>
                                    <br><small class="text-muted">Uploaded on: <?php echo date('M j, Y, g:i a', strtotime($material['created_at'])); ?></small>
                                </div>
                                <div>
                                    <a href="<?php echo base_url('materials/delete/' . $material['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this material?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No materials uploaded yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
