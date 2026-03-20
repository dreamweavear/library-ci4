<?= $this->extend('site/_layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="card card-soft">
            <div class="card-top-accent"></div>
            <div class="card-body p-4 p-md-5">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-6">
                        <h1 class="h4 fw-semibold mb-2">Admin Login</h1>
                        <p class="text-body-secondary mb-4">Sign in to manage seats, students, and enrollments.</p>

                        <form method="post" action="<?= site_url('admin/login') ?>" class="vstack gap-3">
                            <?= csrf_field() ?>

                            <div>
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control" type="email" id="email" name="email" placeholder="admin@example.com" required>
                            </div>

                            <div>
                                <label class="form-label" for="password">Password</label>
                                <input class="form-control" type="password" id="password" name="password" placeholder="••••••••" required>
                            </div>

                            <div class="d-flex align-items-center justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                <a class="link-secondary small" href="#" tabindex="-1" aria-disabled="true">Forgot password?</a>
                            </div>

                            <button class="btn btn-bb btn-lg" type="submit">Login</button>
                            <div class="text-body-secondary small">
                                Student? <a href="<?= site_url('student/login') ?>">Login here</a>.
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-6">
                        <div class="bb-mini">
                            <div class="fw-semibold mb-1">Admin Panel</div>
                            <div class="text-body-secondary small mb-3">This is a Bootstrap 5 UI page. Authentication can be connected later.</div>
                            <ul class="small text-body-secondary ps-3 mb-0">
                                <li>Dashboard overview</li>
                                <li>Seat management</li>
                                <li>Student records</li>
                                <li>Enrollments</li>
                            </ul>
                        </div>
                        <div class="mt-3 text-body-secondary small">
                            Demo shortcut: <a href="<?= site_url('admin') ?>">Go to admin dashboard</a>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
