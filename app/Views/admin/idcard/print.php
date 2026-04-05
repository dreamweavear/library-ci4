<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($title) ?></title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; background: #D1D5DB; }

/* ── Screen controls ── */
.screen-controls {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 24px;
    background: #fff;
    border-bottom: 1px solid #E2E8F0;
}
.screen-controls h1 { font-size: 1rem; font-weight: 700; color: #1E293B; flex: 1; }
.btn-ctrl {
    padding: 9px 20px;
    border-radius: 8px;
    font-size: .875rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    border: 1px solid #D1D5DB;
    background: #fff;
    color: #374151;
}
.btn-ctrl--primary { background: #2563EB; color: #fff; border-color: #2563EB; }

/* ── A4 sheet ── */
.a4-sheet {
    width: 210mm;
    min-height: 297mm;
    margin: 20px auto;
    padding: 10mm;
    background: #fff;
    box-shadow: 0 4px 24px rgba(0,0,0,.18);
}

/* ── Card grid: 2 cols ── */
.card-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 5mm;
}

/* ═══════════════════════════════════════
   ID CARD  —  88.9 mm × 60 mm
═══════════════════════════════════════ */
.id-card {
    width: 100%;
    height: 60mm;
    border: 1.5px solid #BFDBFE;
    border-radius: 2.5mm;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    background: #fff;
    page-break-inside: avoid;
    break-inside: avoid;
}

/* ── HEADER ── */
.id-card__hdr {
    background: #1a5fa0;
    display: flex;
    align-items: center;
    padding: 2mm 2.5mm;
    gap: 2mm;
    flex-shrink: 0;
    min-height: 13mm;
}
.id-card__logo-wrap {
    width: 11mm;
    height: 11mm;
    border-radius: 50%;
    background: #fff;
    border: 1.5px solid rgba(255,255,255,.50);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}
.id-card__logo-wrap img { width: 100%; height: 100%; object-fit: cover; }
.id-card__logo-abbr {
    font-size: 7pt;
    font-weight: 900;
    color: #1a5fa0;
    letter-spacing: -.5px;
}
.id-card__hdr-text {
    flex: 1;
    text-align: center;
    color: #fff;
}
/* Institute name — bigger, visible from distance */
.id-card__hdr-name { font-size: 11.5pt; font-weight: 800; line-height: 1.15; letter-spacing: .01em; }
/* Subtitle — same small size as before */
.id-card__hdr-sub  { font-size: 5.5pt; opacity: .88; margin-top: .5mm; }

/* ── BODY ── */
.id-card__body {
    flex: 1;
    display: flex;
    overflow: hidden;
}

/* Left: fields */
.id-card__fields {
    flex: 1;
    padding: 1mm 2mm 1.5mm 2.5mm;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    gap: .55mm;
    border-right: 1px solid #E2E8F0;
}

.id-card__student-id-bar {
    text-align: center;
    padding: 1mm 0;
    margin-bottom: .8mm;
}
.id-card__student-id-bar span {
    background: #D97706;
    color: #fff;
    font-size: 6pt;
    font-weight: 800;
    letter-spacing: .10em;
    padding: 1px 8px;
    border-radius: 3px;
    display: inline-block;
}

.id-card__field {
    display: flex;
    align-items: baseline;
    gap: 1mm;
    font-size: 6pt;
    line-height: 1.35;
}
.id-card__field-lbl {
    color: #374151;
    font-weight: 700;       /* bold labels */
    white-space: nowrap;
    flex-shrink: 0;
    min-width: 13mm;
}
.id-card__field-val {
    color: #1E293B;
    font-weight: 600;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
/* Address field — wraps to max 2 lines, photo never hidden */
.id-card__field--address .id-card__field-val {
    white-space: normal;
    overflow: hidden;
    text-overflow: clip;
    word-break: break-word;
    font-size: 5.5pt;
    line-height: 1.2;
    max-width: 42mm;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
/* Name row — bigger value, label visible and bold */
.id-card__field--name .id-card__field-val { font-size: 9.5pt; font-weight: 700; }

/* Right: photo */
.id-card__photo-col {
    width: 22mm;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2mm;
}
.id-card__photo {
    width: 18mm;
    height: 22mm;
    object-fit: cover;
    border-radius: 1.5mm;
    border: 1px solid #CBD5E1;
}
.id-card__no-photo {
    width: 18mm;
    height: 22mm;
    border-radius: 1.5mm;
    background: #F1F5F9;
    border: 1px dashed #CBD5E1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16pt;
    color: #94A3B8;
}

/* ── FOOTER ── */
.id-card__ftr {
    background: #1a5fa0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5mm 2.5mm;
    flex-shrink: 0;
    min-height: 8mm;
}
.id-card__ftr-addr {
    color: #fff;
    font-size: 7pt;
    font-weight: 600;
    line-height: 1.6;
}
.id-card__ftr-url {
    font-size: 6.5pt;
    color: #D97706;
    font-weight: 700;
    text-decoration: underline;
    text-underline-offset: 1px;
}
.id-card__sig-box {
    text-align: center;
    min-width: 20mm;
}
.id-card__sig-name {
    font-size: 4.5pt;
    color: rgba(255,255,255,.85);
    border-top: 1px solid rgba(255,255,255,.50);
    padding-top: .8mm;
    margin-top: 3mm;
    white-space: nowrap;
}

/* ── Print ── */
@media print {
    @page { size: A4 portrait; margin: 10mm; }
    body { background: none; }
    .screen-controls { display: none !important; }
    .a4-sheet { margin: 0; padding: 0; box-shadow: none; width: 100%; min-height: auto; }
    .id-card__hdr,
    .id-card__ftr,
    .id-card__student-id-bar span { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
}
</style>
</head>
<body>

<div class="screen-controls">
    <h1><?= esc($title) ?></h1>
    <a class="btn-ctrl" href="<?= site_url('admin/students') ?>">← Back</a>
    <button class="btn-ctrl btn-ctrl--primary" onclick="window.print()">🖨 Print / Save PDF</button>
</div>

<div class="a4-sheet">
    <div class="card-grid">
        <?php foreach ($cards as $student): ?>
        <div class="id-card">

            <!-- HEADER: logo + institute name + subtitle only -->
            <div class="id-card__hdr">
                <div class="id-card__logo-wrap">
                    <img src="<?= base_url('assets/logo.webp') ?>"
                         alt="Logo"
                         onerror="this.onerror=null;this.src='<?= base_url('assets/logo.jpg') ?>'">
                </div>
                <div class="id-card__hdr-text">
                    <div class="id-card__hdr-name">Brilliant Brains Library</div>
                    <div class="id-card__hdr-sub">Study &amp; Reading Centre, Rewa (M.P.)</div>
                </div>
            </div>

            <!-- BODY -->
            <div class="id-card__body">
                <div class="id-card__fields">

                    <!-- STUDENT ID label — text-width only, centered -->
                    <div class="id-card__student-id-bar"><span>STUDENT ID</span></div>

                    <!-- Name (label hidden, value big) -->
                    <div class="id-card__field id-card__field--name">
                        <span class="id-card__field-lbl">Name&nbsp;:</span>
                        <span class="id-card__field-val"><?= esc($student['full_name']) ?></span>
                    </div>

                    <div class="id-card__field">
                        <span class="id-card__field-lbl">Aadhar&nbsp;:</span>
                        <span class="id-card__field-val"><?= ! empty($student['aadhar_number']) ? esc($student['aadhar_number']) : '—' ?></span>
                    </div>

                    <?php if (! empty($student['seat_no'])): ?>
                    <div class="id-card__field">
                        <span class="id-card__field-lbl">Seat No&nbsp;:</span>
                        <span class="id-card__field-val">#<?= esc($student['seat_no']) ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (! empty($student['admission_date'])): ?>
                    <div class="id-card__field">
                        <span class="id-card__field-lbl">Adm. Date&nbsp;:</span>
                        <span class="id-card__field-val"><?= esc($student['admission_date']) ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (! empty($student['address'])): ?>
                    <div class="id-card__field id-card__field--address">
                        <span class="id-card__field-lbl">Address&nbsp;:</span>
                        <span class="id-card__field-val"><?= esc($student['address']) ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (! empty($student['phone'])): ?>
                    <div class="id-card__field">
                        <span class="id-card__field-lbl">Mobile&nbsp;:</span>
                        <span class="id-card__field-val"><?= esc($student['phone']) ?></span>
                    </div>
                    <?php endif; ?>

                </div>

                <!-- Photo -->
                <div class="id-card__photo-col">
                    <?php if (! empty($student['photo']) && file_exists(FCPATH . ltrim($student['photo'], '/'))): ?>
                        <img class="id-card__photo"
                             src="<?= base_url(esc($student['photo'])) ?>"
                             alt="<?= esc($student['full_name']) ?>">
                    <?php else: ?>
                        <div class="id-card__no-photo">&#128100;</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="id-card__ftr">
                <div class="id-card__ftr-addr">
                    Alld. Road, Rewa (M.P.)<br>
                    <span class="id-card__ftr-url">&#127760; www.brilliantbrains.vindhy.com</span>
                </div>
                <div class="id-card__sig-box">
                    <div class="id-card__sig-name"><?= esc($student['full_name']) ?><br>Signature</div>
                </div>
            </div>

        </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
