<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $p = $payment; ?>

<div class="pagehead no-print">
    <h1>Fee Receipt</h1>
    <div class="actions">
        <a class="btn" href="#" onclick="window.print();return false;"><i class="bi bi-printer-fill"></i> Print / Save PDF</a>
        <a class="btn btn--ghost" href="<?= site_url('/admin/fees') ?>">Back</a>
    </div>
</div>

<?php
// Shared receipt table rows — rendered twice (Student Copy + Office Copy)
$receiptRows = static function(array $p, bool $isOfficeCopy): void { ?>
<div class="receipt-header">
    <div class="receipt-title">Brilient Brains Library</div>
    <div class="receipt-subtitle">Fee Receipt &mdash; <?= $isOfficeCopy ? 'Office Copy' : 'Student Copy' ?></div>
</div>

<table class="receipt-table">
    <tr><th>System Receipt#</th><td><?= esc($p['receipt_no']) ?></td></tr>
    <?php if (! empty($p['receipt_number'])): ?>
    <tr><th>Manual Receipt#</th><td><strong><?= esc($p['receipt_number']) ?></strong></td></tr>
    <?php endif; ?>
    <tr><th>Paid On</th><td><?= esc($p['paid_on']) ?></td></tr>
    <tr><th>Type</th><td><?= esc($p['type']) ?></td></tr>
    <?php if (! empty($p['for_month'])): ?>
    <tr><th>For Month</th><td><?= esc($p['for_month']) ?></td></tr>
    <?php endif; ?>
    <tr><th>Student</th><td><strong><?= esc($p['full_name']) ?></strong></td></tr>
    <tr><th>Phone</th><td><?= esc($p['phone'] ?? '-') ?></td></tr>
    <?php if (! empty($p['seat_no'])): ?>
    <tr><th>Seat</th><td>#<?= esc($p['seat_no']) ?> (<?= esc($p['floor'] ?? '') ?>)</td></tr>
    <?php endif; ?>
    <tr><th>Admission Date</th><td><?= esc($p['admission_date'] ?? '-') ?></td></tr>
    <tr class="receipt-amount-row">
        <th>Amount Paid</th>
        <td><strong>&#x20B9;<?= esc($p['amount']) ?></strong></td>
    </tr>
    <?php if (! empty($p['notes'])): ?>
    <tr><th>Notes</th><td><?= esc($p['notes']) ?></td></tr>
    <?php endif; ?>
</table>

<div class="receipt-footer">
    <div class="receipt-sig">
        <div class="receipt-sig-line"></div>
        <div><?= $isOfficeCopy ? 'Received by / Signature' : 'Authorised Signature' ?></div>
    </div>
    <div class="receipt-generated">
        Generated: <?= esc(date('d-m-Y')) ?><br>
        <span style="font-size:.7rem;">Computer generated receipt.</span>
    </div>
</div>
<?php }; ?>

<!-- ── Student Copy ── -->
<div class="receipt-copy" id="student-copy">
    <?php $receiptRows($p, false); ?>
</div>

<!-- ── Cut line ── -->
<div class="receipt-cut">✂ &nbsp; Cut Here &nbsp; ✂</div>

<!-- ── Office Copy ── -->
<div class="receipt-copy" id="office-copy">
    <?php $receiptRows($p, true); ?>
</div>

<style>
/* ── Screen ── */
.receipt-copy {
    max-width: 580px;
    margin: 0 auto 0;
    background: #fff;
    border: 1px solid var(--border, #E2E8F0);
    border-radius: 12px;
    padding: 24px 32px;
    font-size: .88rem;
    color: #1E293B;
}
.receipt-cut {
    max-width: 580px;
    margin: 0 auto;
    border-top: 2px dashed #aaa;
    text-align: center;
    color: #aaa;
    font-size: .75rem;
    padding: 6px 0;
    letter-spacing: .06em;
}
.receipt-header { text-align: center; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 2px solid #1E293B; }
.receipt-title  { font-size: 1.2rem; font-weight: 700; }
.receipt-subtitle { font-size: .78rem; color: #64748B; margin-top: 2px; }
.receipt-table  { width: 100%; border-collapse: collapse; }
.receipt-table th { text-align: left; color: #64748B; font-weight: 500; padding: 5px 0; width: 42%; font-size: .82rem; }
.receipt-table td { padding: 5px 0; border-bottom: 1px solid #F1F5F9; }
.receipt-amount-row th,
.receipt-amount-row td { border-top: 2px solid #1E293B; border-bottom: 2px solid #1E293B; padding: 7px 0; font-size: .98rem; }
.receipt-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 18px; padding-top: 12px; font-size: .75rem; color: #64748B; }
.receipt-sig-line { width: 130px; border-bottom: 1px solid #1E293B; margin-bottom: 4px; }
.receipt-generated { text-align: right; }

/* ── Print — A4, both copies on one page ── */
@media print {
    @page { size: A4; margin: 8mm; }

    .no-print,
    .bb-sidebar,
    .bb-topbar,
    .actions { display: none !important; }

    body { background: #fff !important; color: #000 !important; }
    .bb-content { padding: 0 !important; }

    .receipt-copy {
        max-width: 100%;
        border: none;
        border-radius: 0;
        padding: 6mm 8mm;
        box-shadow: none;
        page-break-inside: avoid;
        break-inside: avoid;
    }
    .receipt-cut {
        max-width: 100%;
        border-top: 2px dashed #555;
        margin: 4mm 0;
        padding: 2mm 0;
        color: #555;
    }
    .receipt-header { border-bottom-color: #000; }
    .receipt-amount-row th,
    .receipt-amount-row td { border-color: #000; }
    .receipt-sig-line { border-bottom-color: #000; }
}
</style>

<?= $this->endSection() ?>
