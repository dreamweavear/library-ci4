<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $p = $payment; ?>

<div class="pagehead">
    <h1>Fee Receipt</h1>
    <div class="actions">
        <a class="btn" href="#" onclick="window.print();return false;">Print</a>
        <a class="btn btn--ghost" href="<?= site_url('/admin/fees') ?>">Back</a>
    </div>
</div>

<div class="panel" style="max-width:820px">
    <h2>Brilient Brains Library</h2>
    <div class="muted small">Receipt generated on <?= esc(date('Y-m-d')) ?></div>

    <table class="table table--compact" style="margin-top:10px">
        <tr><th>Receipt No</th><td><?= esc($p['receipt_no']) ?></td></tr>
        <tr><th>Paid On</th><td><?= esc($p['paid_on']) ?></td></tr>
        <tr><th>Type</th><td><?= esc($p['type']) ?></td></tr>
        <tr><th>Month</th><td><?= esc($p['for_month'] ?? '-') ?></td></tr>
        <tr><th>Student</th><td><?= esc($p['full_name']) ?></td></tr>
        <tr><th>Phone</th><td><?= esc($p['phone'] ?? '-') ?></td></tr>
        <tr><th>Admission Date</th><td><?= esc($p['admission_date'] ?? '-') ?></td></tr>
        <tr><th>Amount</th><td><strong>₹<?= esc($p['amount']) ?></strong></td></tr>
        <tr><th>Notes</th><td><?= esc($p['notes'] ?? '-') ?></td></tr>
    </table>

    <div class="muted small" style="margin-top:14px">This receipt is computer generated.</div>
</div>

<style>
@media print{
    .bb-sidebar,.bb-topbar,.actions{display:none !important;}
    .bb-content{padding:0 !important;}
    body{background:#fff !important;color:#000 !important;}
}
</style>

<?= $this->endSection() ?>

