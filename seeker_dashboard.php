<?php
$pageTitle = 'Seeker Dashboard';
require_once __DIR__ . '/includes/header.php';
authRequired(['seeker']);
$user = currentUser();
$jobs = db()->query('SELECT * FROM jobs ORDER BY created_at DESC LIMIT 3')->fetch_all(MYSQLI_ASSOC);
$seekerId = (int)$user['id'];
$statsStmt = db()->prepare("SELECT COUNT(*) AS total, SUM(status='Shortlisted') AS shortlisted FROM applications WHERE seeker_id=?");
$statsStmt->bind_param('i', $seekerId);
$statsStmt->execute();
$stats = $statsStmt->get_result()->fetch_assoc();
$applicationCount = (int)$stats['total'];
$shortlistedCount = (int)$stats['shortlisted'];
$applicationsStmt = db()->prepare('SELECT a.id,a.status,j.title,j.company,j.location FROM applications a JOIN jobs j ON j.id=a.job_id WHERE a.seeker_id=? ORDER BY a.applied_at DESC');
$applicationsStmt->bind_param('i', $seekerId);
$applicationsStmt->execute();
$applications = $applicationsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<section class="section dashboard-page">
    <div class="container">
        <div class="dashboard-header">
            <div>
                <span class="eyebrow">JOB SEEKER WORKSPACE</span>
                <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?></h2>
                <p class="muted">Track your applications and find your next opportunity.</p>
            </div>
            <div class="dashboard-actions">
                <a class="btn btn-dashboard" href="jobs.php">Find a job</a>
                <a class="btn btn-dashboard btn-dashboard-muted" href="logout.php">Log out</a>
            </div>
        </div>

        <div class="dashboard-stats dashboard-stats-2">
            <article class="dashboard-stat">
                <span class="stat-label">Applications sent</span>
                <strong><?php echo $applicationCount; ?></strong>
                <span class="stat-note">Jobs you have applied for</span>
            </article>
            <article class="dashboard-stat">
                <span class="stat-label">Shortlisted</span>
                <strong><?php echo $shortlistedCount; ?></strong>
                <span class="stat-note">Applications progressing</span>
            </article>
        </div>

        <?php if ($applications): ?>
        <section class="dashboard-panel dashboard-section">
            <div class="panel-heading">
                <div>
                    <h3>My applications</h3>
                    <p class="muted">The latest status of your applications</p>
                </div>
            </div>
            <div class="table-scroll">
                <table class="data-table">
                    <thead><tr><th>Job</th><th>Company</th><th>Location</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($application['title']); ?></td>
                            <td><?php echo htmlspecialchars($application['company']); ?></td>
                            <td><?php echo htmlspecialchars($application['location']); ?></td>
                            <td><span class="role-badge"><?php echo htmlspecialchars($application['status']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php else: ?>
        <div class="empty-state dashboard-section">
            <h3>Your applications will show up here</h3>
            <p class="muted">Browse open roles and apply to start tracking your progress.</p>
            <a class="btn btn-dashboard" href="jobs.php">Explore jobs</a>
        </div>
        <?php endif; ?>

        <div class="panel-heading dashboard-section">
            <div>
                <h3>Recently posted jobs</h3>
                <p class="muted">A few opportunities picked for you</p>
            </div>
            <a class="text-link" href="jobs.php">View all jobs</a>
        </div>
        <div class="job-grid">
            <?php foreach ($jobs as $job): ?>
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
                        <a href="jobs.php" class="btn btn-dashboard">View job</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
