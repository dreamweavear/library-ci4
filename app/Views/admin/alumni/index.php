<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Alumni'; ?>

<div class="pagehead">
    <h1>Alumni <span class="muted small" style="font-weight:400;">(<?= esc($total) ?> total)</span></h1>
    <div class="actions">
        <a class="btn" href="<?= site_url('/admin/alumni/new') ?>">+ Add Alumni</a>
        <button class="btn no-print" onclick="window.print()"><i class="bi bi-printer-fill"></i> Print / PDF</button>
        <a class="btn btn--ghost no-print" href="<?= site_url('/admin/alumni/import') ?>">Import CSV</a>
        <a class="btn btn--ghost no-print" href="<?= site_url('/admin/alumni/export') ?>">Export CSV</a>
    </div>
</div>

<form class="search" method="get" action="<?= site_url('/admin/alumni') ?>">
    <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Search name or phone">
    <button class="btn" type="submit">Search</button>
    <?php if ($q !== ''): ?>
        <a class="btn btn--ghost" href="<?= site_url('/admin/alumni') ?>">Clear</a>
    <?php endif; ?>
</form>

<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Phone</th>
            <th>DOB</th>
            <th>Preparing For</th>
            <th>Adm. Date</th>
            <th>Left Date</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($alumni)): ?>
        <tr>
            <td colspan="8" class="muted" style="text-align:center;padding:24px;">No alumni found.</td>
        </tr>
        <?php endif; ?>
        <?php foreach ($alumni as $a): ?>
        <tr>
            <td class="muted small"><?= esc($a['id']) ?></td>
            <td>
                <?= esc($a['full_name']) ?>
                <?php if (! empty($a['student_id'])): ?>
                    <div class="muted small">Student #<?= esc($a['student_id']) ?></div>
                <?php endif; ?>
            </td>
            <td><?= esc($a['phone'] ?? '—') ?></td>
            <td><?= ! empty($a['dob']) ? date('d M Y', strtotime($a['dob'])) : '—' ?></td>
            <td><?= esc($a['preparing_for'] ?? '—') ?></td>
            <td><?= esc($a['admission_date'] ?? '—') ?></td>
            <td><?= esc($a['left_date'] ?? '—') ?></td>
            <td style="white-space:nowrap;">
                <a class="link" href="<?= site_url('/admin/alumni/' . $a['id'] . '/edit') ?>">Edit</a>
                &nbsp;·&nbsp;
                <form method="post" action="<?= site_url('/admin/alumni/' . $a['id'] . '/readmit') ?>"
                      style="display:inline;"
                      onsubmit="return confirm('Re-admit <?= esc(addslashes($a['full_name'])) ?> as an active student?');">
                    <?= csrf_field() ?>
                    <button type="submit" style="background:none;border:none;color:var(--brand);cursor:pointer;font-size:1rem;font-weight:600;padding:0;">
                        Re-admit
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $pager->links() ?>

<?= $this->endSection() ?>
