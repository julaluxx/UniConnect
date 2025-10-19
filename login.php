<?php
// login.php
session_start();
require 'pdo.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        header('Location: redirect.php');
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - UniConnect</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" />
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

  <!-- Login Form -->
  <div class="container mx-auto px-4 mt-10 max-w-md">
    <div class="card bg-base-100 shadow-md">
      <div class="card-body">
        <h2 class="card-title text-xl mb-4">Login</h2>

        <?php if (isset($error)): ?>
          <div class="alert alert-error text-sm mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
          <div>
            <label class="label"><span class="label-text">Username</span></label>
            <input type="text" name="username" class="input input-bordered w-full" required />
          </div>
          <div>
            <label class="label"><span class="label-text">Password</span></label>
            <input type="password" name="password" class="input input-bordered w-full" required />
          </div>
          <button type="submit" class="btn btn-primary w-full">Login</button>
        </form>

        <p class="mt-4 text-sm text-center">
          Don't have an account? <a href="register.php" class="link link-primary">Register here</a>
        </p>
      </div>
    </div>
  </div>

</body>
</html>
