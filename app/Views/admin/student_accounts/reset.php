<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $errors = $errors ?? []; ?>

<div class="pagehead">
    <h1>Reset Student Password</h1>
    <div class="actions">
        <a class="btn btn--ghost" href="<?= site_url('/admin/student-accounts') ?>">Back</a>
        <a class="btn btn--ghost" href="<?= site_url('/admin/students/' . (int) $account['student_id']) ?>">Open Student</a>
    </div>
</div>

<div class="panel form">
    <div class="muted small">Student: <?= esc($account['full_name']) ?> · Username: <span class="big"><?= esc($account['username']) ?></span></div>

    <form method="post" action="">
        <?= csrf_field() ?>

        <label>
            New Password
            <input type="text" name="password" value="<?= esc(old('password')) ?>" placeholder="Set new password" required>
            <?php if (isset($errors['password'])): ?><div class="muted small"><?= esc($errors['password']) ?></div><?php endif; ?>
        </label>

        <label>
            Status
            <select name="status" required>
                <?php $s = (string) old('status', $account['status'] ?? 'ACTIVE'); ?>
                <option value="ACTIVE" <?= $s === 'ACTIVE' ? 'selected' : '' ?>>ACTIVE</option>
                <option value="DISABLED" <?= $s === 'DISABLED' ? 'selected' : '' ?>>DISABLED</option>
            </select>
            <?php if (isset($errors['status'])): ?><div class="muted small"><?= esc($errors['status']) ?></div><?php endif; ?>
        </label>

        <div class="form__actions">
            <button class="btn" type="submit">Update</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>