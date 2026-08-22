<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<style>
.table tbody tr:nth-child(even) { background-color: #f8f9fa; }
.table tbody tr:hover { background-color: #e8f4fd; cursor: default; }
.table thead th { background: #f1f3f5; }
</style>

<?php $title = 'Enrollments'; ?>

<div class="pagehead">
    <h1>Enrollments (<?= esc($status) ?>)</h1>
    <div class="actions">
        <a class="btn" href="<?= site_url('/admin/enrollments/new') ?>">Allot Seat</a>
        <?php if ($status === 'ACTIVE'): ?>
            <a class="btn btn--ghost" href="<?= site_url('/admin/enrollments?status=ENDED') ?>">View Ended</a>
        <?php else: ?>
            <a class="btn btn--ghost" href="<?= site_url('/admin/enrollments?status=ACTIVE') ?>">View Active</a>
        <?php endif; ?>
        <button class="btn no-print" onclick="window.print()"><i class="bi bi-printer-fill"></i> Print / PDF</button>
        <a class="btn btn--ghost no-print" href="<?= site_url('/admin/enrollments/export-csv') . '?status=' . esc($status) ?>"><i class="bi bi-file-earmark-spreadsheet"></i> Export CSV</a>
    </div>
</div>

<table class="table" style="width:100%;border-collapse:collapse;font-size:0.9rem;">
    <thead>
        <tr style="background:#f8f9fa;border-bottom:2px solid #dee2e6;">
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Sr.No</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">ID</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Student</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Phone</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Seat</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Floor</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Plan</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Fee</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Start</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">End</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;"></th>
        </tr>
    </thead>
    <tbody>
        <?php $sno = 1; ?>
        <?php foreach ($enrollments as $e): ?>
        <tr>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= $sno++ ?></td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($e['id']) ?></td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><a class="link" href="<?= site_url('/admin/students/' . $e['student_id']) ?>"><?= esc($e['full_name']) ?></a></td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($e['phone'] ?? '') ?></td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($e['seat_no']) ?></td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($e['floor']) ?></td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;">
                <?= esc($e['plan']) ?>
                <?php if (! empty($e['half_day_slot'])): ?>
                    (<?= esc($e['half_day_slot']) ?>)
                <?php endif; ?>
            </td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($e['fee']) ?></td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($e['start_date']) ?></td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($e['end_date'] ?? '') ?></td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;">
                <?php if ($status === 'ACTIVE'): ?>
                    <form method="post" action="<?= site_url('/admin/enrollments/' . $e['id'] . '/end') ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn--tiny btn--danger" type="submit" onclick="return confirm('End enrollment?')">End</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $pager->links() ?>

<?= $this->endSection() ?>

