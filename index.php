<?php
session_start();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
// ไม่ดึง threads ด้วย PHP เพราะใช้ JS fetch แทน
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniConnect</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-light sticky-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">UniConnect</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#main-content">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#links">Contact</a>
                    </li>
                </ul>
                <div class="d-flex gap-2" id="auth-buttons">
                    <button class="btn btn-outline-success" type="button" id="login-btn" data-bs-toggle="modal"
                        data-bs-target="#loginModal">Login</button>
                    <button class="btn btn-outline-success" type="button" id="register-btn" data-bs-toggle="modal"
                        data-bs-target="#registerModal">Register</button>
                    <button class="btn btn-outline-danger d-none" type="button" id="logout-btn">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="login-form">
                        <div class="mb-3">
                            <label for="login-username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="login-username" required>
                        </div>
                        <div class="mb-3">
                            <label for="login-password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="login-password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                    <div class="alert alert-danger d-none mt-3" id="login-error">Invalid credentials!</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="register-form">
                        <div class="mb-3">
                            <label for="register-username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="register-username" required>
                        </div>
                        <div class="mb-3">
                            <label for="register-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="register-email" required>
                        </div>
                        <div class="mb-3">
                            <label for="register-password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="register-password" required>
                        </div>
                        <div class="mb-3">
                            <label for="register-confirm-password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="register-confirm-password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </form>
                    <div class="alert alert-success d-none mt-3" id="register-success">Registered successfully!</div>
                    <div class="alert alert-danger d-none mt-3" id="register-error">Passwords do not match!</div>
                </div>
            </div>
        </div>
    </div>

    <section id="navigation" class="container-fluid my-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div id="main-content">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#main-content">Home</a></li>
                        <li class="breadcrumb-item active" id="current-page">Forum</li>
                    </ol>
                    <div class="card mb-3 shadow-sm" id="forum">
                        <div class="card-header">Forum</div>
                        <div class="card-body">
                            <!-- Threads will be loaded here by JS -->
                        </div>
                    </div>
                    <!-- เพิ่ม form สร้าง thread และส่วนอื่นๆ ตามเดิม -->
                </div>
                <!-- อื่นๆ เช่น my-threads, notifications, etc. -->
            </div>
        </div>
    </section>

    <footer class="footer container-fluid bg-dark text-white py-4">
        <div class="row">
            <div class="col-md-4 website-info">
                <p>This website made by Juju.</p>
            </div>
            <div class="col-md-4 social-media-links" id="links">
                <h5>Follow Us</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white"><i class="bi bi-facebook me-2"></i>Facebook</a></li>
                    <li><a href="#" class="text-white"><i class="bi bi-instagram me-2"></i>Instagram</a></li>
                    <li><a href="#" class="text-white"><i class="bi bi-youtube me-2"></i>Youtube</a></li>
                </ul>
            </div>
            <div class="col-md-4 contact">
                <h5>Contact</h5>
                <ul class="list-unstyled">
                    <li><a href="mailto:example@email.com" class="text-white"><i
                                class="bi bi-envelope-at me-2"></i>Email</a></li>
                </ul>
            </div>
        </div>
        <div class="text-center mt-3">
            <p>&copy; 2025 UniConnect. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
</body>

</html>