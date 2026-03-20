<?= $this->extend('site/_layout') ?>

<?= $this->section('content') ?>
<?php $errors = $errors ?? []; ?>
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="card card-soft">
            <div class="card-top-accent"></div>
            <div class="card-body p-4 p-md-5">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-6">
                        <h1 class="h4 fw-semibold mb-2">Student Login</h1>
                        <p class="text-body-secondary mb-4">Login details are created by admin from the dashboard.</p>

                        <form method="post" action="<?= site_url('student/login') ?>" class="vstack gap-3">
                            <?= csrf_field() ?>

                            <div>
                                <label class="form-label" for="username">Username</label>
                                <input class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" type="text" id="username" name="username" value="<?= esc(old('username')) ?>" placeholder="Enter username" required>
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback"><?= esc($errors['username']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="form-label" for="password">Password</label>
                                <input class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" type="password" id="password" name="password" placeholder="••••••••" required>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
                                <?php endif; ?>
                            </div>

                            <button class="btn btn-bb btn-lg" type="submit">Login</button>
                            <div class="text-body-secondary small">
                                Admin? <a href="<?= site_url('admin/login') ?>">Login here</a>.
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-6">
                        <div class="bb-mini">
                            <div class="fw-semibold mb-1">Need an account?</div>
                            <div class="text-body-secondary small mb-3">Ask admin to create your username/password from Admin Dashboard → Student Accounts.</div>
                            <ul class="small text-body-secondary ps-3 mb-0">
                                <li>Clean navigation</li>
                                <li>Attractive, minimal cards</li>
                                <li>Mobile friendly</li>
                            </ul>
                        </div>
                        <div class="mt-3 text-body-secondary small">
                            Back to <a href="<?= site_url('/') ?>">Home</a>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>