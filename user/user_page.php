<?php
// user_page.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header('Location: login.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" />
</head>

<body class="bg-gray-100 min-h-screen">

  <nav class="bg-primary text-primary-content p-4 flex justify-between">
    <a href="index.php" class="font-bold text-lg">UniConnect</a>
    <div>
      <span>Hi, User</span>
      <a href="logout.php" class="btn btn-sm btn-secondary ml-2">Logout</a>
    </div>
  </nav>

  <div class="container mx-auto px-4 mt-10">
    <div class="card bg-base-100 shadow-md p-6">
      <h2 class="text-2xl font-bold mb-4">Welcome to the User Dashboard</h2>
      <p>You're logged in as a normal user.</p>
    </div>
  </div>

</body>

</html>