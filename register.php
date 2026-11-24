<?php
$pageTitle = 'Register';
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $submittedRole = trim($_POST['role'] ?? 'seeker');
    $role = in_array($submittedRole, ['seeker', 'employer'], true) ? $submittedRole : 'seeker';

    $error = '';
    if (strlen($password) < 6) {
        $error = 'Password must contain at least 6 characters.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = db()->prepare('INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)');
        $stmt->bind_param('ssss', $name, $email, $hash, $role);
        try {
            $stmt->execute();
            $_SESSION['user'] = ['id' => $stmt->insert_id, 'name' => $name, 'email' => $email, 'role' => $role];
            header('Location: dashboard.php');
            exit;
        } catch (mysqli_sql_exception $exception) {
            $error = 'This email is already registered.';
        }
    }
}
?>
<section class="section">
    <div class="form-wrap">
        <h2>Create your account</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-grid">
                <div>
                    <label for="name">Full name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div>
                    <label for="role">Account type</label>
                    <select id="role" name="role">
                        <option value="seeker">Job Seeker</option>
                        <option value="employer">Employer</option>
                    </select>
                </div>
                <button type="submit" class="btn">Register</button>
            </div>
        </form>
        <p>Already registered? <a href="login.php">Login here</a>.</p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
