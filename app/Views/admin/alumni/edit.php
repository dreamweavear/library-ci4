<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Edit Alumni — ' . esc($alumni['full_name']); ?>

<div class="pagehead">
    <h1>Edit Alumni</h1>
    <div class="actions">
        <a class="btn btn--ghost" href="<?= site_url('/admin/alumni') ?>">Back</a>
    </div>
</div>

<?php if (! empty($errors)): ?>
    <div class="alert alert--error">
        <?php foreach ($errors as $e): ?>
            <div><?= esc($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form class="form" method="post"
      action="<?= site_url('/admin/alumni/' . $alumni['id'] . '/update') ?>">
    <?= csrf_field() ?>

    <label>
        Full Name <span style="color:#dc2626">*</span>
        <input type="text" name="full_name" value="<?= esc(old('full_name', $alumni['full_name'])) ?>" required>
    </label>

    <label>
        Phone
        <input type="text" name="phone" value="<?= esc(old('phone', $alumni['phone'] ?? '')) ?>">
    </label>

    <label>
        Email
        <input type="email" name="email" value="<?= esc(old('email', $alumni['email'] ?? '')) ?>">
    </label>

    <label>
        Guardian Name
        <input type="text" name="guardian_name" value="<?= esc(old('guardian_name', $alumni['guardian_name'] ?? '')) ?>">
    </label>

    <label>
        Preparing For
        <select name="preparing_for">
            <option value="">— Select —</option>
            <?php
            $examOptions = ['UPSC', 'SSC', 'Bank', 'State PCS', 'Other'];
            $currentExam = old('preparing_for', $alumni['preparing_for'] ?? '');
            foreach ($examOptions as $opt):
            ?>
                <option value="<?= esc($opt) ?>" <?= $currentExam === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Address
        <textarea name="address" rows="3"><?= esc(old('address', $alumni['address'] ?? '')) ?></textarea>
    </label>

    <label>
        Admission Date
        <input type="date" name="admission_date" value="<?= esc(old('admission_date', $alumni['admission_date'] ?? '')) ?>">
    </label>

    <label>
        Left Date
        <input type="date" name="left_date" value="<?= esc(old('left_date', $alumni['left_date'] ?? '')) ?>">
    </label>

    <label>
        Notes
        <textarea name="notes" rows="3"><?= esc(old('notes', $alumni['notes'] ?? '')) ?></textarea>
    </label>

    <div class="form__actions">
        <button class="btn" type="submit">Update Alumni</button>
    </div>
</form>

<?= $this->endSection() ?>
