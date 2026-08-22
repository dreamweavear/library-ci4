<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<div class="pagehead">
    <h1>Fees Pending Report</h1>
    <div class="actions">
        <a class="btn" href="<?= site_url('/admin/fees/collect') ?>">Collect Fee</a>
        <button class="btn no-print" onclick="window.print()"><i class="bi bi-printer-fill"></i> Print / PDF</button>
        <a class="btn btn--ghost no-print" href="<?= site_url('/admin/fees/pending/export-csv') ?>"><i class="bi bi-file-earmark-spreadsheet"></i> Export CSV</a>
        <a class="btn btn--ghost" href="<?= site_url('/admin/fees') ?>">Back</a>
    </div>
</div>

<div class="panel">
    <div class="muted small">Calculated from active enrollments. Current month: <?= esc($currentMonth) ?> (inclusive).</div>
</div>

<section class="panel">
    <h2>Pending Students</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Seat</th>
                <th>Plan</th>
                <th>From</th>
                <th>To</th>
                <th>Monthly Fee</th>
                <th>Due</th>
                <th>Paid</th>
                <th>Pending</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($report)): ?>
            <tr><td colspan="9" class="muted">No pending fees.</td></tr>
        <?php endif; ?>
        <?php foreach ($report as $r): ?>
            <?php $e = $r['enrollment']; ?>
            <tr>
                <td><?= esc($e['full_name']) ?><?= ! empty($e['phone']) ? ' (' . esc($e['phone']) . ')' : '' ?></td>
                <td>#<?= esc($e['seat_no']) ?> (<?= esc($e['floor']) ?>)</td>
                <td><?= esc($e['plan']) ?><?= ! empty($e['half_day_slot']) ? ' (' . esc($e['half_day_slot']) . ')' : '' ?></td>
                <td><?= esc($r['fromMonth']) ?></td>
                <td><?= esc($r['toMonth']) ?></td>
                <td>₹<?= esc($r['monthlyFee']) ?></td>
                <td>₹<?= esc($r['dueAmount']) ?></td>
                <td>₹<?= esc($r['paidAmount']) ?></td>
                <td><strong>₹<?= esc($r['pendingAmount']) ?></strong></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?= $this->endSection() ?>

