<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<style>
.table tbody tr:nth-child(even) { background-color: #f8f9fa; }
.table tbody tr:hover { background-color: #e8f4fd; cursor: default; }
.table thead th { background: #f1f3f5; }
</style>

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
    <table class="table" style="width:100%;border-collapse:collapse;font-size:0.9rem;">
        <thead>
            <tr style="background:#f8f9fa;border-bottom:2px solid #dee2e6;">
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">#</th>
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Student Name</th>
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Phone</th>
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Template</th>
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Status</th>
                <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Sent At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $i => $log): ?>
            <tr>
                <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= ($page - 1) * $perPage + $i + 1 ?></td>
                <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($log['student_name']) ?></td>
                <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($log['phone']) ?></td>
                <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><code><?= esc($log['template_name']) ?></code></td>
                <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;">
                    <?php if ($log['status'] === 'sent'): ?>
                        <span style="display:inline-block;background:#16a34a;color:#fff;padding:2px 10px;border-radius:20px;font-size:0.78rem;">sent</span>
                    <?php else: ?>
                        <span style="display:inline-block;background:#dc2626;color:#fff;padding:2px 10px;border-radius:20px;font-size:0.78rem;"
                              title="<?= esc($log['error_message'] ?? '') ?>">failed</span>
                    <?php endif; ?>
                </td>
                <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;" class="muted small"><?= esc($log['sent_at']) ?></td>
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
