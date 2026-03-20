<?= $this->extend('site/_layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card card-soft">
            <div class="card-top-accent"></div>
            <div class="card-body p-4 p-md-5">
                <h1 class="h4 fw-semibold mb-2">Setup Required</h1>
                <p class="text-body-secondary mb-3">Database tables are not ready. Run migrations and seed seats.</p>

                <?php if (! empty($error)): ?>
                    <div class="alert alert-danger bb-alert" role="alert"><?= esc($error) ?></div>
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="bb-mini">
                            <div class="fw-semibold mb-1">Commands</div>
                            <div class="text-body-secondary small">Run in project root:</div>
                            <pre class="mb-0 small"><code>php spark migrate
php spark db:seed SeatSeeder</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="bb-mini">
                            <div class="fw-semibold mb-1">Alternative</div>
                            <div class="text-body-secondary small">Import SQL in phpMyAdmin:</div>
                            <pre class="mb-0 small"><code>library_schema_mysql.sql</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-body-secondary small">
                    After migration: open <a href="<?= site_url('admin/login') ?>">Admin Login</a> to create the first admin.
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>