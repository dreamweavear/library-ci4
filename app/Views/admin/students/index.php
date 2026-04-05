<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = 'Students'; ?>

<div class="pagehead">
    <h1>Students</h1>
    <div class="actions">
        <button class="btn" type="submit" form="bulk-form">Print Selected ID Cards</button>
        <a class="btn" href="<?= site_url('/admin/students/new') ?>">Add Student</a>
    </div>
</div>

<!-- Filter tabs -->
<div style="display:flex;gap:6px;margin-bottom:14px;flex-wrap:wrap;">
    <?php
    $tabs = [
        ''        => 'All',
        'active'  => 'Active',
        'dormant' => 'Dormant',
        'alumni'  => 'Alumni',
    ];
    foreach ($tabs as $val => $label):
        $isActive = ($status === $val);
        $url = site_url('/admin/students') . ($val !== '' ? '?status=' . $val : '') . ($q !== '' ? ($val !== '' ? '&' : '?') . 'q=' . urlencode($q) : '');
        $countKey = $val !== '' ? $val : 'all';
        $cnt = $counts[$countKey] ?? 0;
    ?>
    <a href="<?= esc($url) ?>"
       style="padding:6px 14px;border-radius:8px;font-size:.875rem;font-weight:600;text-decoration:none;border:1.5px solid <?= $isActive ? '#2563EB' : '#E2E8F0' ?>;background:<?= $isActive ? '#2563EB' : '#fff' ?>;color:<?= $isActive ? '#fff' : '#64748B' ?>;">
        <?= esc($label) ?> <span style="opacity:.75;font-weight:400;">(<?= $cnt ?>)</span>
    </a>
    <?php endforeach; ?>
</div>

<form class="search" method="get" action="<?= site_url('/admin/students') ?>">
    <?php if ($status !== ''): ?>
        <input type="hidden" name="status" value="<?= esc($status) ?>">
    <?php endif; ?>
    <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Search name or phone">
    <button class="btn" type="submit">Search</button>
    <?php if ($q !== ''): ?>
        <a class="btn btn--ghost" href="<?= site_url('/admin/students') . ($status !== '' ? '?status=' . esc($status) : '') ?>">Clear</a>
    <?php endif; ?>
</form>

<form id="bulk-form" method="post" action="<?= site_url('/admin/idcard/bulk') ?>">
    <?= csrf_field() ?>

<table class="table">
    <thead>
        <tr>
            <th style="width:36px;">
                <input type="checkbox" id="chk-all" onchange="toggleAll(this)" title="Select all">
            </th>
            <th>Name</th>
            <th>Status</th>
            <th>Seat</th>
            <th>Admission</th>
            <th>Fees Paid</th>
            <th>Phone</th>
            <th style="width:180px;"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $s): ?>
        <?php
            $stu_status = $s['status'] ?? 'active';
            $badgeStyle = match($stu_status) {
                'active'  => 'background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;',
                'dormant' => 'background:#FEF9C3;color:#854D0E;border:1px solid #FDE68A;',
                'alumni'  => 'background:#F1F5F9;color:#475569;border:1px solid #E2E8F0;',
                default   => 'background:#F1F5F9;color:#475569;',
            };
        ?>
        <tr>
            <td><input type="checkbox" name="ids[]" value="<?= esc($s['id']) ?>" class="row-chk"></td>
            <td>
                <a class="link" href="<?= site_url('/admin/students/' . $s['id']) ?>"><?= esc($s['full_name']) ?></a>
                <div class="muted small">#<?= esc($s['id']) ?></div>
            </td>
            <td>
                <span style="display:inline-block;padding:2px 9px;border-radius:999px;font-size:.78rem;font-weight:600;<?= $badgeStyle ?>">
                    <?= ucfirst(esc($stu_status)) ?>
                </span>
            </td>
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
            <td style="white-space:nowrap;">
                <!-- Edit -->
                <a class="link" href="<?= site_url('/admin/students/' . $s['id'] . '/edit') ?>" title="Edit">Edit</a>
                &nbsp;·&nbsp;
                <!-- Change Seat (only if seat allotted) -->
                <?php if (! empty($s['seat_no'])): ?>
                <a class="link" href="<?= site_url('/admin/enrollments/change-seat/' . $s['id']) ?>" title="Change Seat">⇄ Seat</a>
                &nbsp;·&nbsp;
                <?php endif; ?>
                <!-- ID Card -->
                <a class="link" href="<?= site_url('/admin/idcard/print/' . $s['id']) ?>" title="Print ID Card">ID Card</a>
                &nbsp;·&nbsp;
                <!-- Status dropdown -->
                <div style="display:inline-block;position:relative;">
                    <button type="button"
                            style="background:none;border:none;color:var(--brand);font-size:1rem;cursor:pointer;padding:0;font-weight:600;"
                            onclick="toggleMenu('menu-<?= $s['id'] ?>')">Status ▾</button>
                    <div id="menu-<?= $s['id'] ?>"
                         style="display:none;position:absolute;right:0;top:100%;background:#fff;border:1px solid #E2E8F0;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.10);z-index:100;min-width:150px;padding:4px 0;">
                        <?php if ($stu_status !== 'active'): ?>
                        <form method="post" action="<?= site_url('/admin/students/' . $s['id'] . '/status') ?>" style="margin:0;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="new_status" value="active">
                            <button type="submit" style="display:block;width:100%;padding:8px 14px;background:none;border:none;text-align:left;cursor:pointer;font-size:.875rem;color:#166534;">
                                ✓ Mark Active
                            </button>
                        </form>
                        <?php endif; ?>
                        <?php if ($stu_status !== 'dormant'): ?>
                        <form method="post" action="<?= site_url('/admin/students/' . $s['id'] . '/status') ?>" style="margin:0;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="new_status" value="dormant">
                            <button type="submit" style="display:block;width:100%;padding:8px 14px;background:none;border:none;text-align:left;cursor:pointer;font-size:.875rem;color:#854D0E;">
                                ⏸ Mark Dormant
                            </button>
                        </form>
                        <?php endif; ?>
                        <?php if ($stu_status !== 'alumni'): ?>
                        <form method="post" action="<?= site_url('/admin/students/' . $s['id'] . '/status') ?>" style="margin:0;"
                              onsubmit="return confirm('Move <?= esc(addslashes($s['full_name'])) ?> to Alumni? This will copy their record to the Alumni list.');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="new_status" value="alumni">
                            <button type="submit" style="display:block;width:100%;padding:8px 14px;background:none;border:none;text-align:left;cursor:pointer;font-size:.875rem;color:#475569;border-top:1px solid #F1F5F9;">
                                🎓 Move to Alumni
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</form>

<?= $pager->links() ?>

<script>
function toggleAll(master) {
    document.querySelectorAll('.row-chk').forEach(function(chk) {
        chk.checked = master.checked;
    });
}
function toggleMenu(id) {
    var m = document.getElementById(id);
    // Close all others first
    document.querySelectorAll('[id^="menu-"]').forEach(function(el) {
        if (el.id !== id) el.style.display = 'none';
    });
    m.style.display = m.style.display === 'none' ? 'block' : 'none';
}
// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (! e.target.closest('[id^="menu-"]') && e.target.getAttribute('onclick') === null) {
        document.querySelectorAll('[id^="menu-"]').forEach(function(el) {
            el.style.display = 'none';
        });
    }
});
</script>

<?= $this->endSection() ?>
