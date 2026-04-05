<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Dashboard'; ?>

<div class="pagehead">
    <h1>Dashboard</h1>
    <div class="actions">
        <a class="btn" href="<?= site_url('/admin/enrollments/new') ?>">Seat Allotment</a>
        <a class="btn btn--ghost" href="<?= site_url('/admin/students/new') ?>">Admission</a>
    </div>
</div>

<!-- ── Quick Actions ── -->
<div class="bb-quick">
    <a href="<?= site_url('/admin/students/new') ?>">
        <div class="card adm-c-blue">
            <div class="adm-quick-icon"><i class="bi bi-person-plus-fill"></i></div>
            <div class="card__label">Admission</div>
            <div class="bb-quick__title">Add Student</div>
            <div class="bb-quick__desc">Create student entry and details.</div>
        </div>
    </a>
    <a href="<?= site_url('/admin/enrollments/new') ?>">
        <div class="card adm-c-green">
            <div class="adm-quick-icon"><i class="bi bi-person-workspace"></i></div>
            <div class="card__label">Seat Allotment</div>
            <div class="bb-quick__title">Allot Seat</div>
            <div class="bb-quick__desc">Assign seat + plan (Full/Half day).</div>
        </div>
    </a>
    <a href="<?= site_url('/admin/fees') ?>">
        <div class="card adm-c-amber">
            <div class="adm-quick-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="card__label">Fees</div>
            <div class="bb-quick__title">Fees Collection</div>
            <div class="bb-quick__desc">Track collections and pending fees.</div>
        </div>
    </a>
    <a href="<?= site_url('/admin/student-accounts') ?>">
        <div class="card adm-c-purple">
            <div class="adm-quick-icon"><i class="bi bi-key-fill"></i></div>
            <div class="card__label">Student Login</div>
            <div class="bb-quick__title">Create IDs / Reset</div>
            <div class="bb-quick__desc">Manage username &amp; password for students.</div>
        </div>
    </a>
</div>

<!-- ── Student Status Stats ── -->
<div class="cards" style="grid-template-columns: repeat(3, 1fr);margin-bottom:12px;">
    <a href="<?= site_url('/admin/students?status=active') ?>" style="text-decoration:none;">
        <div class="card adm-c-green">
            <div class="adm-stat-icon"><i class="bi bi-person-check-fill"></i></div>
            <div class="card__label">Active Students</div>
            <div class="card__value"><?= esc($activeStudents) ?></div>
        </div>
    </a>
    <a href="<?= site_url('/admin/students?status=dormant') ?>" style="text-decoration:none;">
        <div class="card adm-c-amber">
            <div class="adm-stat-icon"><i class="bi bi-person-dash-fill"></i></div>
            <div class="card__label">Dormant Students</div>
            <div class="card__value"><?= esc($dormantStudents) ?></div>
        </div>
    </a>
    <a href="<?= site_url('/admin/alumni') ?>" style="text-decoration:none;">
        <div class="card adm-c-purple">
            <div class="adm-stat-icon"><i class="bi bi-mortarboard-fill"></i></div>
            <div class="card__label">Total Alumni</div>
            <div class="card__value"><?= esc($alumniCount) ?></div>
        </div>
    </a>
</div>

<!-- ── Seat Stats ── -->
<div class="cards" style="grid-template-columns: repeat(4, 1fr);">
    <div class="card adm-c-blue">
        <div class="adm-stat-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
        <div class="card__label">Total Seats</div>
        <div class="card__value"><?= esc($totalSeats) ?></div>
    </div>
    <div class="card adm-c-green">
        <div class="adm-stat-icon"><i class="bi bi-sun-fill"></i></div>
        <div class="card__label">Full Day (Active)</div>
        <div class="card__value"><?= esc($fullDayCount) ?></div>
    </div>
    <div class="card adm-c-amber">
        <div class="adm-stat-icon"><i class="bi bi-brightness-high-fill"></i></div>
        <div class="card__label">Half Day AM</div>
        <div class="card__value"><?= esc($amCount) ?></div>
    </div>
    <div class="card adm-c-purple">
        <div class="adm-stat-icon"><i class="bi bi-moon-fill"></i></div>
        <div class="card__label">Half Day PM</div>
        <div class="card__value"><?= esc($pmCount) ?></div>
    </div>
</div>

<!-- ── Detail Panels ── -->
<div class="grid2">
    <section class="panel">
        <h2>Availability</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Available Seats</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Full Day</td>
                    <td><?= esc($availableForFullDay) ?></td>
                </tr>
                <tr>
                    <td>Half Day (AM)</td>
                    <td><?= esc($availableForAm) ?></td>
                </tr>
                <tr>
                    <td>Half Day (PM)</td>
                    <td><?= esc($availableForPm) ?></td>
                </tr>
            </tbody>
        </table>

        <h3>By Floor (Available)</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Floor</th>
                    <th>Total</th>
                    <th>Full Day</th>
                    <th>Half Day (AM)</th>
                    <th>Half Day (PM)</th>
                </tr>
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
        <p class="muted small">Tip: Half day seats can be shared (AM + PM) on the same seat; Full day blocks both slots.</p>
    </section>

    <section class="panel">
        <h2>Fees &amp; Timings</h2>
        <div class="muted small">Students bring their own books; library provides environment, cabin, WiFi, water, AC, etc.</div>

        <h3>Fees (INR)</h3>
        <ul class="list">
            <li>Full day (Ground): <?= esc($library->fees['FULL_DAY']['GROUND']) ?></li>
            <li>Full day (First): <?= esc($library->fees['FULL_DAY']['FIRST']) ?></li>
            <li>Half day (AM or PM): <?= esc($library->fees['HALF_DAY']) ?></li>
        </ul>

        <h3>Timings</h3>
        <ul class="list">
            <li>Full day: <?= esc($library->timings['FULL_DAY'][0]) ?> to <?= esc($library->timings['FULL_DAY'][1]) ?></li>
            <li>Half day (AM): <?= esc($library->timings['HALF_DAY']['AM'][0]) ?> to <?= esc($library->timings['HALF_DAY']['AM'][1]) ?></li>
            <li>Half day (PM): <?= esc($library->timings['HALF_DAY']['PM'][0]) ?> to <?= esc($library->timings['HALF_DAY']['PM'][1]) ?></li>
        </ul>
    </section>
</div>

<?= $this->endSection() ?>
