<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $errors = $errors ?? []; ?>

<div class="pagehead">
    <h1>Create Student Login</h1>
    <div class="actions">
        <a class="btn btn--ghost" href="<?= site_url('/admin/student-accounts') ?>">Back</a>
    </div>
</div>

<div class="panel form">
    <form method="post" action="">
        <?= csrf_field() ?>

        <label>
            Student
            <select name="student_id" required>
                <option value="">Select student...</option>
                <?php foreach ($students as $s): ?>
                    <option value="<?= esc($s['id']) ?>" <?= (string) old('student_id') === (string) $s['id'] ? 'selected' : '' ?>>
                        #<?= esc($s['id']) ?> · <?= esc($s['full_name']) ?><?= $s['phone'] ? ' (' . esc($s['phone']) . ')' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['student_id'])): ?><div class="muted small"><?= esc($errors['student_id']) ?></div><?php endif; ?>
        </label>

        <label>
            Username
            <input type="text" name="username" value="<?= esc(old('username')) ?>" placeholder="e.g. s101" required>
            <?php if (isset($errors['username'])): ?><div class="muted small"><?= esc($errors['username']) ?></div><?php endif; ?>
        </label>

        <label>
            Password
            <input type="text" name="password" value="<?= esc(old('password')) ?>" placeholder="Set a temporary password" required>
            <?php if (isset($errors['password'])): ?><div class="muted small"><?= esc($errors['password']) ?></div><?php endif; ?>
        </label>

        <div class="form__actions">
            <button class="btn" type="submit">Create Login</button>
        </div>
    </form>

    <p class="muted small">Tip: After creating, student can login from <a class="link" href="<?= site_url('/student/login') ?>">Student Login</a>.</p>
</div>

<?= $this->endSection() ?>