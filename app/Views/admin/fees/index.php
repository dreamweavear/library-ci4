<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<style>
.table tbody tr:nth-child(even) { background-color: #f8f9fa; }
.table tbody tr:hover { background-color: #e8f4fd; cursor: default; }
.table thead th { background: #f1f3f5; }
</style>

<div class="pagehead">
    <h1>Fees Collection</h1>
    <div class="actions">
        <a class="btn" href="<?= site_url('/admin/fees/collect') ?>">Collect Fee</a>
        <a class="btn btn--ghost" href="<?= site_url('/admin/fees/pending') ?>">Pending Report</a>
    </div>
</div>

<div class="cards">
    <div class="card">
        <div class="card__label">Today Collection</div>
        <div class="card__value">₹<?= esc($todayTotal) ?></div>
    </div>
    <div class="card">
        <div class="card__label">This Month</div>
        <div class="card__value">₹<?= esc($monthTotal) ?></div>
    </div>
    <div class="card">
        <div class="card__label">Quick</div>
        <div class="card__value"><a class="link" href="<?= site_url('/admin/fees/collect') ?>">New Receipt</a></div>
    </div>
</div>

<section class="panel">
    <h2>Recent Receipts</h2>
    <table class="table" style="width:100%;border-collapse:collapse;font-size:0.9rem;">
        <thead>
            <tr style="background:#f8f9fa;border-bottom:2px solid #dee2e6;">
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">#</th>
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">ID</th>
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">System Receipt#</th>
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Manual Receipt#</th>
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Student</th>
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Type</th>
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Month</th>
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Paid On</th>
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Amount</th>
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;"></th>
            </tr>
        </thead>
        <tbody>
             <?php $sno = 1; ?>
            <?php foreach ($recent as $r): ?>
                <tr>
                    <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= $sno++ ?></td>
                    <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($r['id']) ?></td>
                    <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($r['receipt_no']) ?></td>
                    <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($r['receipt_number'] ?? '-') ?></td>
                    <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($r['full_name']) ?><?= ! empty($r['phone']) ? ' (' . esc($r['phone']) . ')' : '' ?></td>
                    <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($r['type']) ?></td>
                    <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($r['for_month'] ?? '-') ?></td>
                    <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($r['paid_on']) ?></td>
                    <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;">₹<?= esc($r['amount']) ?></td>
                    <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;text-align:right;"><a class="link" href="<?= site_url('/admin/fees/receipt/' . (int) $r['id']) ?>">Receipt</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?= $this->endSection() ?>
