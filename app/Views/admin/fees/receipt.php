<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $p = $payment; ?>

<div class="pagehead no-print">
    <h1>Fee Receipt</h1>
    <div class="actions">
        <a class="btn" href="#" onclick="window.print();return false;"><i class="bi bi-printer-fill"></i> Print</a>
        <a class="btn btn--ghost" href="<?= site_url('/admin/fees') ?>">Back</a>
    </div>
</div>

<!-- ── A5 Receipt ── -->
<div class="receipt-a5" id="receipt">
    <div class="receipt-header">
        <div class="receipt-title">Brilient Brains Library</div>
        <div class="receipt-subtitle">Fee Receipt</div>
    </div>

    <table class="receipt-table">
        <tr>
            <th>System Receipt#</th>
            <td><?= esc($p['receipt_no']) ?></td>
        </tr>
        <?php if (! empty($p['receipt_number'])): ?>
        <tr>
            <th>Manual Receipt#</th>
            <td><strong><?= esc($p['receipt_number']) ?></strong></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th>Paid On</th>
            <td><?= esc($p['paid_on']) ?></td>
        </tr>
        <tr>
            <th>Type</th>
            <td><?= esc($p['type']) ?></td>
        </tr>
        <?php if (! empty($p['for_month'])): ?>
        <tr>
            <th>For Month</th>
            <td><?= esc($p['for_month']) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th>Student</th>
            <td><strong><?= esc($p['full_name']) ?></strong></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><?= esc($p['phone'] ?? '-') ?></td>
        </tr>
        <?php if (! empty($p['seat_no'])): ?>
        <tr>
            <th>Seat</th>
            <td>#<?= esc($p['seat_no']) ?> (<?= esc($p['floor'] ?? '') ?>)</td>
        </tr>
        <?php endif; ?>
        <tr>
            <th>Admission Date</th>
            <td><?= esc($p['admission_date'] ?? '-') ?></td>
        </tr>
        <tr class="receipt-amount-row">
            <th>Amount Paid</th>
            <td><strong>₹<?= esc($p['amount']) ?></strong></td>
        </tr>
        <?php if (! empty($p['notes'])): ?>
        <tr>
            <th>Notes</th>
            <td><?= esc($p['notes']) ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <div class="receipt-footer">
        <div class="receipt-sig">
            <div class="receipt-sig-line"></div>
            <div>Authorised Signature</div>
        </div>
        <div class="receipt-generated">
            Generated: <?= esc(date('d-m-Y')) ?><br>
            <span style="font-size:.7rem;">This is a computer generated receipt.</span>
        </div>
    </div>
</div>

<style>
/* ── Screen styles ── */
.receipt-a5 {
    max-width: 540px;
    margin: 0 auto;
    background: #fff;
    border: 1px solid var(--border, #E2E8F0);
    border-radius: 12px;
    padding: 28px 32px;
    font-size: .88rem;
    color: #1E293B;
}
.receipt-header { text-align: center; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 2px solid #1E293B; }
.receipt-title  { font-size: 1.3rem; font-weight: 700; }
.receipt-subtitle { font-size: .8rem; color: #64748B; margin-top: 2px; }
.receipt-table  { width: 100%; border-collapse: collapse; }
.receipt-table th { text-align: left; color: #64748B; font-weight: 500; padding: 6px 0; width: 42%; font-size: .82rem; }
.receipt-table td { padding: 6px 0; border-bottom: 1px solid #F1F5F9; }
.receipt-amount-row th,
.receipt-amount-row td { border-top: 2px solid #1E293B; border-bottom: 2px solid #1E293B; padding: 8px 0; font-size: 1rem; }
.receipt-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 24px; padding-top: 14px; font-size: .75rem; color: #64748B; }
.receipt-sig-line { width: 120px; border-bottom: 1px solid #1E293B; margin-bottom: 4px; }
.receipt-generated { text-align: right; }

/* ── Print styles — A5 paper ── */
@media print {
    @page { size: A5; margin: 10mm; }

    .no-print,
    .bb-sidebar,
    .bb-topbar,
    .actions { display: none !important; }

    body { background: #fff !important; color: #000 !important; }
    .bb-content { padding: 0 !important; }

    .receipt-a5 {
        max-width: 100%;
        border: none;
        border-radius: 0;
        padding: 0;
        box-shadow: none;
    }
    .receipt-header { border-bottom-color: #000; }
    .receipt-amount-row th,
    .receipt-amount-row td { border-color: #000; }
}
</style>

<?= $this->endSection() ?>
