<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Change Seat — ' . esc($student['full_name']); ?>

<div class="pagehead">
    <h1>Change Seat</h1>
    <div class="actions">
        <a class="btn btn--ghost" href="<?= site_url('/admin/students/' . $student['id']) ?>">Back</a>
    </div>
</div>

<?php if (! empty($errors)): ?>
    <div class="alert alert--error">
        <?php foreach ($errors as $e): ?>
            <div><?= esc($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="panel" style="max-width:520px;">

    <!-- Current seat info -->
    <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:10px;padding:14px 16px;margin-bottom:20px;">
        <div class="muted small" style="margin-bottom:4px;">Student</div>
        <div style="font-weight:700;font-size:1.05rem;"><?= esc($student['full_name']) ?></div>
        <?php if (! empty($student['phone'])): ?>
            <div class="muted small"><?= esc($student['phone']) ?></div>
        <?php endif; ?>

        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <span style="font-size:.8rem;font-weight:600;color:#64748B;">Current Seat:</span>
            <span style="background:#2563EB;color:#fff;padding:2px 10px;border-radius:999px;font-weight:700;font-size:.85rem;">
                #<?= esc($current['seat_no']) ?>
                (<?= esc($current['floor'] ?? '') ?>)
            </span>
            <span style="background:#F1F5F9;color:#475569;padding:2px 10px;border-radius:999px;font-size:.8rem;">
                <?= esc($current['plan']) ?>
                <?= ! empty($current['half_day_slot']) ? '· ' . esc($current['half_day_slot']) : '' ?>
            </span>
            <span class="muted small">Since <?= esc($current['start_date'] ?? '') ?></span>
        </div>
    </div>

    <?php if (empty($availableSeats)): ?>
        <div class="alert alert--error">
            No seats are currently available for the same plan (<?= esc($current['plan']) ?>
            <?= ! empty($current['half_day_slot']) ? '· ' . esc($current['half_day_slot']) : '' ?>).
            End other enrollments first to free up seats.
        </div>
    <?php else: ?>

    <form method="post" action="<?= site_url('/admin/enrollments/change-seat/' . $student['id']) ?>">
        <?= csrf_field() ?>

        <label style="display:block;margin-bottom:14px;">
            New Seat <span style="color:#dc2626">*</span>
            <select name="new_seat_id" id="new_seat_id" required
                    style="display:block;width:100%;margin-top:6px;">
                <option value="">— Select available seat —</option>
                <?php foreach ($availableSeats as $seat): ?>
                    <option value="<?= esc($seat['id']) ?>"
                        <?= (int) old('new_seat_id') === (int) $seat['id'] ? 'selected' : '' ?>>
                        Seat #<?= esc($seat['seat_no']) ?> — <?= esc(ucfirst(strtolower($seat['floor']))) ?> Floor
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="muted small" style="margin-top:4px;">
                <?= count($availableSeats) ?> seat(s) available for
                <?= esc($current['plan']) ?><?= ! empty($current['half_day_slot']) ? ' · ' . esc($current['half_day_slot']) : '' ?>
            </div>
        </label>

        <label style="display:block;margin-bottom:14px;">
            Change Date
            <input type="date" name="change_date" id="change_date"
                   value="<?= esc(old('change_date', date('Y-m-d'))) ?>"
                   style="display:block;width:100%;margin-top:6px;">
        </label>

        <label style="display:block;margin-bottom:18px;">
            Reason (optional)
            <textarea name="reason" id="reason" rows="2"
                      placeholder="e.g. student preference, better location…"
                      style="display:block;width:100%;margin-top:6px;"><?= esc(old('reason')) ?></textarea>
        </label>

        <div style="display:flex;gap:10px;">
            <button class="btn" type="submit">Change Seat</button>
            <a class="btn btn--ghost" href="<?= site_url('/admin/students/' . $student['id']) ?>">Cancel</a>
        </div>
    </form>

    <?php endif; ?>
</div>

<?= $this->endSection() ?>
