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
        <select name="student_id" class="form-select" required>
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
        Batch
        <select name="batch" id="batch" class="form-select" required>
            <option value="">Select batch</option>
            <option value="AM" <?= $oldBatch === 'AM' ? 'selected' : '' ?>>AM — Half Day (07:00 AM – 02:00 PM)</option>
            <option value="PM" <?= $oldBatch === 'PM' ? 'selected' : '' ?>>PM — Half Day (02:00 PM – 09:00 PM)</option>
            <option value="FULL" <?= $oldBatch === 'FULL' ? 'selected' : '' ?>>FULL — Full Day (07:00 AM – 09:00 PM)</option>
        </select>
    </label>

    <label>
        Seat
        <select name="seat_id" id="seat_id" class="form-select" required <?= $oldBatch === '' ? 'disabled' : '' ?>>
            <?php if ($oldBatch === ''): ?>
                <option value="">Select batch first</option>
            <?php else: ?>
                <option value=""><?= empty($seats) ? 'No seats available for this batch' : 'Select seat' ?></option>
                <?php foreach ($seats as $seat): ?>
                    <option value="<?= esc($seat['id']) ?>" <?= (string) old('seat_id') === (string) $seat['id'] ? 'selected' : '' ?>>
                        <?= esc($seat['label']) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <div class="muted small" id="seat_hint">Fee for Full Day is based on seat floor. Half Day fee is fixed.</div>
    </label>

    <label>
        Start Date
        <input type="date" name="start_date" value="<?= esc(old('start_date', date('Y-m-d'))) ?>" required>
    </label>

    <div class="form__actions">
        <button class="btn" type="submit">Allot Seat</button>
    </div>
</form>

<script>
(function () {
    var batchSel = document.getElementById('batch');
    var seatSel  = document.getElementById('seat_id');
    var url      = <?= json_encode(site_url('admin/enrollments/available-seats')) ?>;

    function setPlaceholder(text) {
        seatSel.innerHTML = '';
        var o = document.createElement('option');
        o.value = '';
        o.textContent = text;
        seatSel.appendChild(o);
    }

    batchSel.addEventListener('change', function () {
        var batch = batchSel.value;
        if (!batch) {
            setPlaceholder('Select batch first');
            seatSel.disabled = true;
            return;
        }
        setPlaceholder('Loading seats…');
        seatSel.disabled = true;

        fetch(url + '?batch=' + encodeURIComponent(batch), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        .then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function (seats) {
            if (batchSel.value !== batch) return; // batch changed while loading
            setPlaceholder(seats.length ? 'Select seat (' + seats.length + ' available)' : 'No seats available for this batch');
            seats.forEach(function (s) {
                var o = document.createElement('option');
                o.value = s.id;
                o.textContent = s.label;
                seatSel.appendChild(o);
            });
            seatSel.disabled = false;
        })
        .catch(function () {
            setPlaceholder('Could not load seats — please retry');
        });
    });
})();
</script>

<?= $this->endSection() ?>

