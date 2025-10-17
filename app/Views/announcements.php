<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Announcements</h2>
            
            <?php if (empty($announcements)): ?>
                <div class="alert alert-info">
                    <h5>No announcements available</h5>
                    <p>There are currently no announcements to display.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><?= esc($announcement['title']) ?></h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?= nl2br(esc($announcement['content'])) ?></p>
                                </div>
                                <div class="card-footer text-muted">
                                    <small>Posted on: <?= date('F j, Y \a\t g:i A', strtotime($announcement['created_at'])) ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
