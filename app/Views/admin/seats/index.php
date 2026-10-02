<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Seats'; ?>

<style>
/* Seat colours — inline to guarantee override */
.seat.seat--free {
    background: #DCFCE7 !important;
    border: 2px solid #22c55e !important;
}
.seat.seat--occupied {
    background: #FFE4E4 !important;
    border: 2px solid #ef4444 !important;
}
div.seat.seat--half {
    background: #FEF9C3 !important;
    border: 2px solid #eab308 !important;
}
.seat.seat--free .seat__no,
.seat.seat--free .small,
.seat.seat--free .muted,
.seat.seat--occupied .seat__no,
.seat.seat--occupied .small,
.seat.seat--occupied .muted,
.seat.seat--half .seat__no,
.seat.seat--half .small,
.seat.seat--half .muted {
    color: #1e293b !important;
}
.seat__tag {
    display: inline-block;
    margin-left: 4px;
    padding: 0 6px;
    border-radius: 999px;
    background: #eab308;
    color: #1e293b;
    font-size: .7rem;
    font-weight: 600;
    vertical-align: middle;
}
.pill--half { border-color: rgba(234,179,8,.45); }
</style>

<div class="pagehead">
    <h1>Seat Map (1 to 100)</h1>
    <div class="actions">
        <a class="btn" href="<?= site_url('/admin/enrollments/new') ?>">Allot Seat</a>
    </div>
</div>

<div class="cards">
    <div class="card">
        <div class="card__label">Total Seats</div>
        <div class="card__value"><?= $totalSeats ?></div>
    </div>
    <div class="card">
        <div class="card__label">Occupied</div>
        <div class="card__value" style="color:#ef4444"><?= $occupiedCount ?></div>
    </div>
    <div class="card">
        <div class="card__label">Half Booked (AM / PM only)</div>
        <div class="card__value" style="color:#ca8a04"><?= $halfCount ?></div>
    </div>
    <div class="card">
        <div class="card__label">Available</div>
        <div class="card__value" style="color:#16a34a"><?= $availableCount ?></div>
    </div>
</div>

<div class="legend">
    <span class="pill pill--free">Free</span>
    <span class="pill pill--occupied">Occupied</span>
    <span class="pill pill--half">Half booked</span>
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
            $full = $assign['FULL_DAY'] ?? null;
            $am = $assign['AM'] ?? null;
            $pm = $assign['PM'] ?? null;
            $halfTag = '';
            if ($full || ($am && $pm)) {
                $statusClass = 'seat--occupied';
            } elseif ($am || $pm) {
                $statusClass = 'seat--half';
                $halfTag = $am ? 'AM' : 'PM';
            } else {
                $statusClass = 'seat--free';
            }
        ?>
        <div class="seat <?= esc($floorClass) ?> <?= esc($statusClass) ?>">
            <div class="seat__no">#<?= esc($s['seat_no']) ?><?php if ($halfTag !== ''): ?><span class="seat__tag" title="<?= esc($halfTag) ?> booked"><?= esc($halfTag) ?></span><?php endif; ?></div>
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
