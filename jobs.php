<?php
$pageTitle = 'Browse Jobs';
require_once __DIR__ . '/includes/header.php';
$search = trim($_GET['search'] ?? '');
$location = trim($_GET['location'] ?? '');
$sql = 'SELECT * FROM jobs WHERE 1=1';
$params = [];
$types = '';
if ($search !== '') {
    $sql .= ' AND (title LIKE ? OR company LIKE ? OR description LIKE ?)';
    $term = '%' . $search . '%';
    $params = array_merge($params, [$term, $term, $term]);
    $types .= 'sss';
}
if ($location !== '') {
    $sql .= ' AND location LIKE ?';
    $params[] = '%' . $location . '%';
    $types .= 's';
}
$sql .= ' ORDER BY created_at DESC';
$stmt = db()->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_job'])) {
    authRequired(['seeker']);
    $jobId = (int)$_POST['apply_job'];
    $apply = db()->prepare('INSERT IGNORE INTO applications (seeker_id,job_id,status) VALUES (?,?,"Applied")');
    $seekerId = (int)currentUser()['id'];
    $apply->bind_param('ii', $seekerId, $jobId);
    $apply->execute();
    $message = $apply->affected_rows ? 'Application submitted successfully.' : 'You have already applied for this job.';
}
?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>Available jobs</h2>
            <p class="muted">Explore new openings and apply for roles aligned with your career goals.</p>
        </div>
        <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
        <form method="get" class="form-wrap" style="margin-top:0;margin-bottom:2rem;">
            <div class="form-grid">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Job title, company or skills">
                <input type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" placeholder="Location">
                <button type="submit" class="btn">Search jobs</button>
            </div>
        </form>
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
                        <?php if (currentUser() && currentUser()['role'] === 'seeker'): ?>
                            <form method="post">
                                <button type="submit" name="apply_job" value="<?php echo (int)$job['id']; ?>" class="btn btn-primary">Apply Now</button>
                            </form>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary">Apply Now</a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
