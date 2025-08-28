<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'My Site with Bootstrap' ?></title>

    <!-- Bootstrap CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Simple Navigation Bar -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url('/') ?>">MySite</a>
            <div>
                <a class="btn btn-outline-light me-2" href="<?= site_url('/') ?>">Home</a>
                <a class="btn btn-outline-light me-2" href="<?= site_url('about') ?>">About</a>
                <a class="btn btn-outline-light" href="<?= site_url('contact') ?>">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="container mt-5">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Bootstrap JS via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
