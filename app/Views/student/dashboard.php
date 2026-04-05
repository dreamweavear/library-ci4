<?= $this->extend('site/_layout') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div class="d-flex align-items-center gap-3">
        <?php if (! empty($student['photo'])): ?>
            <img src="<?= base_url(esc($student['photo'])) ?>" alt="Photo"
                 style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.12);">
        <?php else: ?>
            <div style="width:80px;height:80px;border-radius:50%;background:rgba(37,99,235,.10);border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.10);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-person-fill" style="font-size:2.2rem;color:#2563EB;"></i>
            </div>
        <?php endif; ?>
        <div>
            <h1 class="h4 fw-semibold mb-1">Student Dashboard</h1>
            <div class="text-body-secondary">Welcome, <?= esc($student['full_name'] ?? 'Student') ?>.</div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="<?= site_url('/') ?>">Home</a>
        <a class="btn btn-bb" href="<?= site_url('student/logout') ?>">Logout</a>
    </div>
</div>

<!-- Details + Active Seat -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card card-soft h-100">
            <div class="card-top-accent"></div>
            <div class="card-body">
                <div class="fw-semibold mb-2">Your Details</div>
                <div class="text-body-secondary">Phone: <?= esc($student['phone'] ?? '-') ?></div>
                <div class="text-body-secondary">Guardian: <?= esc($student['guardian_name'] ?? '-') ?></div>
                <?php if (! empty($student['preparing_for'])): ?>
                    <div class="text-body-secondary">Preparing For: <?= esc($student['preparing_for']) ?></div>
                <?php endif; ?>
                <?php if (! empty($student['email'])): ?>
                    <div class="text-body-secondary">Email: <?= esc($student['email']) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card card-soft h-100">
            <div class="card-top-accent"></div>
            <div class="card-body">
                <div class="fw-semibold mb-2">Active Seat</div>
                <?php if ($active): ?>
                    <div class="text-body-secondary">Seat: <span class="fw-semibold text-dark">#<?= esc($active['seat_no']) ?></span> (<?= esc($active['floor']) ?>)</div>
                    <div class="text-body-secondary">Plan: <?= esc($active['plan']) ?><?= ($active['plan'] ?? '') === 'HALF_DAY' ? ' (' . esc($active['half_day_slot'] ?? '-') . ')' : '' ?></div>
                    <div class="text-body-secondary">Fee: ₹<?= esc($active['fee']) ?>/month</div>
                    <div class="text-body-secondary">Since: <?= esc($active['start_date']) ?></div>
                <?php else: ?>
                    <div class="text-body-secondary">No active enrollment found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Fee Payment History -->
<div class="card card-soft">
    <div class="card-top-accent"></div>
    <div class="card-body">
        <div class="fw-semibold mb-3">Fee Payment History</div>

        <?php if (empty($payments)): ?>
            <div class="text-body-secondary">No payments recorded yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Month</th>
                            <th>Paid On</th>
                            <th>Amount</th>
                            <th>Receipt No</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $i => $p): ?>
                            <tr>
                                <td><?= count($payments) - $i ?></td>
                                <td>
                                    <?php if ($p['type'] === 'MONTHLY'): ?>
                                        <span class="badge bg-primary" style="font-size:.75rem;">Monthly</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark" style="font-size:.75rem;">Admission</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($p['for_month'] ?? '—') ?></td>
                                <td><?= esc($p['paid_on']) ?></td>
                                <td class="fw-semibold">₹<?= esc($p['amount']) ?></td>
                                <td class="text-body-secondary small"><?= esc($p['receipt_no']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-semibold">Total Paid</td>
                            <td class="fw-bold">₹<?= esc(array_sum(array_column($payments, 'amount'))) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
