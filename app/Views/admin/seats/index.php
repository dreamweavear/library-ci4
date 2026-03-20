<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Seats'; ?>

<div class="pagehead">
    <h1>Seat Map (1 to 100)</h1>
    <div class="actions">
        <a class="btn" href="<?= site_url('/admin/enrollments/new') ?>">Allot Seat</a>
    </div>
</div>

<div class="legend">
    <span class="pill pill--free">Free</span>
    <span class="pill pill--occupied">Occupied</span>
    <span class="pill pill--ground">Ground</span>
    <span class="pill pill--first">First</span>
</div>

<div class="seatgrid">
    <?php foreach ($seats as $s): ?>
        <?php
            $floor = strtoupper((string) ($s['floor'] ?? ''));
            $floorClass = $floor === 'FIRST' ? 'seat--first' : 'seat--ground';
            $seatId = (int) $s['id'];
            $assign = $seatAssignments[$seatId] ?? [];
            $hasAny = ! empty($assign);
            $statusClass = $hasAny ? 'seat--occupied' : 'seat--free';
            $full = $assign['FULL_DAY'] ?? null;
            $am = $assign['AM'] ?? null;
            $pm = $assign['PM'] ?? null;
        ?>
        <div class="seat <?= esc($floorClass) ?> <?= esc($statusClass) ?>">
            <div class="seat__no">#<?= esc($s['seat_no']) ?></div>
            <div class="seat__meta">
                <div class="muted small"><?= esc($floor) ?></div>

                <?php if ($full): ?>
                    <div class="small"><strong><?= esc($full['student_name'] ?? '') ?></strong></div>
                    <div class="muted small">FULL_DAY</div>
                <?php else: ?>
                    <div class="muted small">AM: <?= esc($am['student_name'] ?? 'Free') ?></div>
                    <div class="muted small">PM: <?= esc($pm['student_name'] ?? 'Free') ?></div>
                <?php endif; ?>
            </div>
            <form class="seat__form" method="post" action="<?= site_url('/admin/seats/' . $s['id'] . '/floor') ?>">
                <?= csrf_field() ?>
                <select name="floor">
                    <option value="GROUND" <?= $floor === 'GROUND' ? 'selected' : '' ?>>GROUND</option>
                    <option value="FIRST" <?= $floor === 'FIRST' ? 'selected' : '' ?>>FIRST</option>
                </select>
                <button class="btn btn--tiny" type="submit">Save</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>
