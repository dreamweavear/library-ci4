<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Students'; ?>

<div class="pagehead">
    <h1>Students</h1>
    <div class="actions">
        <a class="btn" href="<?= site_url('/admin/students/new') ?>">Add Student</a>
    </div>
</div>

<form class="search" method="get" action="<?= site_url('/admin/students') ?>">
    <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Search name or phone">
    <button class="btn" type="submit">Search</button>
    <?php if ($q !== ''): ?>
        <a class="btn btn--ghost" href="<?= site_url('/admin/students') ?>">Clear</a>
    <?php endif; ?>
</form>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Seat</th>
            <th>Admission</th>
            <th>Fees Paid</th>
            <th>Phone</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $s): ?>
        <tr>
            <td><?= esc($s['id']) ?></td>
            <td><?= esc($s['full_name']) ?></td>
            <td>
                <?php if (! empty($s['seat_no'])): ?>
                    #<?= esc($s['seat_no']) ?> (<?= esc($s['seat_floor'] ?? '') ?>)
                <?php else: ?>
                    <span class="muted">—</span>
                <?php endif; ?>
            </td>
            <td><?= esc($s['admission_date'] ?? '') ?></td>
            <td>₹<?= esc($s['fees_paid_total'] ?? 0) ?></td>
            <td><?= esc($s['phone'] ?? '') ?></td>
            <td><a class="link" href="<?= site_url('/admin/students/' . $s['id']) ?>">View</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $pager->links() ?>

<?= $this->endSection() ?>
