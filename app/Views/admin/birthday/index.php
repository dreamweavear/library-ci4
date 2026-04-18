<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = "Birthday Reminder"; ?>

<div class="pagehead">
    <h1>Birthday Reminder</h1>
    <div class="actions">
        <?php if (! empty($students)): ?>
        <form method="post" action="<?= site_url('/admin/birthday-reminder/send-all') ?>" onsubmit="return confirm('Send birthday wishes to all <?= count($students) ?> student(s)?');">
            <?= csrf_field() ?>
            <button type="submit" class="btn">Send to All (<?= count($students) ?>)</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($students)): ?>
    <div class="alert alert--success">No birthdays today. Check back tomorrow!</div>
<?php else: ?>
    <div class="alert alert--success">
        Today is <strong><?= date('d F') ?></strong> — <?= count($students) ?> student(s) celebrating their birthday today.
    </div>

    <section class="panel">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Date of Birth</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $i => $s): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td>
                        <?= esc($s['full_name']) ?>
                        <?php if (($s['_type'] ?? 'student') === 'alumni'): ?>
                            <span class="muted small" style="background:#f1f5f9;border-radius:4px;padding:1px 6px;margin-left:4px;">Alumni</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($s['phone'] ?? '—') ?></td>
                    <td><?= $s['dob'] ? date('d M Y', strtotime($s['dob'])) : '—' ?></td>
                    <td>
                        <?php if (! empty($s['phone']) && ($s['_type'] ?? 'student') === 'student'): ?>
                        <form method="post" action="<?= site_url('/admin/birthday-reminder/send/' . $s['id']) ?>" style="display:inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--ghost" style="font-size:0.8rem;padding:4px 10px;">
                                Send WhatsApp
                            </button>
                        </form>
                        <?php elseif (! empty($s['phone']) && ($s['_type'] ?? '') === 'alumni'): ?>
                        <span class="muted small">Alumni (no WA)</span>
                        <?php else: ?>
                        <span class="muted small">No phone</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
<?php endif; ?>

<?= $this->endSection() ?>
