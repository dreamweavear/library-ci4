<?= $this->extend('site/_layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-7">
        <div class="card card-soft">
            <div class="card-top-accent"></div>
            <div class="card-body p-4 p-md-5">
                <h1 class="h4 fw-semibold mb-2">Create First Admin</h1>
                <p class="text-body-secondary mb-4">No admin user found. Create the first admin account.</p>

                <?php $errors = $errors ?? []; ?>

                <form method="post" action="<?= site_url('admin/login') ?>" class="vstack gap-3">
                    <?= csrf_field() ?>

                    <div>
                        <label class="form-label" for="name">Name</label>
                        <input class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" type="text" id="name" name="name" value="<?= esc(old('name')) ?>" placeholder="Admin" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="form-label" for="username">Username</label>
                        <input class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" type="text" id="username" name="username" value="<?= esc(old('username')) ?>" placeholder="admin" required>
                        <?php if (isset($errors['username'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['username']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="form-label" for="password">Password</label>
                        <input class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" type="password" id="password" name="password" placeholder="Min 6 characters" required>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button class="btn btn-bb btn-lg" type="submit">Create Admin</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>