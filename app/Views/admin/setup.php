<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Setup Required'; ?>

<div class="pagehead">
    <h1>Setup Required</h1>
    <div class="actions">
        <a class="btn btn--ghost" href="<?= site_url('/admin') ?>">Retry</a>
    </div>
</div>

<div class="panel">
    <h2>Database Not Ready</h2>
    <p class="muted small">This admin app needs database tables for seats, students, and enrollments.</p>

    <?php if (! empty($error)): ?>
        <div class="alert alert--error"><?= esc($error) ?></div>
    <?php endif; ?>

    <h3>Fix Steps</h3>
    <ul class="list">
        <li>Create a MySQL database (example: <code>ci4_library</code>).</li>
        <li>Set DB config in <code>.env</code> (already created in your project root).</li>
        <li>Run migrations and seed seats.</li>
    </ul>

    <h3>Commands</h3>
    <div class="muted small">If <code>php spark</code> fails, run it using XAMPP PHP directly.</div>
    <pre class="code"><code>php spark migrate
php spark db:seed SeatSeeder

# or (XAMPP)
G:\xampp\php\php.exe spark migrate
G:\xampp\php\php.exe spark db:seed SeatSeeder</code></pre>

    <h3>Alternative (phpMyAdmin)</h3>
    <div class="muted small">Import this SQL file into your database:</div>
    <pre class="code"><code>library_schema_mysql.sql</code></pre>
</div>

<?= $this->endSection() ?>
