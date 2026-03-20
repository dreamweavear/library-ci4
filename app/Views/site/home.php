<?= $this->extend('site/_layout') ?>

<?= $this->section('content') ?>
<div class="bb-hero p-4 p-md-5 mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-lg-7">
            <div class="badge bb-badge mb-3">Blue · Sky Blue · Gradient</div>
            <h1 class="display-6 fw-semibold mb-2">Welcome to Brilient Brains Library</h1>
            <p class="lead mb-4 text-white-75">
                A clean, quiet and comfortable student library with simple and attractive design.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-bb btn-lg" href="<?= site_url('student/login') ?>">Student Login</a>
                <a class="btn btn-outline-light btn-lg" href="<?= site_url('admin/login') ?>">Admin Login</a>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card card-soft">
                <div class="card-top-accent"></div>
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bb-icon">
                            <span class="fw-bold">BB</span>
                        </div>
                        <div>
                            <div class="fw-semibold">Library Highlights</div>
                            <div class="text-body-secondary small mb-2">Simple, modern, and easy to navigate.</div>
                            <ul class="mb-0 small text-body-secondary ps-3">
                                <li>Comfortable seating</li>
                                <li>Clean UI with Bootstrap 5</li>
                                <li>Fast access to admin & student logins</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 g-md-4">
    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-top-accent"></div>
            <div class="card-body">
                <div class="fw-semibold mb-1">Study-Friendly Space</div>
                <div class="text-body-secondary">A calm environment designed for focus and consistency.</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-top-accent"></div>
            <div class="card-body">
                <div class="fw-semibold mb-1">Seat & Enrollment</div>
                <div class="text-body-secondary">Admin can manage seats, students, and enrollments easily.</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-top-accent"></div>
            <div class="card-body">
                <div class="fw-semibold mb-1">Simple Cards</div>
                <div class="text-body-secondary">Attractive but minimal card design for a clean look.</div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <div class="card card-soft">
        <div class="card-top-accent"></div>
        <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <div class="fw-semibold">New here?</div>
                <div class="text-body-secondary">Learn more about Brilient Brains Library and how it works.</div>
            </div>
            <a class="btn btn-bb" href="<?= site_url('about') ?>">About the Library</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

