<?= $this->extend('admin/_layout') ?>
<?= $this->section('content') ?>

<?php $title = "Bulk WhatsApp"; ?>

<div class="pagehead">
    <h1>Bulk WhatsApp</h1>
</div>

<form method="post" action="<?= site_url('/admin/bulk-whatsapp/send') ?>" id="bulkForm">
    <?= csrf_field() ?>

    <section class="panel" style="margin-bottom:16px;">
        <h2>Step 1 — Select Recipients</h2>

        <div style="margin-bottom:12px;">
            <label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="radio" name="filter" value="all" id="filterAll" checked onchange="toggleStudentList()">
                <strong>All Active Students</strong>
                <span class="muted small">(<?= count($students) ?> students)</span>
            </label>
        </div>
        <div style="margin-bottom:12px;">
            <label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="radio" name="filter" value="selected" id="filterSelected" onchange="toggleStudentList()">
                <strong>Select by Name</strong>
            </label>
        </div>

        <div id="studentList" style="display:none; max-height:300px; overflow-y:auto; border:1px solid var(--border); border-radius:6px; padding:10px;">
            <div style="margin-bottom:8px;">
                <input type="text" id="studentSearch" placeholder="Search student..." style="width:100%;padding:6px 10px;border:1px solid var(--border);border-radius:4px;" oninput="filterStudents()">
            </div>
            <div style="margin-bottom:6px;">
                <button type="button" class="btn btn--ghost" style="font-size:0.8rem;padding:3px 8px;" onclick="selectAll()">Select All</button>
                <button type="button" class="btn btn--ghost" style="font-size:0.8rem;padding:3px 8px;" onclick="deselectAll()">Deselect All</button>
                <span class="muted small" id="selectedCount" style="margin-left:8px;">0 selected</span>
            </div>
            <div id="studentCheckboxes">
                <?php foreach ($students as $s): ?>
                <label style="display:flex;align-items:center;gap:8px;padding:4px 2px;cursor:pointer;" class="student-item">
                    <input type="checkbox" name="student_ids[]" value="<?= esc($s['id']) ?>" class="student-cb" onchange="updateCount()">
                    <span><?= esc($s['full_name']) ?></span>
                    <span class="muted small"><?= esc($s['phone'] ?? '') ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="panel" style="margin-bottom:16px;">
        <h2>Step 2 — Select Template</h2>
        <label>
            Template
            <select name="template_name" required onchange="toggleOccasionField(this.value)">
                <option value="">— Choose template —</option>
                <?php foreach ($templates as $key => $label): ?>
                <option value="<?= esc($key) ?>"><?= esc($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <div id="occasionField" style="display:none;margin-top:10px;">
            <label>
                Occasion Name (e.g. Diwali, Holi, Eid, New Year)
                <input type="text" name="occasion" id="occasion" placeholder="e.g. Diwali">
            </label>
        </div>
        <div class="muted small" style="margin-top:6px;">Each message will use: <em>student full_name</em> and <em>Brilient Brains Library</em> as parameters (or occasion name for Seasonal Greetings).</div>
    </section>

    <div class="form__actions">
        <button type="submit" class="btn" onclick="return confirmSend()">Send WhatsApp Messages</button>
    </div>
</form>

<script>
function toggleStudentList() {
    var show = document.getElementById('filterSelected').checked;
    document.getElementById('studentList').style.display = show ? 'block' : 'none';
}

function updateCount() {
    var checked = document.querySelectorAll('.student-cb:checked').length;
    document.getElementById('selectedCount').textContent = checked + ' selected';
}

function selectAll() {
    document.querySelectorAll('.student-cb').forEach(function(cb) { cb.checked = true; });
    updateCount();
}

function deselectAll() {
    document.querySelectorAll('.student-cb').forEach(function(cb) { cb.checked = false; });
    updateCount();
}

function filterStudents() {
    var q = document.getElementById('studentSearch').value.toLowerCase();
    document.querySelectorAll('.student-item').forEach(function(item) {
        item.style.display = item.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

function toggleOccasionField(val) {
    document.getElementById('occasionField').style.display = (val === 'seasonal_greetings') ? 'block' : 'none';
}

function confirmSend() {
    var isSelected = document.getElementById('filterSelected').checked;
    var count = isSelected
        ? document.querySelectorAll('.student-cb:checked').length
        : <?= count($students) ?>;
    if (isSelected && count === 0) {
        alert('Please select at least one student.');
        return false;
    }
    return confirm('Send WhatsApp to ' + count + ' student(s)? This may take a while.');
}
</script>

<?= $this->endSection() ?>
