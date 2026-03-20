<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $errors = $errors ?? []; ?>

<div class="pagehead">
    <h1><?= esc($title ?? 'Admin User') ?></h1>
    <div class="actions">
        <a class="btn btn--ghost" href="<?= site_url('/admin/users') ?>">Back</a>
    </div>
</div>

<div class="panel form">
    <form method="post" action="">
        <?= csrf_field() ?>

        <label>
            Name
            <input type="text" name="name" value="<?= esc(old('name', $user['name'] ?? '')) ?>" required>
            <?php if (isset($errors['name'])): ?><div class="muted small"><?= esc($errors['name']) ?></div><?php endif; ?>
        </label>

        <label>
            Username
            <input type="text" name="username" value="<?= esc(old('username', $user['username'] ?? '')) ?>" required>
            <?php if (isset($errors['username'])): ?><div class="muted small"><?= esc($errors['username']) ?></div><?php endif; ?>
        </label>

        <?php if (! $user): ?>
            <label>
                Password
                <input type="password" name="password" required>
                <?php if (isset($errors['password'])): ?><div class="muted small"><?= esc($errors['password']) ?></div><?php endif; ?>
            </label>
        <?php endif; ?>

        <?php if ($user): ?>
            <label>
                Status
                <select name="status" required>
                    <?php $s = (string) old('status', $user['status'] ?? 'ACTIVE'); ?>
                    <option value="ACTIVE" <?= $s === 'ACTIVE' ? 'selected' : '' ?>>ACTIVE</option>
                    <option value="DISABLED" <?= $s === 'DISABLED' ? 'selected' : '' ?>>DISABLED</option>
                </select>
                <?php if (isset($errors['status'])): ?><div class="muted small"><?= esc($errors['status']) ?></div><?php endif; ?>
            </label>
        <?php endif; ?>

        <div class="form__actions">
            <button class="btn" type="submit">Save</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>