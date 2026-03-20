<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<div class="pagehead">
    <h1>Student Login IDs</h1>
    <div class="actions">
        <a class="btn" href="<?= site_url('/admin/student-accounts/new') ?>">Create Login</a>
    </div>
</div>

<div class="panel">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Phone</th>
                <th>Username</th>
                <th>Status</th>
                <th>Last Login</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($accounts as $a): ?>
            <tr>
                <td><?= esc($a['id']) ?></td>
                <td><?= esc($a['full_name']) ?></td>
                <td><?= esc($a['phone'] ?? '-') ?></td>
                <td><?= esc($a['username']) ?></td>
                <td><?= esc($a['status']) ?></td>
                <td><?= esc($a['last_login_at'] ?? '-') ?></td>
                <td style="text-align:right">
                    <a class="btn btn--tiny" href="<?= site_url('/admin/student-accounts/' . (int) $a['id'] . '/reset') ?>">Reset</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>