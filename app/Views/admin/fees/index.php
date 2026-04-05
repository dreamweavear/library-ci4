<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

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
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>System Receipt#</th>
                <th>Manual Receipt#</th>
                <th>Student</th>
                <th>Type</th>
                <th>Month</th>
                <th>Paid On</th>
                <th>Amount</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent as $r): ?>
                <tr>
                    <td><?= esc($r['id']) ?></td>
                    <td><?= esc($r['receipt_no']) ?></td>
                    <td><?= esc($r['receipt_number'] ?? '-') ?></td>
                    <td><?= esc($r['full_name']) ?><?= ! empty($r['phone']) ? ' (' . esc($r['phone']) . ')' : '' ?></td>
                    <td><?= esc($r['type']) ?></td>
                    <td><?= esc($r['for_month'] ?? '-') ?></td>
                    <td><?= esc($r['paid_on']) ?></td>
                    <td>₹<?= esc($r['amount']) ?></td>
                    <td style="text-align:right"><a class="link" href="<?= site_url('/admin/fees/receipt/' . (int) $r['id']) ?>">Receipt</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?= $this->endSection() ?>
