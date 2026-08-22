<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Allot Seat'; ?>

<div class="pagehead">
    <h1>Allot Seat</h1>
    <div class="actions">
        <a class="btn btn--ghost" href="<?= site_url('/admin/enrollments') ?>">Back</a>
    </div>
</div>

<?php if (! empty($errors)): ?>
    <div class="alert alert--error">
        <?php foreach ($errors as $e): ?>
            <div><?= esc($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form class="form" method="post" action="<?= site_url('/admin/enrollments') ?>">
    <?= csrf_field() ?>

    <label>
        Student
        <select name="student_id" required>
            <option value="">Select student</option>
            <?php foreach ($students as $s): ?>
                <?php
                    $oldVal = old('student_id');
                    $preId  = (int) ($preselectedStudentId ?? 0);
                    $sel    = ($oldVal !== null)
                        ? ((string) $oldVal === (string) $s['id'])
                        : ($preId > 0 && $preId === (int) $s['id']);
                ?>
                <option value="<?= esc($s['id']) ?>" <?= $sel ? 'selected' : '' ?>>
                    <?= esc($s['full_name']) ?><?= ! empty($s['phone']) ? ' - ' . esc($s['phone']) : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Seat
        <select name="seat_id" required>
            <option value="">Select seat</option>
            <?php foreach ($seats as $seat): ?>
                <option value="<?= esc($seat['id']) ?>" <?= (string) old('seat_id') === (string) $seat['id'] ? 'selected' : '' ?>>
                    #<?= esc($seat['seat_no']) ?> (<?= esc($seat['floor']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <div class="muted small">Fee for Full Day is based on seat floor. Half Day fee is fixed.</div>
    </label>

    <label>
        Plan
        <select name="plan" required>
            <option value="FULL_DAY" <?= old('plan', 'FULL_DAY') === 'FULL_DAY' ? 'selected' : '' ?>>FULL_DAY</option>
            <option value="HALF_DAY" <?= old('plan') === 'HALF_DAY' ? 'selected' : '' ?>>HALF_DAY</option>
        </select>
    </label>

    <label>
        Half Day Slot (only for HALF_DAY)
        <select name="half_day_slot">
            <option value="">Select slot</option>
            <option value="AM" <?= old('half_day_slot') === 'AM' ? 'selected' : '' ?>>AM (07:00-14:00)</option>
            <option value="PM" <?= old('half_day_slot') === 'PM' ? 'selected' : '' ?>>PM (14:00-21:00)</option>
        </select>
    </label>

    <label>
        Start Date
        <input type="date" name="start_date" value="<?= esc(old('start_date', date('Y-m-d'))) ?>" required>
    </label>

    <div class="form__actions">
        <button class="btn" type="submit">Allot Seat</button>
    </div>
</form>

<?= $this->endSection() ?>

