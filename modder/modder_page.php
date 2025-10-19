<?php
// modder_page.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'moderator') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Moderator Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" />
</head>

<body class="bg-gray-100 min-h-screen">

    <nav class="bg-primary text-primary-content p-4 flex justify-between">
        <a href="index.php" class="font-bold text-lg">UniConnect</a>
        <div>
            <span>Hi, Moderator</span>
            <a href="logout.php" class="btn btn-sm btn-secondary ml-2">Logout</a>
        </div>
    </nav>

    <div class="container mx-auto px-4 mt-10">
        <div class="card bg-base-100 shadow-md p-6">
            <h2 class="text-2xl font-bold mb-4">Moderator Tools</h2>
            <p>You can review reports and manage threads.</p>
        </div>
    </div>

</body>

</html>