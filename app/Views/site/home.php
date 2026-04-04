<?= $this->extend('site/_layout') ?>

<?= $this->section('content') ?>

<!-- ── Hero ── -->
<section class="bb-hero mb-5">
    <div class="bb-badge mb-3">
        <span class="bb-badge-dot"></span>
        Brilient Brains Library
    </div>
    <h1 class="display-5 fw-bold mb-3 text-white">
        Your Perfect <span class="bb-hero-highlight">Study Space</span> Awaits
    </h1>
    <p class="mb-4 text-white-75 mx-auto" style="max-width:480px">
        A calm, focused environment designed for students who want to achieve more.
    </p>
    <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
        <a class="btn btn-bb-solid btn-lg px-4" href="<?= site_url('student/login') ?>">
            <i class="bi bi-person-fill me-2"></i>Student Login
        </a>
        <a class="btn btn-bb-outline btn-lg px-4" href="<?= site_url('admin/login') ?>">
            <i class="bi bi-shield-lock-fill me-2"></i>Admin Login
        </a>
    </div>

    <!-- Stats row -->
    <div class="row g-3 justify-content-center" style="max-width:540px; margin:0 auto;">
        <div class="col-4">
            <div class="bb-stat-item">
                <div class="bb-stat-num">200+</div>
                <div class="bb-stat-label">Total Seats</div>
            </div>
        </div>
        <div class="col-4">
            <div class="bb-stat-item">
                <div class="bb-stat-num">07–21</div>
                <div class="bb-stat-label">Hours Open</div>
            </div>
        </div>
        <div class="col-4">
            <div class="bb-stat-item">
                <div class="bb-stat-num">2</div>
                <div class="bb-stat-label">Daily Shifts</div>
            </div>
        </div>
    </div>
</section>

<!-- ── Features ── -->
<section class="mb-5">
    <div class="text-center mb-4">
        <div class="bb-section-badge">Why Choose Us</div>
        <h2 class="fw-bold mt-2">Everything You Need to Study Better</h2>
        <p class="text-body-secondary">Designed to keep you focused and comfortable throughout the day.</p>
    </div>
    <div class="row g-3 g-md-4">
        <div class="col-md-4">
            <div class="card card-soft h-100">
                <div class="card-top-accent"></div>
                <div class="card-body p-4">
                    <div class="bb-feat-icon mb-3">
                        <i class="bi bi-bookmark-star-fill"></i>
                    </div>
                    <div class="fw-bold mb-2">Study-Friendly Space</div>
                    <div class="text-body-secondary small">A calm, distraction-free environment with comfortable seating designed for maximum focus and long study sessions.</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft h-100">
                <div class="card-top-accent"></div>
                <div class="card-body p-4">
                    <div class="bb-feat-icon mb-3">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <div class="fw-bold mb-2">Seat &amp; Enrollment</div>
                    <div class="text-body-secondary small">Admins can manage seats, enroll students, assign shifts, and track daily attendance with ease.</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft h-100">
                <div class="card-top-accent"></div>
                <div class="card-body p-4">
                    <div class="bb-feat-icon mb-3">
                        <i class="bi bi-grid-1x2-fill"></i>
                    </div>
                    <div class="fw-bold mb-2">Clean Dashboard</div>
                    <div class="text-body-secondary small">Modern, minimal interface for both students and admins. Built with Bootstrap 5 for a fast, responsive experience.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── How it Works ── -->
<section class="mb-5">
    <div class="text-center mb-4">
        <div class="bb-section-badge">Simple Steps</div>
        <h2 class="fw-bold mt-2">How It Works</h2>
        <p class="text-body-secondary">Get started in just a few simple steps.</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="bb-step-card">
                <div class="bb-step-num">01</div>
                <div class="fw-bold mb-1">Register / Enroll</div>
                <div class="text-body-secondary small">Contact the admin or get enrolled by the library management team to receive your student account.</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bb-step-card">
                <div class="bb-step-num">02</div>
                <div class="fw-bold mb-1">Choose Your Shift</div>
                <div class="text-body-secondary small">Pick a full-day or half-day shift that fits your schedule. Morning or evening, we've got you covered.</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bb-step-card">
                <div class="bb-step-num">03</div>
                <div class="fw-bold mb-1">Start Studying</div>
                <div class="text-body-secondary small">Come in, take your assigned seat, and focus. Your dashboard shows your seat info and membership details.</div>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA ── -->
<section class="mb-2">
    <div class="bb-cta-card">
        <div class="row g-3 align-items-center">
            <div class="col-md">
                <div class="fw-bold fs-5 text-white">New here? Learn More</div>
                <div class="text-white-75 small mt-1">Discover what Brilient Brains Library offers and how to get started.</div>
            </div>
            <div class="col-md-auto">
                <a class="btn btn-bb-solid" href="<?= site_url('about') ?>">
                    <i class="bi bi-arrow-right-circle-fill me-2"></i>About the Library
                </a>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
