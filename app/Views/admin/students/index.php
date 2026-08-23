<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<meta name="csrf-name" content="<?= csrf_token() ?>">
<meta name="csrf-hash" content="<?= csrf_hash() ?>">

<style>
.table tbody tr:nth-child(even) { background-color: #f8f9fa; }
.table tbody tr:hover { background-color: #e8f4fd; cursor: default; }
.table thead th { background: #f1f3f5; }
@media print {
    .print-remarks { display:table-cell !important; }
}
</style>

<?php $title = 'Students'; ?>

<?php
$_printAllUrl = site_url('/admin/students') . '?all=1'
    . ($status !== '' ? '&status=' . esc($status) : '')
    . ($q !== '' ? '&q=' . urlencode($q) : '');
?>
<div class="pagehead">
    <h1>Students<?= $printAll ? ' — Full List' : '' ?></h1>
    <div class="actions">
        <?php if (! $printAll): ?>
        <button class="btn" type="submit" form="bulk-form">Print Selected ID Cards</button>
        <a class="btn" href="<?= site_url('/admin/students/new') ?>">Add Student</a>
        <a class="btn no-print" href="<?= esc($_printAllUrl) ?>" target="_blank"><i class="bi bi-printer-fill"></i> Print All</a>
        <button class="btn no-print" onclick="window.print()"><i class="bi bi-printer-fill"></i> Print Page</button>
        <a class="btn btn--ghost no-print" href="<?= site_url('/admin/students/export-csv') . (request()->getGet('status') ? '?status=' . esc(request()->getGet('status')) : '') . (request()->getGet('q') ? (request()->getGet('status') ? '&' : '?') . 'q=' . urlencode(request()->getGet('q')) : '') ?>"><i class="bi bi-file-earmark-spreadsheet"></i> Export CSV</a>
        <?php endif; ?>
    </div>
</div>

<div class="cards" style="grid-template-columns:repeat(4,1fr)">
    <div class="card">
        <div class="card__label">Total Students</div>
        <div class="card__value"><?= $counts['all'] ?></div>
    </div>
    <div class="card">
        <div class="card__label">Active</div>
        <div class="card__value" style="color:#16a34a"><?= $counts['active'] ?></div>
    </div>
    <div class="card">
        <div class="card__label">Dormant</div>
        <div class="card__value" style="color:#d97706"><?= $counts['dormant'] ?></div>
    </div>
    <div class="card">
        <div class="card__label">Alumni</div>
        <div class="card__value" style="color:#64748b"><?= $counts['alumni'] ?></div>
    </div>
</div>

<?php if ($printAll): ?>
<p class="muted small no-print" style="margin-bottom:8px;">Showing all <?= count($students) ?> students — <a href="javascript:window.print()">Print / PDF</a> | <a href="javascript:window.close()">Close</a></p>
<script>window.onload = function(){ window.print(); };</script>
<?php endif; ?>

<!-- Filter tabs -->
<?php if (! $printAll): ?>
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
<?php endif; // ! $printAll — end filter/search section ?>

<form id="bulk-form" method="post" action="<?= site_url('/admin/idcard/bulk') ?>">
    <?= csrf_field() ?>

<table class="table" style="width:100%;border-collapse:collapse;font-size:0.9rem;">
    <thead>
        <tr style="background:#f8f9fa;border-bottom:2px solid #dee2e6;">
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Sr.No.</th>
            <th class="no-print" style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;width:36px;">
                <input type="checkbox" id="chk-all" onchange="toggleAll(this)" title="Select all">
            </th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Name</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Status</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Seat</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Admission</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Fees Paid</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;">Phone</th>
            <th class="no-print" style="padding:10px 12px;text-align:left;font-weight:600;color:#495057;white-space:nowrap;width:180px;"></th>
            <th class="print-remarks" style="display:none;padding:10px 12px;text-align:left;font-weight:600;color:#495057;">Remarks</th>
        </tr>
    </thead>
    <tbody>
        <?php $sno = 1; ?>
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
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= $sno++ ?></td>
            <td class="no-print" style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><input type="checkbox" name="ids[]" value="<?= esc($s['id']) ?>" class="row-chk"></td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;">
                <a class="link" href="<?= site_url('/admin/students/' . $s['id']) ?>"><?= esc($s['full_name']) ?></a>
                <div class="muted small">#<?= esc($s['id']) ?></div>
            </td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;">
                <span style="display:inline-block;padding:2px 9px;border-radius:999px;font-size:.78rem;font-weight:600;<?= $badgeStyle ?>">
                    <?= ucfirst(esc($stu_status)) ?>
                </span>
            </td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;">
                <?php if (! empty($s['seat_no'])): ?>
                    #<?= esc($s['seat_no']) ?> (<?= esc($s['seat_floor'] ?? '') ?>)
                <?php else: ?>
                    <span class="muted">—</span>
                <?php endif; ?>
            </td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($s['admission_date'] ?? '') ?></td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;">₹<?= esc($s['fees_paid_total'] ?? 0) ?></td>
            <td style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;"><?= esc($s['phone'] ?? '') ?></td>
            <td class="no-print" style="padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;white-space:nowrap;">
                <!-- Edit -->
                <a class="link" href="<?= site_url('/admin/students/' . $s['id'] . '/edit') ?>" title="Edit">Edit</a>
                &nbsp;·&nbsp;
                <!-- Seat: Allot if none, Change if allotted -->
                <?php if (! empty($s['seat_no'])): ?>
                <a class="link" href="<?= site_url('/admin/enrollments/change-seat/' . $s['id']) ?>" title="Change Seat">⇄ Seat</a>
                <?php else: ?>
                <a class="link" href="<?= site_url('/admin/enrollments/new?student_id=' . (int) $s['id']) ?>" title="Allot Seat">+ Seat</a>
                <?php endif; ?>
                &nbsp;·&nbsp;
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
                        <?php
                            $stuUrl  = site_url('/admin/students/' . (int)$s['id'] . '/status');
                            $stuName = esc(json_encode($s['full_name']), 'attr');
                        ?>
                        <?php if ($stu_status !== 'active'): ?>
                        <button type="button"
                                onclick="changeStudentStatus('<?= $stuUrl ?>', 'active', <?= $stuName ?>)"
                                style="display:block;width:100%;padding:8px 14px;background:none;border:none;text-align:left;cursor:pointer;font-size:.875rem;color:#166534;">
                            ✓ Mark Active
                        </button>
                        <?php endif; ?>
                        <?php if ($stu_status !== 'dormant'): ?>
                        <button type="button"
                                onclick="changeStudentStatus('<?= $stuUrl ?>', 'dormant', <?= $stuName ?>)"
                                style="display:block;width:100%;padding:8px 14px;background:none;border:none;text-align:left;cursor:pointer;font-size:.875rem;color:#854D0E;">
                            ⏸ Mark Dormant
                        </button>
                        <?php endif; ?>
                        <?php if ($stu_status !== 'alumni'): ?>
                        <button type="button"
                                onclick="changeStudentStatus('<?= $stuUrl ?>', 'alumni', <?= $stuName ?>)"
                                style="display:block;width:100%;padding:8px 14px;background:none;border:none;text-align:left;cursor:pointer;font-size:.875rem;color:#475569;border-top:1px solid #F1F5F9;">
                            🎓 Move to Alumni
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td class="print-remarks" style="display:none;padding:9px 12px;border-bottom:1px solid #f0f0f0;vertical-align:middle;min-width:140px;"></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</form>

<?php if ($pager): ?><?= $pager->links() ?><?php endif; ?>

<script>
function toggleAll(master) {
    document.querySelectorAll('.row-chk').forEach(function(chk) {
        chk.checked = master.checked;
    });
}
function toggleMenu(id) {
    var m = document.getElementById(id);
    document.querySelectorAll('[id^="menu-"]').forEach(function(el) {
        if (el.id !== id) el.style.display = 'none';
    });
    m.style.display = m.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    if (! e.target.closest('[id^="menu-"]') && e.target.getAttribute('onclick') === null) {
        document.querySelectorAll('[id^="menu-"]').forEach(function(el) {
            el.style.display = 'none';
        });
    }
});

// Status change via fetch — avoids nested-form HTML bug
function changeStudentStatus(url, newStatus, studentName) {
    if (newStatus === 'alumni') {
        if (! confirm('Move ' + studentName + ' to Alumni? This will copy their record to the Alumni list.')) {
            return;
        }
    }
    var csrfName = document.querySelector('meta[name="csrf-name"]').content;
    var csrfHash = document.querySelector('meta[name="csrf-hash"]').content;
    var fd = new FormData();
    fd.append('new_status', newStatus);
    fd.append(csrfName, csrfHash);
    fetch(url, { method: 'POST', body: fd })
        .then(function() { window.location.reload(); })
        .catch(function() { window.location.reload(); });
}
</script>

<?= $this->endSection() ?>
