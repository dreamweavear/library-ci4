<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Import Alumni CSV'; ?>

<div class="pagehead">
    <h1>Import Alumni CSV</h1>
    <div class="actions">
        <a class="btn btn--ghost" href="<?= site_url('/admin/alumni') ?>">Back to Alumni</a>
        <a class="btn btn--ghost" href="<?= base_url('sample_alumni.csv') ?>">Download Sample CSV</a>
    </div>
</div>

<?php if (! empty($errors)): ?>
    <div class="alert alert--error">
        <?php foreach ($errors as $e): ?>
            <div><?= esc($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (! empty($result)): ?>
    <div class="alert alert--success"><?= esc($result) ?></div>
<?php endif; ?>

<div class="panel" style="max-width:540px;">
    <h2 style="margin-bottom:14px;">Upload CSV File</h2>

    <form method="post" action="<?= site_url('/admin/alumni/import/store') ?>"
          enctype="multipart/form-data">
        <?= csrf_field() ?>

        <label style="display:block;margin-bottom:14px;">
            CSV File <span style="color:#dc2626">*</span>
            <input type="file" name="csv_file" accept=".csv,text/csv" required style="display:block;margin-top:6px;">
        </label>

        <label style="display:block;margin-bottom:18px;">
            On Duplicate Phone
            <select name="on_duplicate" style="display:block;margin-top:6px;">
                <option value="skip">Skip (do not overwrite existing)</option>
                <option value="update">Update existing record</option>
            </select>
        </label>

        <button class="btn" type="submit">Import</button>
    </form>

    <hr style="margin:20px 0;border-color:#E2E8F0;">

    <h3 style="margin-bottom:8px;">Expected CSV Format</h3>
    <p class="muted small" style="margin-bottom:8px;">
        Required header row (column order does not matter, extra columns are ignored):
    </p>
    <pre class="code" style="font-size:.78rem;">full_name,phone,dob,email,guardian_name,address,preparing_for,admission_date,left_date,notes</pre>
    <ul class="list muted small" style="margin-top:10px;">
        <li><code>full_name</code> — required, all others optional</li>
        <li><code>phone</code> — used for duplicate detection</li>
        <li><code>dob</code> — Date of Birth, YYYY-MM-DD format</li>
        <li>Dates: YYYY-MM-DD format</li>
        <li>Handles 400+ rows (batch import)</li>
    </ul>
</div>

<?= $this->endSection() ?>
