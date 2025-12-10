<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'My Site with Bootstrap' ?></title>

    <!-- Bootstrap CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom theme overrides (green) -->
    <style>
        :root{
            /* Primary brand color */
            --bs-primary: #198754; /* bootstrap success green */
            --bs-body-color: #1b3b2a;
            /* Dark variant used for headers/navbars */
            --bs-dark: #0f5132;
            /* Secondary accent (lighter green) */
            --bs-secondary: #6cc08a;
            --bs-success: #198754;
            --bs-info: #20c997;
        }

        /* Override some default components for a consistent green look */
        .navbar.bg-dark, .card-header.bg-dark {
            background-color: var(--bs-dark) !important;
            color: #fff !important;
        }

        .card-header.bg-secondary {
            background-color: var(--bs-secondary) !important;
            color: #fff !important;
        }

        .btn-primary {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
        }

        .btn-outline-primary {
            color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
        }

        .badge.bg-dark {
            background-color: var(--bs-dark) !important;
        }

        /* Tweak alert colors to match theme */
        .alert-info {
            background-color: #e6f4eb;
            color: #155724;
            border-color: #c7eed6;
        }
    </style>

    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- jQuery from CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <!-- Header include -->
    <?= $this->include('templates/header') ?>

    <!-- Main Content Area -->
    <div class="container mt-5">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Bootstrap JS via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
