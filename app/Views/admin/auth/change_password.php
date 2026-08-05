<?= $this->extend('admin/_layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card card-soft">
            <div class="card-top-accent"></div>
            <div class="card-body p-4 p-md-5">
                <h1 class="h4 fw-semibold mb-4">Change Password</h1>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger py-2"><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>

                <form method="post" action="<?= site_url('admin/change-password') ?>" class="vstack gap-3">
                    <?= csrf_field() ?>

                    <div>
                        <label class="form-label" for="current_password">Current Password</label>
                        <input class="form-control" type="password" id="current_password" name="current_password" placeholder="••••••••" required autocomplete="current-password">
                    </div>

                    <div>
                        <label class="form-label" for="new_password">New Password</label>
                        <input class="form-control" type="password" id="new_password" name="new_password" placeholder="••••••••" required autocomplete="new-password" minlength="6">
                        <div class="form-text">Minimum 6 characters.</div>
                    </div>

                    <div>
                        <label class="form-label" for="confirm_password">Confirm New Password</label>
                        <input class="form-control" type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required autocomplete="new-password" minlength="6">
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <button class="btn btn-bb" type="submit">Change Password</button>
                        <a href="<?= site_url('admin') ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
