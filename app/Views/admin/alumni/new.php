<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Add Alumni'; ?>

<div class="pagehead">
    <h1>Add Alumni</h1>
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

<form class="form" method="post" action="<?= site_url('/admin/alumni/create') ?>">
    <?= csrf_field() ?>

    <!-- Row 1: Full Name | Phone -->
    <div class="row g-3">
        <div class="col-md-6">
            <label>
                Full Name <span style="color:#dc2626">*</span>
                <input type="text" name="full_name" value="<?= esc(old('full_name')) ?>" required>
            </label>
        </div>
        <div class="col-md-6">
            <label>
                Phone
                <input type="text" name="phone" value="<?= esc(old('phone')) ?>" placeholder="10-digit mobile number">
            </label>
        </div>
    </div>

    <!-- Row 2: DOB | Email -->
    <div class="row g-3">
        <div class="col-md-6">
            <label>
                Date of Birth
                <input type="date" name="dob" value="<?= esc(old('dob')) ?>">
            </label>
        </div>
        <div class="col-md-6">
            <label>
                Email
                <input type="email" name="email" value="<?= esc(old('email')) ?>" placeholder="alumni@email.com">
            </label>
        </div>
    </div>

    <!-- Row 3: Guardian Name | Preparing For -->
    <div class="row g-3">
        <div class="col-md-6">
            <label>
                Guardian Name
                <input type="text" name="guardian_name" value="<?= esc(old('guardian_name')) ?>">
            </label>
        </div>
        <div class="col-md-6">
            <label>
                Preparing For
                <select name="preparing_for">
                    <option value="">— Select —</option>
                    <?php
                    $examOptions = ['UPSC', 'SSC', 'Bank', 'State PCS', 'Other'];
                    $currentExam = old('preparing_for', '');
                    foreach ($examOptions as $opt):
                    ?>
                        <option value="<?= esc($opt) ?>" <?= $currentExam === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
    </div>

    <!-- Row 4: Admission Date | Left Date -->
    <div class="row g-3">
        <div class="col-md-6">
            <label>
                Admission Date
                <input type="date" name="admission_date" value="<?= esc(old('admission_date')) ?>">
            </label>
        </div>
        <div class="col-md-6">
            <label>
                Left Date
                <input type="date" name="left_date" value="<?= esc(old('left_date')) ?>">
            </label>
        </div>
    </div>

    <!-- Row 5: Address (full width) -->
    <label>
        Address
        <textarea name="address" rows="3" placeholder="Full address..."><?= esc(old('address')) ?></textarea>
    </label>

    <!-- Row 6: Notes (full width) -->
    <label>
        Notes
        <textarea name="notes" rows="3"><?= esc(old('notes')) ?></textarea>
    </label>

    <div class="form__actions">
        <button class="btn" type="submit">Save Alumni</button>
    </div>
</form>

<?= $this->endSection() ?>
