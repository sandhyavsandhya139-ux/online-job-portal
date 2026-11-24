<?php
$pageTitle = 'Job Portal Home';
require_once __DIR__ . '/includes/header.php';
$jobs = db()->query('SELECT * FROM jobs ORDER BY created_at DESC')->fetch_all(MYSQLI_ASSOC);
?>
<section class="hero">
    <div class="container hero-grid">
        <div>
            <h1>Find the job that fits your future.</h1>
            <p>JobPortal is a modern recruitment platform where job seekers discover opportunities and employers hire faster with less friction.</p>
            <div class="hero-actions">
                <a href="jobs.php" class="btn btn-primary">Explore Jobs</a>
                <a href="login.php" class="btn btn-secondary">Login / Register</a>
            </div>
        </div>
        <div class="hero-card">
            <h3>Live hiring snapshot</h3>
            <div class="hero-stat-grid">
                <div class="stat-box">
                    <strong>12k+</strong>
                    <span>Job seekers</span>
                </div>
                <div class="stat-box">
                    <strong>3.5k</strong>
                    <span>Employers</span>
                </div>
                <div class="stat-box">
                    <strong>480</strong>
                    <span>Open roles</span>
                </div>
                <div class="stat-box">
                    <strong>96%</strong>
                    <span>Hiring success</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>Why job seekers love JobPortal</h2>
            <p class="muted">Everything needed to search, apply, and advance your career in one place.</p>
        </div>
        <div class="feature-grid">
            <article class="card">
                <div class="icon">01</div>
                <h3>Smart job search</h3>
                <p>Filter by role, salary, location, or job type to discover the right opportunity quickly.</p>
            </article>
            <article class="card">
                <div class="icon">02</div>
                <h3>Profile built for hiring</h3>
                <p>Showcase skills, resumes, and work history so recruiters can make faster decisions.</p>
            </article>
            <article class="card">
                <div class="icon">03</div>
                <h3>Recruiter-friendly tools</h3>
                <p>Post jobs, review applications, and track the progress of every candidate from one dashboard.</p>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>Featured jobs</h2>
            <p class="muted">Trending roles available right now.</p>
        </div>
        <div class="job-grid">
            <?php foreach (array_slice($jobs, 0, 3) as $job): ?>
                <article class="job-card">
                    <div class="content">
                        <span class="badge"><?php echo htmlspecialchars($job['type']); ?></span>
                        <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                        <p class="muted"><?php echo htmlspecialchars($job['company']); ?></p>
                        <div class="job-meta">
                            <span><?php echo htmlspecialchars($job['location']); ?></span>
                            <span><?php echo htmlspecialchars($job['salary']); ?></span>
                        </div>
                        <p><?php echo htmlspecialchars($job['description']); ?></p>
                        <a href="jobs.php" class="btn btn-primary">View details</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="stats-grid">
            <div class="panel">
                <h3>250+</h3>
                <p>Active job listings</p>
            </div>
            <div class="panel">
                <h3>35%</h3>
                <p>Faster hiring cycle</p>
            </div>
            <div class="panel">
                <h3>4.9/5</h3>
                <p>User satisfaction</p>
            </div>
            <div class="panel">
                <h3>24/7</h3>
                <p>Application support</p>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
