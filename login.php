<?php
session_start();
require 'pdo.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid username or password";
    }

// หลังตรวจสอบ password เรียบร้อย
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];

// redirect ตาม role
header('Location: dashboard_redirect.php');
exit;

}
?>
<form method="post">
    Username: <input name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>
<?php if(isset($error)) echo $error; ?>
