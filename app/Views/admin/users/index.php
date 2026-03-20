<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<div class="pagehead">
    <h1>Admin Users</h1>
    <div class="actions">
        <a class="btn" href="<?= site_url('/admin/users/new') ?>">Add Admin</a>
    </div>
</div>

<div class="panel">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Status</th>
                <th>Last Login</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= esc($u['id']) ?></td>
                <td><?= esc($u['name']) ?></td>
                <td><?= esc($u['username']) ?></td>
                <td><?= esc($u['status']) ?></td>
                <td><?= esc($u['last_login_at'] ?? '-') ?></td>
                <td style="text-align:right">
                    <a class="btn btn--tiny btn--ghost" href="<?= site_url('/admin/users/' . (int) $u['id'] . '/edit') ?>">Edit</a>
                    <a class="btn btn--tiny" href="<?= site_url('/admin/users/' . (int) $u['id'] . '/reset') ?>">Reset Password</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>