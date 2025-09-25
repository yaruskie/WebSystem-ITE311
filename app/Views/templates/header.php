<?php $isLoggedIn = (bool) session('isLoggedIn'); $role = strtolower((string) session('role')); ?>
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('/') ?>">MySite</a>
        <div class="d-flex align-items-center gap-2">
            <a class="btn btn-outline-light" href="<?= site_url('/') ?>">Home</a>
            <a class="btn btn-outline-light" href="<?= site_url('about') ?>">About</a>
            <a class="btn btn-outline-light" href="<?= site_url('contact') ?>">Contact</a>

            <?php if ($isLoggedIn): ?>
                <a class="btn btn-light" href="<?= site_url('dashboard') ?>">Dashboard</a>
                
                <a class="btn btn-outline-danger" href="<?= site_url('logout') ?>">Logout</a>
            <?php else: ?>
                <a class="btn btn-outline-light" href="<?= site_url('login') ?>">Login</a>
               
            <?php endif; ?>
        </div>
    </div>
</nav>


