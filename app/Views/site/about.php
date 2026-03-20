<?= $this->extend('site/_layout') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card card-soft">
            <div class="card-top-accent"></div>
            <div class="card-body p-4">
                <h2 class="h4 fw-semibold mb-2">About Brilient Brains Library</h2>
                <p class="text-body-secondary mb-3">
                    Brilient Brains Library is built for students who want a distraction-free and comfortable place to study.
                    We keep the interface simple, navigation clear, and the look inspired by a blue/sky-blue gradient theme.
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="bb-mini">
                            <div class="fw-semibold mb-1">Our Focus</div>
                            <div class="text-body-secondary small">Clean design, comfortable seating, and smooth admin management.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bb-mini">
                            <div class="fw-semibold mb-1">Timings</div>
                            <div class="text-body-secondary small">Full day 07:00–21:00 · Half day 07:00–14:00 / 14:00–21:00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card card-soft">
            <div class="card-top-accent"></div>
            <div class="card-body p-4">
                <div class="fw-semibold mb-2">Quick Links</div>
                <div class="d-grid gap-2">
                    <a class="btn btn-bb" href="<?= site_url('student/login') ?>">Student Login</a>
                    <a class="btn btn-outline-primary" href="<?= site_url('admin/login') ?>">Admin Login</a>
                </div>
                <hr class="my-4">
                <div class="text-body-secondary small">
                    Note: Login screens are UI pages (authentication can be connected later).
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

