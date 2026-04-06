<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = "Message Logs"; ?>

<div class="pagehead">
    <h1>Message Logs</h1>
    <div class="actions">
        <span class="muted small">Total: <?= esc($total) ?> messages</span>
    </div>
</div>

<section class="panel">
    <?php if (empty($logs)): ?>
        <div class="muted" style="padding:16px;">No messages sent yet.</div>
    <?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Phone</th>
                <th>Template</th>
                <th>Status</th>
                <th>Sent At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $i => $log): ?>
            <tr>
                <td><?= ($page - 1) * $perPage + $i + 1 ?></td>
                <td><?= esc($log['student_name']) ?></td>
                <td><?= esc($log['phone']) ?></td>
                <td><code><?= esc($log['template_name']) ?></code></td>
                <td>
                    <?php if ($log['status'] === 'sent'): ?>
                        <span style="display:inline-block;background:#16a34a;color:#fff;padding:2px 10px;border-radius:20px;font-size:0.78rem;">sent</span>
                    <?php else: ?>
                        <span style="display:inline-block;background:#dc2626;color:#fff;padding:2px 10px;border-radius:20px;font-size:0.78rem;"
                              title="<?= esc($log['error_message'] ?? '') ?>">failed</span>
                    <?php endif; ?>
                </td>
                <td class="muted small"><?= esc($log['sent_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="margin-top:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <?php if ($page > 1): ?>
            <a class="btn btn--ghost" href="?page=<?= $page - 1 ?>">Previous</a>
        <?php endif; ?>
        <span class="muted small">Page <?= $page ?> of <?= $totalPages ?></span>
        <?php if ($page < $totalPages): ?>
            <a class="btn btn--ghost" href="?page=<?= $page + 1 ?>">Next</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</section>

<?= $this->endSection() ?>
