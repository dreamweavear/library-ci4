<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $errors = $errors ?? []; ?>

<div class="pagehead">
    <h1>Reset Password</h1>
    <div class="actions">
        <a class="btn btn--ghost" href="<?= site_url('/admin/users') ?>">Back</a>
    </div>
</div>

<div class="panel form">
    <div class="muted small">Admin: <?= esc($user['name']) ?> (<?= esc($user['username']) ?>)</div>

    <form method="post" action="" class="mt">
        <?= csrf_field() ?>

        <label>
            New Password
            <input type="password" name="password" required>
            <?php if (isset($errors['password'])): ?><div class="muted small"><?= esc($errors['password']) ?></div><?php endif; ?>
        </label>

        <div class="form__actions">
            <button class="btn" type="submit">Update Password</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>