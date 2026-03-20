<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Brilient Brains Library · Admin') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/admin.css') ?>">
    <link rel="stylesheet" href="<?= base_url('adminlte.css') ?>">
    <link rel="stylesheet" href="<?= base_url('admin_theme.css') ?>">
</head>
<body class="bb-admin-body">
<?php
    $path = trim(uri_string(), '/');
    $isActiveExact = static function (string $routePath) use ($path): string {
        $routePath = trim($routePath, '/');
        if ($routePath === '' && $path === 'admin') {
            return 'is-active';
        }
        return $path === $routePath ? 'is-active' : '';
    };
    $isActivePrefix = static function (string $routePrefix) use ($path): string {
        $routePrefix = trim($routePrefix, '/');
        if ($routePrefix === '') {
            return '';
        }

        if ($path === $routePrefix || str_starts_with($path, $routePrefix . '/')) {
            return 'is-active';
        }
        return '';
    };

    $adminName = (string) (session()->get('admin_name') ?: session()->get('admin_username') ?: 'Admin');
?>

<div class="bb-admin">
    <aside class="bb-sidebar">
        <div class="bb-sidebar__brand">
            <a href="<?= site_url('/admin') ?>">Brilient Brains Library</a>
            <div class="muted small">Admin Dashboard</div>
        </div>

        <div class="bb-sidebar__user">
            <div class="bb-avatar"><?= esc(strtoupper(substr($adminName, 0, 1))) ?></div>
            <div>
                <div class="bb-user__name"><?= esc($adminName) ?></div>
                <div class="muted small">Logged in</div>
            </div>
        </div>

        <nav class="bb-menu">
            <a class="bb-menu__item <?= esc($isActiveExact('admin')) ?>" href="<?= site_url('/admin') ?>">Dashboard</a>
            <a class="bb-menu__item <?= esc($isActivePrefix('admin/seats')) ?>" href="<?= site_url('/admin/seats') ?>">Seats</a>
            <a class="bb-menu__item <?= esc($isActivePrefix('admin/enrollments')) ?>" href="<?= site_url('/admin/enrollments') ?>">Seat Allotment</a>
            <a class="bb-menu__item <?= esc($isActivePrefix('admin/students')) ?>" href="<?= site_url('/admin/students') ?>">Admissions (Students)</a>
            <a class="bb-menu__item <?= esc($isActivePrefix('admin/fees')) ?>" href="<?= site_url('/admin/fees') ?>">Fees Collection</a>
            <div class="bb-menu__hr"></div>
            <a class="bb-menu__item <?= esc($isActivePrefix('admin/student-accounts')) ?>" href="<?= site_url('/admin/student-accounts') ?>">Student Login IDs</a>
            <a class="bb-menu__item <?= esc($isActivePrefix('admin/users')) ?>" href="<?= site_url('/admin/users') ?>">Admin Users</a>
            <div class="bb-menu__hr"></div>
            <a class="bb-menu__item" href="<?= site_url('/admin/logout') ?>">Logout</a>
        </nav>

        <div class="bb-sidebar__footer muted small">
            Timings: Full day 07:00–21:00 · Half day 07:00–14:00 / 14:00–21:00
        </div>
    </aside>

    <div class="bb-main">
        <header class="bb-topbar">
            <div class="bb-topbar__title">
                <?= esc($title ?? 'Admin') ?>
            </div>
            <div class="bb-topbar__actions">
                <a class="btn btn--ghost btn--tiny" href="<?= site_url('/') ?>">Open Website</a>
            </div>
        </header>

        <main class="bb-content">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert--success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert--error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>
</body>
</html>
