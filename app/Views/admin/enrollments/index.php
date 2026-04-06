<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Enrollments'; ?>

<div class="pagehead">
    <h1>Enrollments (<?= esc($status) ?>)</h1>
    <div class="actions">
        <a class="btn" href="<?= site_url('/admin/enrollments/new') ?>">Allot Seat</a>
        <?php if ($status === 'ACTIVE'): ?>
            <a class="btn btn--ghost" href="<?= site_url('/admin/enrollments?status=ENDED') ?>">View Ended</a>
        <?php else: ?>
            <a class="btn btn--ghost" href="<?= site_url('/admin/enrollments?status=ACTIVE') ?>">View Active</a>
        <?php endif; ?>
    </div>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Sr.No</th>
            <th>ID</th>
            <th>Student</th>
            <th>Phone</th>
            <th>Seat</th>
            <th>Floor</th>
            <th>Plan</th>
            <th>Fee</th>
            <th>Start</th>
            <th>End</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php $sno = 1; ?>
        <?php foreach ($enrollments as $e): ?>
        <tr>
            <td><?= $sno++ ?></td>
            <td><?= esc($e['id']) ?></td>
            <td><a class="link" href="<?= site_url('/admin/students/' . $e['student_id']) ?>"><?= esc($e['full_name']) ?></a></td>
            <td><?= esc($e['phone'] ?? '') ?></td>
            <td><?= esc($e['seat_no']) ?></td>
            <td><?= esc($e['floor']) ?></td>
            <td>
                <?= esc($e['plan']) ?>
                <?php if (! empty($e['half_day_slot'])): ?>
                    (<?= esc($e['half_day_slot']) ?>)
                <?php endif; ?>
            </td>
            <td><?= esc($e['fee']) ?></td>
            <td><?= esc($e['start_date']) ?></td>
            <td><?= esc($e['end_date'] ?? '') ?></td>
            <td>
                <?php if ($status === 'ACTIVE'): ?>
                    <form method="post" action="<?= site_url('/admin/enrollments/' . $e['id'] . '/end') ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn--tiny btn--danger" type="submit" onclick="return confirm('End enrollment?')">End</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $pager->links() ?>

<?= $this->endSection() ?>

