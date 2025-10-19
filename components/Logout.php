<?php
// components/Logout.php
session_start();
session_destroy();
header('Location: ../index.php'); // กลับไปหน้า index
exit;
