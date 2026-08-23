<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php
$title = 'Dashboard';

// Compute values not passed from controller
$db = \Config\Database::connect();

$todayBirthdayCount = (int) $db->table('students')
    ->where('MONTH(dob)', date('n'))
    ->where('DAY(dob)', date('j'))
    ->where('dob IS NOT NULL')
    ->countAllResults();

$activeEnrollments = $fullDayCount + $amCount + $pmCount;

$totalExpected = (int) ($db->query("SELECT IFNULL(SUM(fee),0) AS t FROM enrollments WHERE status='ACTIVE'")->getRow()->t ?? 0);
$totalPaid     = (int) ($db->query("SELECT IFNULL(SUM(amount),0) AS t FROM payments")->getRow()->t ?? 0);
$pendingFees   = max(0, $totalExpected - $totalPaid);
?>

<style>
.db-section-heading {
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #64748b;
    margin: 28px 0 12px;
    display: flex;
    align-items: center;
    gap: 7px;
}
.db-section-heading i { font-size: .95rem; }

.db-grid-4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; }
.db-grid-2 { display: grid; grid-template-columns: repeat(2,1fr); gap: 14px; }
.db-grid-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; }

@media (max-width: 991px) {
    .db-grid-4, .db-grid-3 { grid-template-columns: repeat(2,1fr); }
}
@media (max-width: 575px) {
    .db-grid-4, .db-grid-3, .db-grid-2 { grid-template-columns: 1fr; }
}

/* ── Stat card (top row) ── */
.db-stat {
    border-radius: 14px;
    padding: 22px 20px 16px;
    color: #fff;
    display: flex;
    flex-direction: column;
    min-height: 140px;
    text-decoration: none;
    transition: transform .15s, box-shadow .15s;
    position: relative;
    overflow: hidden;
}
.db-stat:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.18); color: #fff; }
.db-stat__icon {
    font-size: 2rem;
    opacity: .35;
    position: absolute;
    right: 18px;
    top: 16px;
}
.db-stat__num {
    font-size: 2.4rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 4px;
}
.db-stat__label {
    font-size: .82rem;
    font-weight: 500;
    opacity: .9;
    flex: 1;
}
.db-stat__view {
    font-size: .75rem;
    font-weight: 600;
    opacity: .85;
    margin-top: 10px;
    letter-spacing: .03em;
}

/* ── Action card ── */
.db-action {
    border-radius: 14px;
    padding: 22px 18px 18px;
    color: #fff;
    display: flex;
    flex-direction: column;
    min-height: 120px;
    text-decoration: none;
    transition: transform .15s, box-shadow .15s;
}
.db-action:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.18); color: #fff; }
.db-action__icon { font-size: 1.7rem; margin-bottom: 8px; opacity: .9; }
.db-action__title { font-size: .95rem; font-weight: 700; margin-bottom: 3px; }
.db-action__sub { font-size: .75rem; opacity: .82; }

