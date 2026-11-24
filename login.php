<?php
$pageTitle = 'Login';
require_once __DIR__ . '/includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT id,name,email,password,role FROM users WHERE email=? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            $_SESSION['user'] = $user;
            header('Location: dashboard.php');
            exit;
    }

    $error = 'Invalid email or password. Please use one of the demo accounts.';
}
?>
<section class="section login-section">
    <div class="form-wrap login-form-wrap">
        <h2>Login to JobPortal</h2>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-grid">
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="name@example.com" required>
                </div>
                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </div>
        </form>
        <aside class="demo-accounts">
            <h3>Try a demo account</h3>
            <p class="muted">Choose a role to explore its dashboard.</p>
            <div class="demo-account-list">
                <div class="demo-account">
                    <span class="demo-role">Admin</span>
                    <code>admin@jobportal.com</code>
                    <span class="demo-password">Password: <strong>admin123</strong></span>
                </div>
                <div class="demo-account">
                    <span class="demo-role">Job seeker</span>
                    <code>seeker@jobportal.com</code>
                    <span class="demo-password">Password: <strong>seeker123</strong></span>
                </div>
                <div class="demo-account">
                    <span class="demo-role">Employer</span>
                    <code>employer@jobportal.com</code>
                    <span class="demo-password">Password: <strong>employer123</strong></span>
                </div>
            </div>
        </aside>
        <p>Need an account? <a href="register.php">Create one here</a>.</p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
