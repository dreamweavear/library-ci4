<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php
$isEdit   = $student !== null;
$isPrefil = ! $isEdit && $prefill !== null;
$title    = $isEdit ? 'Edit Student' : ($isPrefil ? 'Re-admit from Alumni' : 'New Student');
// Helper: get value — edit > prefill > old() > default
function fval(string $field, $student, $prefill, $default = '')
{
    if ($student !== null) {
        return old($field, $student[$field] ?? $default);
    }
    if ($prefill !== null) {
        return old($field, $prefill[$field] ?? $default);
    }
    return old($field, $default);
}
?>

<div class="pagehead">
    <h1><?= esc($title) ?></h1>
    <div class="actions">
        <a class="btn btn--ghost" href="<?= site_url('/admin/students') ?>">Back</a>
    </div>
</div>

<?php if ($isPrefil): ?>
<div class="alert alert--success">Pre-filling from alumni record: <strong><?= esc($prefill['full_name']) ?></strong></div>
<?php endif; ?>

<?php if (! empty($errors)): ?>
    <div class="alert alert--error">
        <?php foreach ($errors as $e): ?>
            <div><?= esc($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form class="form" method="post"
      action="<?= $isEdit ? site_url('/admin/students/' . $student['id'] . '/update') : site_url('/admin/students') ?>"
      enctype="multipart/form-data">
    <?= csrf_field() ?>

    <label>
        Date of Admission
        <input type="date" name="admission_date" id="admission_date"
               value="<?= esc(fval('admission_date', $student, $prefill, date('Y-m-d'))) ?>">
    </label>

    <?php if (! $isEdit): ?>
        <label>
            Fees Collected (Admission)
            <input type="number" name="admission_fee_collected" id="admission_fee_collected" min="0" step="1" value="<?= esc(old('admission_fee_collected', '0')) ?>">
        </label>
        <label>
            Manual Receipt No (optional)
            <input type="text" name="receipt_number" id="receipt_number" value="<?= esc(old('receipt_number', '')) ?>">
        </label>
    <?php endif; ?>

    <label>
        Full Name <span style="color:#dc2626">*</span>
        <input type="text" name="full_name" id="full_name"
               value="<?= esc(fval('full_name', $student, $prefill)) ?>" required>
    </label>

    <label>
        Phone (used as Login Username)
        <input type="text" name="phone" id="phone"
               value="<?= esc(fval('phone', $student, $prefill)) ?>"
               placeholder="10-digit mobile number">
    </label>

    <label>
        Date of Birth (optional)
        <input type="date" name="dob" id="dob"
               value="<?= esc(fval('dob', $student, $prefill)) ?>">
    </label>

    <label>
        Email (optional)
        <input type="email" name="email" id="email"
               value="<?= esc(fval('email', $student, $prefill)) ?>"
               placeholder="student@email.com">
    </label>

    <label>
        Aadhar Number (optional, 12 digits)
        <input type="text" name="aadhar_number" id="aadhar_number"
               value="<?= esc(fval('aadhar_number', $student, $prefill)) ?>"
               maxlength="12" pattern="[0-9]{12}" inputmode="numeric" placeholder="123456789012">
    </label>

    <label>
        Preparing For (optional)
        <select name="preparing_for" id="preparing_for">
            <option value="">— Select exam —</option>
            <?php
            $examOptions = ['UPSC', 'SSC', 'Bank', 'State PCS', 'Other'];
            $currentExam = fval('preparing_for', $student, $prefill);
            foreach ($examOptions as $opt):
            ?>
                <option value="<?= esc($opt) ?>" <?= $currentExam === $opt ? 'selected' : '' ?>>
                    <?= esc($opt) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Guardian Name (optional)
        <input type="text" name="guardian_name" id="guardian_name"
               value="<?= esc(fval('guardian_name', $student, $prefill)) ?>">
    </label>

    <label>
        Address (optional)
        <textarea name="address" id="address" rows="3" placeholder="Full address..."><?= esc(fval('address', $student, $prefill)) ?></textarea>
    </label>

    <label>
        Notes (optional)
        <textarea name="notes" id="notes" rows="3"><?= esc(fval('notes', $student, $prefill)) ?></textarea>
    </label>

    <?php if ($isEdit): ?>
    <label>
        Status
        <select name="status" id="status">
            <?php
            $currentStatus = old('status', $student['status'] ?? 'active');
            foreach (['active' => 'Active', 'dormant' => 'Dormant', 'alumni' => 'Alumni'] as $val => $lbl):
            ?>
                <option value="<?= $val ?>" <?= $currentStatus === $val ? 'selected' : '' ?>><?= $lbl ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <?php endif; ?>

    <label>
        Photo (optional, max 2MB — JPEG/PNG/GIF/WEBP)
        <?php if (! empty($student['photo'])): ?>
            <div style="margin:6px 0;">
                <img src="<?= base_url(esc($student['photo'])) ?>" alt="Current photo"
                     style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:2px solid var(--border);">
                <div class="muted small" style="margin-top:4px;">Upload new photo to replace.</div>
            </div>
        <?php endif; ?>
        <input type="file" name="photo" id="photo" accept="image/*">
    </label>

    <div class="form__actions">
        <button type="button" class="btn" onclick="showStudentPreview()">
            Preview &amp; <?= $isEdit ? 'Update' : 'Save' ?>
        </button>
        <button type="submit" id="actualSubmit" style="display:none">Submit</button>
    </div>
</form>

<!-- ── Preview Modal ── -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:#2563EB;color:#fff;">
                <h5 class="modal-title" id="previewModalLabel">Student Details Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="previewBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Edit Details
                </button>
                <button type="button" class="btn btn-success" onclick="confirmStudentSubmit()">
                    Confirm &amp; <?= $isEdit ? 'Update' : 'Save' ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
<?php if ($isEdit && ! empty($student['photo'])): ?>
var _existingPhotoHtml = '<img src="<?= base_url(esc($student['photo'])) ?>" style="width:80px;height:100px;object-fit:cover;border-radius:6px;border:1px solid #ccc;">';
<?php else: ?>
var _existingPhotoHtml = '<span class="text-muted small">No photo</span>';
<?php endif; ?>

function showStudentPreview() {
    var name          = document.getElementById('full_name')?.value     || '—';
    var phone         = document.getElementById('phone')?.value         || '—';
    var guardian      = document.getElementById('guardian_name')?.value || '—';
    var aadhar        = document.getElementById('aadhar_number')?.value || '—';
    var preparingEl   = document.getElementById('preparing_for');
    var preparing     = preparingEl ? (preparingEl.options[preparingEl.selectedIndex]?.text || '—') : '—';
    var address       = document.getElementById('address')?.value       || '—';
    var email         = document.getElementById('email')?.value         || '—';
    var admDate       = document.getElementById('admission_date')?.value || '—';
    var notes         = document.getElementById('notes')?.value         || '—';

    <?php if ($isEdit): ?>
    var statusEl  = document.getElementById('status');
    var status    = statusEl ? (statusEl.options[statusEl.selectedIndex]?.text || '—') : '—';
    var statusRow = '<tr><th>Status</th><td>' + status + '</td></tr>';
    <?php else: ?>
    var feeEl  = document.getElementById('admission_fee_collected');
    var fee    = feeEl ? feeEl.value : '0';
    var statusRow = '<tr><th>Admission Fee</th><td>&#8377;' + fee + '</td></tr>';
    <?php endif; ?>

    // Photo
    var photoInput = document.getElementById('photo');
    var photoHtml  = _existingPhotoHtml;
    if (photoInput && photoInput.files && photoInput.files.length > 0) {
        var url = URL.createObjectURL(photoInput.files[0]);
        photoHtml = '<img src="' + url + '" style="width:80px;height:100px;object-fit:cover;border-radius:6px;border:1px solid #ccc;">';
    }

    document.getElementById('previewBody').innerHTML =
        '<div class="row g-3">' +
            '<div class="col-sm-9">' +
                '<table class="table table-bordered table-sm mb-0">' +
                    '<tr><th style="width:35%">Full Name</th><td><strong>' + escHtml(name) + '</strong></td></tr>' +
                    '<tr><th>Phone</th><td>' + escHtml(phone) + '</td></tr>' +
                    '<tr><th>Guardian Name</th><td>' + escHtml(guardian) + '</td></tr>' +
                    '<tr><th>Aadhar Number</th><td>' + escHtml(aadhar) + '</td></tr>' +
                    '<tr><th>Preparing For</th><td>' + escHtml(preparing) + '</td></tr>' +
                    '<tr><th>Address</th><td>' + escHtml(address) + '</td></tr>' +
                    '<tr><th>Email</th><td>' + escHtml(email) + '</td></tr>' +
                    '<tr><th>Admission Date</th><td>' + escHtml(admDate) + '</td></tr>' +
                    statusRow +
                    '<tr><th>Notes</th><td>' + escHtml(notes) + '</td></tr>' +
                '</table>' +
            '</div>' +
            '<div class="col-sm-3 text-center">' +
                '<p class="fw-bold mb-2">Photo</p>' +
                photoHtml +
            '</div>' +
        '</div>';

    new bootstrap.Modal(document.getElementById('previewModal')).show();
}

function confirmStudentSubmit() {
    document.getElementById('actualSubmit').click();
}

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
</script>

<?= $this->endSection() ?>
