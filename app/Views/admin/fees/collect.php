<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $errors = $errors ?? []; ?>

<div class="pagehead">
    <h1>Collect Fee</h1>
    <div class="actions">
        <a class="btn btn--ghost" href="<?= site_url('/admin/fees') ?>">Back</a>
    </div>
</div>

<?php if (! empty($errors)): ?>
    <div class="alert alert--error">
        <?php foreach ($errors as $e): ?>
            <div><?= esc($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="panel form">
    <form method="post" action="<?= site_url('/admin/fees/collect') ?>">
        <?= csrf_field() ?>

        <label>
            Student
            <select name="student_id" required>
                <option value="">Select student...</option>
                <?php foreach ($students as $s): ?>
                    <option value="<?= esc($s['id']) ?>" <?= (string) old('student_id') === (string) $s['id'] ? 'selected' : '' ?>>
                        #<?= esc($s['id']) ?> · <?= esc($s['full_name']) ?><?= ! empty($s['phone']) ? ' (' . esc($s['phone']) . ')' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Fee Type
            <?php $t = (string) old('type', 'MONTHLY'); ?>
            <select name="type" required>
                <option value="MONTHLY" <?= $t === 'MONTHLY' ? 'selected' : '' ?>>MONTHLY</option>
                <option value="ADMISSION" <?= $t === 'ADMISSION' ? 'selected' : '' ?>>ADMISSION</option>
            </select>
        </label>

        <label>
            Month (for monthly)
            <input type="month" name="for_month" value="<?= esc(old('for_month', $defaultMonth)) ?>">
            <div class="muted small">For MONTHLY: select month. For ADMISSION: leave blank.</div>
        </label>

        <label>
            Paid On
            <input type="date" name="paid_on" value="<?= esc(old('paid_on', $defaultPaidOn)) ?>" required>
        </label>

        <label>
            Amount
            <input type="number" name="amount" min="1" step="1" value="<?= esc(old('amount', '')) ?>" placeholder="Enter amount" required>
        </label>

        <label>
            Notes (optional)
            <input type="text" name="notes" value="<?= esc(old('notes', '')) ?>" placeholder="Optional note">
        </label>

        <div class="form__actions">
            <button class="btn" type="submit">Generate Receipt</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

