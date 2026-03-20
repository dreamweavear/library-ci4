<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = $student ? 'Edit Student' : 'New Student'; ?>

<div class="pagehead">
    <h1><?= esc($title) ?></h1>
    <div class="actions">
        <a class="btn btn--ghost" href="<?= site_url('/admin/students') ?>">Back</a>
    </div>
</div>

<?php if (! empty($errors)): ?>
    <div class="alert alert--error">
        <?php foreach ($errors as $e): ?>
            <div><?= esc($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form class="form" method="post" action="<?= $student ? site_url('/admin/students/' . $student['id'] . '/update') : site_url('/admin/students') ?>">
    <?= csrf_field() ?>

    <label>
        Date of Admission
        <input type="date" name="admission_date" value="<?= esc(old('admission_date', $student['admission_date'] ?? date('Y-m-d'))) ?>">
    </label>

    <?php if (! $student): ?>
        <label>
            Fees Collected (Admission)
            <input type="number" name="admission_fee_collected" min="0" step="1" value="<?= esc(old('admission_fee_collected', '0')) ?>">
        </label>
    <?php endif; ?>

    <label>
        Full Name
        <input type="text" name="full_name" value="<?= esc(old('full_name', $student['full_name'] ?? '')) ?>" required>
    </label>

    <label>
        Phone
        <input type="text" name="phone" value="<?= esc(old('phone', $student['phone'] ?? '')) ?>">
    </label>

    <label>
        Guardian Name (optional)
        <input type="text" name="guardian_name" value="<?= esc(old('guardian_name', $student['guardian_name'] ?? '')) ?>">
    </label>

    <label>
        Notes (optional)
        <textarea name="notes" rows="4"><?= esc(old('notes', $student['notes'] ?? '')) ?></textarea>
    </label>

    <div class="form__actions">
        <button class="btn" type="submit"><?= $student ? 'Update' : 'Create' ?></button>
    </div>
</form>

<?= $this->endSection() ?>