/* Colour palette */
.bg-blue   { background: linear-gradient(135deg,#2563eb,#1d4ed8); }
.bg-red    { background: linear-gradient(135deg,#dc2626,#b91c1c); }
.bg-yellow { background: linear-gradient(135deg,#d97706,#b45309); }
.bg-green  { background: linear-gradient(135deg,#16a34a,#15803d); }
.bg-orange { background: linear-gradient(135deg,#ea580c,#c2410c); }
.bg-purple { background: linear-gradient(135deg,#7c3aed,#6d28d9); }
.bg-teal   { background: linear-gradient(135deg,#0891b2,#0e7490); }
.bg-dark   { background: linear-gradient(135deg,#334155,#1e293b); }
</style>

<!-- ── Top Stats ── -->
<div class="db-section-heading" style="margin-top:8px;">
    <i class="bi bi-bar-chart-fill"></i> Overview
</div>
<div class="db-grid-4">

    <a href="<?= site_url('/admin/students?status=active') ?>" class="db-stat bg-blue">
        <i class="bi bi-people-fill db-stat__icon"></i>
        <div class="db-stat__num"><?= esc($activeStudents) ?></div>
        <div class="db-stat__label">Active Students</div>
        <div class="db-stat__view">View &rarr;</div>
    </a>

    <a href="<?= site_url('/admin/fees/pending') ?>" class="db-stat bg-red">
        <i class="bi bi-currency-rupee db-stat__icon"></i>
        <div class="db-stat__num">&#8377;<?= number_format($pendingFees) ?></div>
        <div class="db-stat__label">Pending Fees</div>
        <div class="db-stat__view">View &rarr;</div>
    </a>

    <a href="<?= site_url('/admin/birthday-reminder') ?>" class="db-stat bg-yellow">
        <i class="bi bi-balloon-heart-fill db-stat__icon"></i>
        <div class="db-stat__num"><?= esc($todayBirthdayCount) ?></div>
        <div class="db-stat__label">Today's Birthdays</div>
        <div class="db-stat__view">View &rarr;</div>
    </a>

    <a href="<?= site_url('/admin/enrollments') ?>" class="db-stat bg-green">
        <i class="bi bi-person-workspace db-stat__icon"></i>
        <div class="db-stat__num"><?= esc($activeEnrollments) ?></div>
        <div class="db-stat__label">Active Enrollments</div>
        <div class="db-stat__view">View &rarr;</div>
    </a>

</div>

<!-- ── Students ── -->
<div class="db-section-heading">
    <i class="bi bi-person-fill"></i> Students
</div>
<div class="db-grid-4">

    <a href="<?= site_url('/admin/students/new') ?>" class="db-action bg-blue">
        <i class="bi bi-person-plus-fill db-action__icon"></i>
        <div class="db-action__title">Admission Form</div>
        <div class="db-action__sub">Create student entry</div>
    </a>

    <a href="<?= site_url('/admin/students') ?>" class="db-action bg-green">
        <i class="bi bi-people-fill db-action__icon"></i>
        <div class="db-action__title">Students List</div>
        <div class="db-action__sub">View all students</div>
    </a>

    <a href="<?= site_url('/admin/enrollments/new') ?>" class="db-action bg-orange">
        <i class="bi bi-grid-3x3-gap-fill db-action__icon"></i>
        <div class="db-action__title"> Allot Seat </div>
        <div class="db-action__sub">Assign seat + plan</div>
    </a>

    <a href="<?= site_url('/admin/alumni') ?>" class="db-action bg-purple">
        <i class="bi bi-mortarboard-fill db-action__icon"></i>
        <div class="db-action__title">Alumni</div>
        <div class="db-action__sub">Manage alumni</div>
    </a>

</div>

<!-- ── Fees ── -->
<div class="db-section-heading">
    <i class="bi bi-cash-stack"></i> Fees
</div>
<div class="db-grid-2" style="max-width:680px;">

    <a href="<?= site_url('/admin/fees') ?>" class="db-action bg-red">
        <i class="bi bi-plus-circle-fill db-action__icon"></i>
        <div class="db-action__title">Fees Collection</div>
        <div class="db-action__sub">Record new fee payment</div>
    </a>

    <a href="<?= site_url('/admin/fees/pending') ?>" class="db-action bg-yellow">
        <i class="bi bi-graph-up-arrow db-action__icon"></i>
        <div class="db-action__title">Fees Pending</div>
        <div class="db-action__sub">View pending dues</div>
    </a>

</div>

<!-- ── Communication ── -->
<div class="db-section-heading">
    <i class="bi bi-chat-dots-fill"></i> Communication
</div>
<div class="db-grid-3">

    <a href="<?= site_url('/admin/bulk-whatsapp') ?>" class="db-action bg-green">
        <i class="bi bi-whatsapp db-action__icon"></i>
        <div class="db-action__title">Bulk WhatsApp</div>
        <div class="db-action__sub">Send to all students</div>
    </a>

    <a href="<?= site_url('/admin/birthday-reminder') ?>" class="db-action bg-orange">
        <i class="bi bi-balloon-heart-fill db-action__icon"></i>
        <div class="db-action__title">Birthday Reminder</div>
        <div class="db-action__sub">Today's birthdays</div>
    </a>

    <a href="<?= site_url('/admin/message-logs') ?>" class="db-action bg-dark">
        <i class="bi bi-clock-history db-action__icon"></i>
        <div class="db-action__title">Message Logs</div>
        <div class="db-action__sub">WhatsApp history</div>
    </a>

</div>

<!-- ── Seat Availability (compact) ── -->
<div class="db-section-heading">
    <i class="bi bi-grid-3x3-gap-fill"></i> Seat Availability
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px;margin-bottom:28px;">
    <section class="panel" style="margin-bottom:0;">
        <table class="table" style="margin-bottom:0;">
            <thead>
                <tr><th>Type</th><th>Available</th></tr>
            </thead>
            <tbody>
                <tr><td>Full Day</td><td><strong><?= esc($availableForFullDay) ?></strong></td></tr>
                <tr><td>Half Day (AM)</td><td><strong><?= esc($availableForAm) ?></strong></td></tr>
                <tr><td>Half Day (PM)</td><td><strong><?= esc($availableForPm) ?></strong></td></tr>
            </tbody>
        </table>
    </section>
    <section class="panel" style="margin-bottom:0;">
        <table class="table" style="margin-bottom:0;">
            <thead>
                <tr><th>Floor</th><th>Total</th><th>Full</th><th>AM</th><th>PM</th></tr>
            </thead>
            <tbody>
                <?php foreach (['GROUND', 'FIRST'] as $floor): ?>
                <tr>
                    <td><?= esc($floor) ?></td>
                    <td><?= esc($totalByFloor[$floor] ?? 0) ?></td>
                    <td><?= esc($availableFullByFloor[$floor] ?? 0) ?></td>
                    <td><?= esc($availableAmByFloor[$floor] ?? 0) ?></td>
                    <td><?= esc($availablePmByFloor[$floor] ?? 0) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>

<?= $this->endSection() ?>
