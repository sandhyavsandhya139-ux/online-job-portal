<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/includes/header.php';
authRequired(['admin']);

$userCount = (int)db()->query('SELECT COUNT(*) AS total FROM users')->fetch_assoc()['total'];
$jobCount = (int)db()->query('SELECT COUNT(*) AS total FROM jobs')->fetch_assoc()['total'];
$applicationCount = (int)db()->query('SELECT COUNT(*) AS total FROM applications')->fetch_assoc()['total'];
$users = db()->query('SELECT id,name,email,role,created_at FROM users ORDER BY created_at DESC')->fetch_all(MYSQLI_ASSOC);
$jobs = db()->query('SELECT j.id,j.title,j.company,j.location,j.type,u.name AS employer FROM jobs j JOIN users u ON u.id=j.employer_id ORDER BY j.created_at DESC')->fetch_all(MYSQLI_ASSOC);
?>
<section class="section dashboard-page">
    <div class="container">
        <div class="dashboard-header">
            <div>
                <span class="eyebrow">PORTAL ADMINISTRATION</span>
                <h2>Admin dashboard</h2>
                <p class="muted">A clear overview of your job portal activity.</p>
            </div>
            <div class="dashboard-actions">
                <a class="btn btn-dashboard" href="jobs.php">Browse jobs</a>
                <a class="btn btn-dashboard btn-dashboard-muted" href="logout.php">Log out</a>
            </div>
        </div>

        <div class="dashboard-stats">
            <article class="dashboard-stat">
                <span class="stat-label">Total users</span>
                <strong><?php echo $userCount; ?></strong>
                <span class="stat-note">Registered accounts</span>
            </article>
            <article class="dashboard-stat">
                <span class="stat-label">Active jobs</span>
                <strong><?php echo $jobCount; ?></strong>
                <span class="stat-note">Published opportunities</span>
            </article>
            <article class="dashboard-stat">
                <span class="stat-label">Applications</span>
                <strong><?php echo $applicationCount; ?></strong>
                <span class="stat-note">Candidate submissions</span>
            </article>
            <article class="dashboard-stat">
                <span class="stat-label">Portal status</span>
                <strong class="stat-status">Live</strong>
                <span class="stat-note">Your portal is running</span>
            </article>
        </div>

        <div class="dashboard-panels">
            <section class="dashboard-panel">
                <div class="panel-heading">
                    <div>
                        <h3>Users</h3>
                        <p class="muted">Recently registered accounts</p>
                    </div>
                    <span class="panel-count"><?php echo count($users); ?></span>
                </div>
                <div class="table-scroll">
                    <table class="data-table">
                        <thead><tr><th>Name</th><th>Email</th><th>Role</th></tr></thead>
                        <tbody>
                        <?php foreach ($users as $managedUser): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($managedUser['name']); ?></td>
                                <td><?php echo htmlspecialchars($managedUser['email']); ?></td>
                                <td><span class="role-badge"><?php echo htmlspecialchars(ucfirst($managedUser['role'])); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="dashboard-panel">
                <div class="panel-heading">
                    <div>
                        <h3>Job listings</h3>
                        <p class="muted">Latest opportunities on the portal</p>
                    </div>
                    <span class="panel-count"><?php echo count($jobs); ?></span>
                </div>
                <div class="table-scroll">
                    <table class="data-table">
                        <thead><tr><th>Position</th><th>Company</th><th>Location</th></tr></thead>
                        <tbody>
                        <?php foreach ($jobs as $managedJob): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($managedJob['title']); ?></td>
                                <td><?php echo htmlspecialchars($managedJob['company']); ?></td>
                                <td><?php echo htmlspecialchars($managedJob['location']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
