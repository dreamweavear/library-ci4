<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Brilient Brains Library') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('site.css') ?>">
</head>
<body class="bb-body">
<?php
    $path = trim(uri_string(), '/');
    $isActive = static function (string $routePath) use ($path): string {
        $routePath = trim($routePath, '/');
        if ($routePath === '' && $path === '') {
            return 'active';
        }
        return $path === $routePath ? 'active' : '';
    };

    $studentLoggedIn = (bool) session()->get('student_id');
    $adminLoggedIn = (bool) session()->get('admin_user_id');
?>

<nav class="navbar navbar-expand-lg navbar-dark bb-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="<?= site_url('/') ?>">Brilient Brains Library</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#bbNav" aria-controls="bbNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="bbNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link <?= esc($isActive('')) ?>" href="<?= site_url('/') ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= esc($isActive('about')) ?>" href="<?= site_url('about') ?>">About</a>
                </li>

                <?php if ($studentLoggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= esc($isActive('student')) ?>" href="<?= site_url('student') ?>">Student Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('student/logout') ?>">Student Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?= esc($isActive('student/login')) ?>" href="<?= site_url('student/login') ?>">Student Login</a>
                    </li>
                <?php endif; ?>

                <?php if ($adminLoggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= esc($isActive('admin')) ?>" href="<?= site_url('admin') ?>">Admin Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('admin/logout') ?>">Admin Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?= esc($isActive('admin/login')) ?>" href="<?= site_url('admin/login') ?>">Admin Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="py-4">
    <div class="container">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success bb-alert" role="alert"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger bb-alert" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>
</main>

<footer class="bb-footer py-4">
    <div class="container">
        <div class="row g-3 align-items-center">
            <div class="col-md">
                <div class="fw-semibold">Brilient Brains Library</div>
                <div class="text-white-50 small">A calm space for focused study.</div>
            </div>
            <div class="col-md-auto">
                <div class="text-white-50 small">Timings: Full day 07:00–21:00 · Half day 07:00–14:00 / 14:00–21:00</div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>