<?php
$pageTitle = 'Employer Dashboard';
require_once __DIR__ . '/includes/header.php';
authRequired(['employer']);
$user = currentUser();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_job'])) {
    $title = trim($_POST['title'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $salary = trim($_POST['salary'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $employerId = (int)$user['id'];
    $stmt = db()->prepare('INSERT INTO jobs (employer_id,title,company,description,salary,location,type) VALUES (?,?,?,?,?,?,?)');
    $stmt->bind_param('issssss', $employerId, $title, $company, $description, $salary, $location, $type);
    $stmt->execute();
    $message = 'Job posted successfully.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $applicationId = (int)($_POST['application_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    if (in_array($status, ['Applied','Shortlisted','Rejected','Selected'], true)) {
        $ownedCheck = db()->prepare('SELECT a.id FROM applications a JOIN jobs j ON j.id=a.job_id WHERE a.id=? AND j.employer_id=?');
        $ownedCheck->bind_param('ii', $applicationId, $user['id']);
        $ownedCheck->execute();
        if ($ownedCheck->get_result()->fetch_assoc()) {
            $update = db()->prepare('UPDATE applications SET status=? WHERE id=?');
            $update->bind_param('si', $status, $applicationId);
            $update->execute();
            $message = 'Application status updated.';
        }
    }
}

$employerJobs = db()->prepare('SELECT * FROM jobs WHERE employer_id=? ORDER BY created_at DESC');
$employerJobs->bind_param('i', $user['id']);
$employerJobs->execute();
$jobs = $employerJobs->get_result()->fetch_all(MYSQLI_ASSOC);
$applicantStmt = db()->prepare('SELECT a.id,a.status,j.title,u.name,u.email FROM applications a JOIN jobs j ON j.id=a.job_id JOIN users u ON u.id=a.seeker_id WHERE j.employer_id=? ORDER BY a.applied_at DESC');
$applicantStmt->bind_param('i', $user['id']);
$applicantStmt->execute();
$applicants = $applicantStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$shortlistedCount = count(array_filter($applicants, fn($applicant) => $applicant['status'] === 'Shortlisted'));
?>
<section class="section dashboard-page">
    <div class="container">
        <div class="dashboard-header">
            <div>
                <span class="eyebrow">EMPLOYER WORKSPACE</span>
                <h2>Hiring dashboard</h2>
                <p class="muted">Post opportunities and keep candidate applications moving.</p>
            </div>
            <div class="dashboard-actions">
                <a class="btn btn-dashboard" href="#post-job">Post a job</a>
                <a class="btn btn-dashboard btn-dashboard-muted" href="logout.php">Log out</a>
            </div>
        </div>

        <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

        <div class="dashboard-stats">
            <article class="dashboard-stat">
                <span class="stat-label">Applicants</span>
                <strong><?php echo count($applicants); ?></strong>
                <span class="stat-note">Across all your job listings</span>
            </article>
            <article class="dashboard-stat">
                <span class="stat-label">Open jobs</span>
                <strong><?php echo count($jobs); ?></strong>
                <span class="stat-note">Your published listings</span>
            </article>
            <article class="dashboard-stat">
                <span class="stat-label">Shortlisted</span>
                <strong><?php echo $shortlistedCount; ?></strong>
                <span class="stat-note">Candidates to follow up with</span>
            </article>
        </div>

        <section class="dashboard-panel dashboard-section" id="post-job">
            <div class="panel-heading">
                <div>
                    <h3>Post a new job</h3>
                    <p class="muted">Share the role details with potential candidates.</p>
                </div>
            </div>
            <form method="post" class="job-form">
                <label>Job title<input type="text" name="title" placeholder="e.g. Frontend Developer" required></label>
                <label>Company name<input type="text" name="company" placeholder="Company" required></label>
                <label>Location<input type="text" name="location" placeholder="City or Remote"></label>
                <label>Salary<input type="text" name="salary" placeholder="e.g. $70,000"></label>
                <label>Employment type
                    <select name="type"><option>Full Time</option><option>Part Time</option><option>Remote</option><option>Hybrid</option></select>
                </label>
                <label class="job-form-description">Job description<textarea name="description" rows="4" placeholder="Describe the role and requirements" required></textarea></label>
                <button type="submit" name="create_job" value="1" class="btn btn-dashboard">Publish job</button>
            </form>
        </section>

        <div class="dashboard-panels dashboard-section">
            <section class="dashboard-panel">
                <div class="panel-heading">
                    <div>
                        <h3>Your job listings</h3>
                        <p class="muted">Current opportunities</p>
                    </div>
                </div>
                <?php if ($jobs): ?>
                <div class="table-scroll">
                    <table class="data-table">
                        <thead><tr><th>Position</th><th>Company</th><th>Location</th><th>Type</th></tr></thead>
                        <tbody>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($job['title']); ?></td>
                                <td><?php echo htmlspecialchars($job['company']); ?></td>
                                <td><?php echo htmlspecialchars($job['location']); ?></td>
                                <td><?php echo htmlspecialchars($job['type']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="muted">You have not posted a job yet.</p>
                <?php endif; ?>
            </section>

            <section class="dashboard-panel">
                <div class="panel-heading">
                    <div>
                        <h3>Applicants</h3>
                        <p class="muted">Review candidate applications</p>
                    </div>
                </div>
                <?php if ($applicants): ?>
                <div class="table-scroll">
                    <table class="data-table">
                        <thead><tr><th>Candidate</th><th>Position</th><th>Status</th><th>Update</th></tr></thead>
                        <tbody>
                        <?php foreach ($applicants as $applicant): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($applicant['name']); ?></strong><br><span class="muted"><?php echo htmlspecialchars($applicant['email']); ?></span></td>
                                <td><?php echo htmlspecialchars($applicant['title']); ?></td>
                                <td><span class="role-badge"><?php echo htmlspecialchars($applicant['status']); ?></span></td>
                                <td>
                                    <form method="post" class="status-form">
                                        <input type="hidden" name="application_id" value="<?php echo (int)$applicant['id']; ?>">
                                        <select name="status">
                                            <?php foreach (['Applied','Shortlisted','Rejected','Selected'] as $status): ?>
                                            <option <?php echo $applicant['status'] === $status ? 'selected' : ''; ?>><?php echo $status; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="update_status" value="1" class="btn btn-dashboard">Save</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="muted">Applicants will appear here when someone applies.</p>
                <?php endif; ?>
            </section>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
