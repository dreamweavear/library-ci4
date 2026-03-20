<?= $this->extend('site/_layout') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <h1 class="h4 fw-semibold mb-1">Student Dashboard</h1>
        <div class="text-body-secondary">Welcome, <?= esc($student['full_name'] ?? 'Student') ?>.</div>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="<?= site_url('/') ?>">Home</a>
        <a class="btn btn-bb" href="<?= site_url('student/logout') ?>">Logout</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card card-soft h-100">
            <div class="card-top-accent"></div>
            <div class="card-body">
                <div class="fw-semibold mb-2">Your Details</div>
                <div class="text-body-secondary">Phone: <?= esc($student['phone'] ?? '-') ?></div>
                <div class="text-body-secondary">Guardian: <?= esc($student['guardian_name'] ?? '-') ?></div>
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
                    <div class="text-body-secondary">Fee: ₹<?= esc($active['fee']) ?></div>
                    <div class="text-body-secondary">Start: <?= esc($active['start_date']) ?></div>
                <?php else: ?>
                    <div class="text-body-secondary">No active enrollment found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>