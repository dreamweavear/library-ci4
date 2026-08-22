<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Collection Report · ' . $year; ?>

<div class="pagehead no-print">
    <h1>Collection Report</h1>
    <div class="actions">
        <button class="btn" onclick="window.print()"><i class="bi bi-printer-fill"></i> Print / PDF</button>
        <a class="btn btn--ghost" href="<?= site_url('/admin/fees/report/export-csv?year=' . $year) ?>"><i class="bi bi-file-earmark-spreadsheet"></i> Export CSV</a>
        <a class="btn btn--ghost" href="<?= site_url('/admin/fees') ?>">Fees Home</a>
    </div>
</div>

<!-- Year selector -->
<form class="no-print" method="get" action="<?= site_url('/admin/fees/report') ?>"
      style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
    <label style="font-weight:600;font-size:.9rem;">Year:</label>
    <select name="year" onchange="this.form.submit()"
            style="padding:6px 12px;border:1.5px solid var(--border,#E2E8F0);border-radius:8px;font-size:.9rem;background:#fff;">
        <?php foreach ($availableYears as $yr): ?>
            <option value="<?= esc($yr) ?>" <?= (int)$yr === $year ? 'selected' : '' ?>><?= esc($yr) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<!-- Print header (only visible when printing) -->
<div class="print-only" style="display:none;text-align:center;margin-bottom:16px;">
    <div style="font-size:1.3rem;font-weight:700;">Brilient Brains Library</div>
    <div style="font-size:.95rem;color:#555;">Collection Report — <?= esc($year) ?></div>
</div>

<!-- Summary cards -->
<div class="cards">
    <div class="card">
        <div class="card__label">Year Total (<?= esc($year) ?>)</div>
        <div class="card__value" style="color:#2563EB;">&#x20B9;<?= number_format($yearTotal) ?></div>
    </div>
    <div class="card">
        <div class="card__label">Total Transactions</div>
        <div class="card__value"><?= $yearPayments ?></div>
    </div>
    <div class="card">
        <div class="card__label">Best Month</div>
        <div class="card__value" style="color:#16a34a;">
            <?= esc($bestMonthName) ?>
            <span style="font-size:.85rem;font-weight:500;color:#64748b;">&#x20B9;<?= number_format($bestMonthAmt) ?></span>
        </div>
    </div>
</div>

<!-- Monthly breakdown table -->
<table class="table" style="width:100%;border-collapse:collapse;font-size:.9rem;">
    <thead>
        <tr style="background:#f1f3f5;border-bottom:2px solid #dee2e6;">
            <th style="padding:10px 14px;text-align:left;font-weight:600;color:#495057;">Month</th>
            <th style="padding:10px 14px;text-align:right;font-weight:600;color:#495057;">Payments</th>
            <th style="padding:10px 14px;text-align:right;font-weight:600;color:#495057;">Monthly Fees</th>
            <th style="padding:10px 14px;text-align:right;font-weight:600;color:#495057;">Admission Fees</th>
            <th style="padding:10px 14px;text-align:right;font-weight:600;color:#495057;">Total</th>
            <th class="no-print" style="padding:10px 14px;min-width:140px;font-weight:600;color:#495057;"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($months as $num => $m): ?>
        <?php
            $isEmpty = $m['row_total'] === 0;
            $pct     = $maxMonthAmt > 0 ? round($m['row_total'] / $maxMonthAmt * 100) : 0;
            $rowBg   = $isEmpty ? '' : '';
        ?>
        <tr style="border-bottom:1px solid #f0f0f0;<?= ! $isEmpty ? 'background:#fafcff;' : '' ?>">
            <td style="padding:10px 14px;font-weight:<?= $isEmpty ? '400' : '600' ?>;color:<?= $isEmpty ? '#94a3b8' : '#1e293b' ?>;">
                <?= esc($m['name']) ?>
            </td>
            <td style="padding:10px 14px;text-align:right;color:<?= $isEmpty ? '#94a3b8' : '#1e293b' ?>;">
                <?= $isEmpty ? '—' : $m['payment_count'] ?>
            </td>
            <td style="padding:10px 14px;text-align:right;color:<?= $isEmpty ? '#94a3b8' : '#1e293b' ?>;">
                <?= $isEmpty ? '—' : '&#x20B9;' . number_format($m['monthly_amt']) ?>
            </td>
            <td style="padding:10px 14px;text-align:right;color:<?= $isEmpty ? '#94a3b8' : '#1e293b' ?>;">
                <?= $m['admission_amt'] > 0 ? '&#x20B9;' . number_format($m['admission_amt']) : ($isEmpty ? '—' : '—') ?>
            </td>
            <td style="padding:10px 14px;text-align:right;font-weight:700;color:<?= $isEmpty ? '#94a3b8' : '#2563EB' ?>;">
                <?= $isEmpty ? '—' : '&#x20B9;' . number_format($m['row_total']) ?>
            </td>
            <td class="no-print" style="padding:10px 14px;">
                <?php if (! $isEmpty): ?>
                <div style="background:#e2e8f0;border-radius:4px;height:8px;width:100%;">
                    <div style="background:<?= $pct >= 80 ? '#16a34a' : ($pct >= 40 ? '#2563EB' : '#93c5fd') ?>;height:8px;border-radius:4px;width:<?= $pct ?>%;transition:width .3s;"></div>
                </div>
                <div style="font-size:.7rem;color:#94a3b8;margin-top:2px;text-align:right;"><?= $pct ?>%</div>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background:#f1f3f5;border-top:2px solid #dee2e6;">
            <td style="padding:11px 14px;font-weight:700;">Total <?= esc($year) ?></td>
            <td style="padding:11px 14px;text-align:right;font-weight:700;"><?= $yearPayments ?></td>
            <td style="padding:11px 14px;text-align:right;font-weight:700;">
                &#x20B9;<?= number_format(array_sum(array_column($months, 'monthly_amt'))) ?>
            </td>
            <td style="padding:11px 14px;text-align:right;font-weight:700;">
                &#x20B9;<?= number_format(array_sum(array_column($months, 'admission_amt'))) ?>
            </td>
            <td style="padding:11px 14px;text-align:right;font-weight:800;font-size:1.05rem;color:#2563EB;">
                &#x20B9;<?= number_format($yearTotal) ?>
            </td>
            <td class="no-print"></td>
        </tr>
    </tfoot>
</table>

<style>
@media print {
    .no-print { display: none !important; }
    .print-only { display: block !important; }
    .bb-sidebar, .bb-topbar, .actions { display: none !important; }
    .bb-main { margin-left: 0 !important; width: 100% !important; }
    .bb-content { padding: 6mm !important; }
    body { background: #fff !important; color: #000 !important; }
    .card { border: 1px solid #ccc !important; background: #fff !important; }
    @page { margin: 12mm; }
}
</style>

<?= $this->endSection() ?>
