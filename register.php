<?php
// register.php
session_start();
require 'pdo.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // ตรวจสอบ username/email ซ้ำ
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username=? OR email=?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetchColumn() > 0) {
        $error = "Username or Email already exists";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
        $stmt->execute([$username, $email, $password]);
        $_SESSION['user_id'] = $conn->lastInsertId();
        $_SESSION['role'] = 'user';
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - UniConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-primary text-primary-content p-4 flex justify-between">
        <a href="index.php" class="font-bold text-lg">UniConnect</a>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Hi, <?= htmlspecialchars($_SESSION['role']) ?></span>
                <a href="logout.php" class="btn btn-sm btn-secondary ml-2">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-sm btn-secondary">Login</a>
                <a href="register.php" class="btn btn-sm btn-accent ml-2">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Register Form -->
    <div class="container mx-auto px-4 mt-10 max-w-md">
        <div class="card bg-base-100 shadow-md">
            <div class="card-body">
                <h2 class="card-title text-xl mb-4">Register</h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-error text-sm mb-4">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="space-y-4">
                    <div>
                        <label class="label">
                            <span class="label-text">Username</span>
                        </label>
                        <input type="text" name="username" class="input input-bordered w-full" required>
                    </div>

                    <div>
                        <label class="label">
                            <span class="label-text">Email</span>
                        </label>
                        <input type="email" name="email" class="input input-bordered w-full" required>
                    </div>

                    <div>
                        <label class="label">
                            <span class="label-text">Password</span>
                        </label>
                        <input type="password" name="password" class="input input-bordered w-full" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">Register</button>
                </form>

                <p class="mt-4 text-sm text-center">
                    Already have an account? <a href="login.php" class="link link-primary">Login here</a>
                </p>
            </div>
        </div>
    </div>

</body>

</html>