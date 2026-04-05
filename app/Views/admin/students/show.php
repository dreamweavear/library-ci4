<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Student'; ?>

<div class="pagehead">
    <h1><?= esc($student['full_name']) ?></h1>
    <div class="actions">
        <a class="btn" href="<?= site_url('/admin/enrollments/new') ?>">Allot Seat</a>
        <a class="btn btn--ghost" href="<?= site_url('/admin/students/' . $student['id'] . '/edit') ?>">Edit</a>
        <a class="btn btn--ghost" href="<?= site_url('/admin/students') ?>">Back</a>
    </div>
</div>

<?php if (session()->getFlashdata('generated_login')): ?>
    <?php $creds = session()->getFlashdata('generated_login'); ?>
    <div class="alert alert--success" style="font-size:.95rem;">
        <strong>Login ID auto-generated (one-time display):</strong><br>
        Username: <code><?= esc($creds['username']) ?></code> &nbsp;|&nbsp;
        Password: <code><?= esc($creds['password']) ?></code><br>
        <span class="muted small">Share these credentials with the student. Password can be changed from Student Accounts.</span>
    </div>
<?php endif; ?>

<div class="grid2">
    <section class="panel">
        <h2>Details</h2>
        <div style="display:flex;gap:16px;align-items:flex-start;margin-bottom:12px;">
            <?php if (! empty($student['photo'])): ?>
                <img src="<?= base_url(esc($student['photo'])) ?>" alt="Photo"
                     style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:2px solid var(--border);flex-shrink:0;">
            <?php else: ?>
                <div style="width:80px;height:80px;border-radius:50%;background:rgba(37,99,235,.10);border:2px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-person-fill" style="font-size:2rem;color:var(--brand);"></i>
                </div>
            <?php endif; ?>
            <div>
                <div style="font-weight:700;font-size:1.05rem;"><?= esc($student['full_name']) ?></div>
                <?php if (! empty($student['phone'])): ?>
                    <div class="muted small"><?= esc($student['phone']) ?></div>
                <?php endif; ?>
                <?php if (! empty($student['email'])): ?>
                    <div class="muted small"><?= esc($student['email']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <table class="table table--compact">
            <tr><th>ID</th><td><?= esc($student['id']) ?></td></tr>
            <tr><th>Admission Date</th><td><?= esc($student['admission_date'] ?? '') ?></td></tr>
            <tr><th>Phone</th><td><?= esc($student['phone'] ?? '') ?></td></tr>
            <tr><th>Email</th><td><?= esc($student['email'] ?? '') ?></td></tr>
            <tr><th>Guardian</th><td><?= esc($student['guardian_name'] ?? '') ?></td></tr>
            <tr><th>Aadhar No.</th><td><?= esc($student['aadhar_number'] ?? '') ?></td></tr>
            <tr><th>Preparing For</th><td><?= esc($student['preparing_for'] ?? '') ?></td></tr>
        </table>

        <?php if (! empty($student['address'])): ?>
            <div class="muted small" style="margin-top:8px;">Address</div>
            <div><?= nl2br(esc($student['address'])) ?></div>
        <?php endif; ?>

        <?php if (! empty($student['notes'])): ?>
            <div class="muted small" style="margin-top:10px;">Notes</div>
            <div><?= nl2br(esc($student['notes'])) ?></div>
        <?php endif; ?>
    </section>

    <section class="panel">
        <h2>Active Seat</h2>
        <?php if ($active): ?>
            <div class="big">
                Seat #<?= esc($active['seat_no']) ?> (<?= esc($active['floor']) ?>)
            </div>
            <div class="muted">
                <?= esc($active['plan']) ?>
                <?php if (! empty($active['half_day_slot'])): ?>
                    (<?= esc($active['half_day_slot']) ?>)
                <?php endif; ?>
                | Fee: <?= esc($active['fee']) ?> | Start: <?= esc($active['start_date']) ?>
            </div>
            <form method="post" action="<?= site_url('/admin/enrollments/' . $active['id'] . '/end') ?>" style="margin-top:10px;">
                <?= csrf_field() ?>
                <button class="btn btn--danger" type="submit" onclick="return confirm('End this enrollment?')">End Enrollment</button>
            </form>
        <?php else: ?>
            <div class="muted">No active seat.</div>
        <?php endif; ?>
    </section>
</div>

<section class="panel">
    <h2>Enrollment History</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Seat</th>
                <th>Floor</th>
                <th>Plan</th>
                <th>Fee</th>
                <th>Start</th>
                <th>End</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $h): ?>
                <tr>
                    <td><?= esc($h['id']) ?></td>
                    <td><?= esc($h['seat_no']) ?></td>
                    <td><?= esc($h['floor']) ?></td>
                    <td>
                        <?= esc($h['plan']) ?>
                        <?php if (! empty($h['half_day_slot'])): ?>
                            (<?= esc($h['half_day_slot']) ?>)
                        <?php endif; ?>
                    </td>
                    <td><?= esc($h['fee']) ?></td>
                    <td><?= esc($h['start_date']) ?></td>
                    <td><?= esc($h['end_date'] ?? '') ?></td>
                    <td><?= esc($h['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section class="panel">
    <h2>Fees &amp; Receipts</h2>
    <div class="actions" style="margin:10px 0 14px">
        <a class="btn" href="<?= site_url('/admin/fees/collect') ?>">Collect Fee</a>
        <a class="btn btn--ghost" href="<?= site_url('/admin/fees/pending') ?>">Pending Report</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Receipt No</th>
                <th>Manual Receipt#</th>
                <th>Type</th>
                <th>Month</th>
                <th>Paid On</th>
                <th>Amount</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($payments ?? [])): ?>
                <tr><td colspan="8" class="muted">No receipts yet.</td></tr>
            <?php endif; ?>
            <?php foreach (($payments ?? []) as $p): ?>
                <tr>
                    <td><?= esc($p['id']) ?></td>
                    <td><?= esc($p['receipt_no']) ?></td>
                    <td><?= esc($p['receipt_number'] ?? '-') ?></td>
                    <td><?= esc($p['type']) ?></td>
                    <td><?= esc($p['for_month'] ?? '-') ?></td>
                    <td><?= esc($p['paid_on']) ?></td>
                    <td>₹<?= esc($p['amount']) ?></td>
                    <td style="text-align:right"><a class="link" href="<?= site_url('/admin/fees/receipt/' . (int) $p['id']) ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?= $this->endSection() ?>
