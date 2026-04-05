<?= $this->extend('site/_layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-7">
        <div class="card card-soft">
            <div class="card-top-accent"></div>
            <div class="card-body p-4 p-md-5">
                <h1 class="h4 fw-semibold mb-2">Admin Login</h1>
                <p class="text-body-secondary mb-4">Login to open the admin dashboard.</p>

                <?php $errors = $errors ?? []; ?>

                <form method="post" action="<?= site_url('admin/login') ?>" class="vstack gap-3">
                    <?= csrf_field() ?>

                    <div>
                        <label class="form-label" for="username">Username</label>
                        <input class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" type="text" id="username" name="username" value="<?= esc(old('username')) ?>" placeholder="admin" required>
                        <?php if (isset($errors['username'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['username']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group">
                            <input class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" type="password" id="password" name="password" placeholder="••••••••" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleAdminPassword()" tabindex="-1" aria-label="Toggle password visibility">
                                <i class="bi bi-eye" id="adminEyeIcon"></i>
                            </button>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button class="btn btn-bb btn-lg" type="submit">Login</button>
                </form>

                <div class="mt-4 text-body-secondary small">
                    Student? <a href="<?= site_url('student/login') ?>">Student login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAdminPassword() {
    const pwd  = document.getElementById('password');
    const icon = document.getElementById('adminEyeIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        pwd.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
<?= $this->endSection() ?>
