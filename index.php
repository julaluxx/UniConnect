<?php
session_start();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
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
            <a class="navbar-brand fw-bold" href="index.html">UniConnect</a>
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
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="ค้นหาในฟอรัม" aria-label="ค้นหาในฟอรัม"
                        id="search-input">
                    <button class="btn btn-outline-secondary" type="button" id="search-btn">ค้นหา</button>
                </div>
            </div>
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" id="breadcrumb-list">
                        <li class="breadcrumb-item"><a href="#main-content">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page" id="current-page">Forum</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <section class="main-content container-fluid">
        <div class="row">
            <div class="side-bar col-md-3">
                <div class="card mb-3 shadow-sm">
                    <div class="card-body text-center">
                        <h4 class="card-title">My Profile</h4>
                        <img class="card-img-top rounded-circle mx-auto" style="width: 100px; height: 100px;"
                            src="/assets/square_holder.png" alt="Profile Image">
                        <h5 class="card-title mt-2">Username</h5>
                        <p class="card-text text-muted">Short introduction of user.</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item profile-menu active" data-target="main-content">Home</li>
                        <li class="list-group-item profile-menu" data-target="my-threads">My Thread</li>
                        <li class="list-group-item profile-menu" data-target="notifications">Notification</li>
                        <li class="list-group-item profile-menu" data-target="my-comments">My Comment</li>
                        <li class="list-group-item profile-menu" data-target="edit-profile">Edit Profile</li>
                        <?php if ($user && in_array($user['role'], ['moderator', 'admin'])): ?>
                            <li class="list-group-item profile-menu d-none" data-target="report-manager"
                                id="menu-report-manager">Report Manager</li>
                            <li class="list-group-item profile-menu d-none" data-target="category-manager"
                                id="menu-category-manager">Category Manager</li>
                        <?php endif; ?>
                        <?php if ($user && $user['role'] === 'admin'): ?>
                            <li class="list-group-item profile-menu d-none" data-target="user-manager"
                                id="menu-user-manager">User Manager</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="category-container card mb-3 shadow-sm">
                    <div class="card-header">Categories</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">General</li>
                        <li class="list-group-item">Academics</li>
                        <li class="list-group-item">Housing</li>
                        <li class="list-group-item">Jobs</li>
                        <li class="list-group-item">Events</li>
                        <li class="list-group-item">Lost & Found</li>
                        <li class="list-group-item">Buy & Sell</li>
                    </ul>
                </div>
                <div class="statistics-container card mb-3 shadow-sm">
                    <div class="card-header">Statistics</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item" id="users-online">Users Online: 10
                            <svg width="20" height="20">
                                <circle cx="8" cy="8" r="5" stroke="green" stroke-width="1" fill="green" />
                            </svg>
                        </li>
                        <li class="list-group-item" id="total-users">Total Users: 200
                            <svg width="20" height="20">
                                <circle cx="8" cy="8" r="5" stroke="#f5d142" stroke-width="1" fill="#f5d142" />
                            </svg>
                        </li>
                        <li class="list-group-item" id="total-threads">Total Threads: 100</li>
                        <li class="list-group-item" id="total-comments">Total Comments: 500</li>
                    </ul>
                </div>
            </div>

            <div class="content-container col-md-9">
                <!-- Main Content (Create Thread and Forum Threads) -->
                <div id="main-content">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header">Create Thread</div>
                        <div class="card-body">
                            <form id="create-thread-form">
                                <div class="mb-3">
                                    <label for="threadTitle" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="threadTitle" required>
                                </div>
                                <div class="mb-3">
                                    <label for="threadCategory" class="form-label">Category</label>
                                    <select class="form-select" id="threadCategory" required>
                                        <option value="">Select a category</option>
                                        <option value="general">General</option>
                                        <option value="academics">Academics</option>
                                        <option value="housing">Housing</option>
                                        <option value="jobs">Jobs</option>
                                        <option value="events">Events</option>
                                        <option value="lost-and-found">Lost & Found</option>
                                        <option value="buy-and-sell">Buy & Sell</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="threadContent" class="form-label">Content</label>
                                    <textarea class="form-control" id="threadContent" rows="4" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Create Thread</button>
                            </form>
                            <div class="alert alert-success d-none mt-3" id="thread-success">Thread created
                                successfully!</div>
                        </div>
                    </div>
                    <div class="card mb-3 shadow-sm" id="forum">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Forum Threads</span>
                            <select class="form-select w-auto" id="sort-threads">
                                <option value="newest">Newest First</option>
                                <option value="popular">Most Popular</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <div class="thread" data-thread-id="1">
                                <h5 class="thread-title">Thread Title 1</h5>
                                <p class="thread-meta">
                                    <i class="bi bi-person-fill"></i> Posted by User1 |
                                    <i class="bi bi-chat-fill"></i> 10 comments |
                                    <i class="bi bi-heart-fill like-btn" data-liked="false"></i> <span
                                        class="like-count">5</span> likes
                                </p>
                            </div>
                            <div class="thread" data-thread-id="2">
                                <h5 class="thread-title">Thread Title 2</h5>
                                <p class="thread-meta">
                                    <i class="bi bi-person-fill"></i> Posted by User2 |
                                    <i class="bi bi-chat-fill"></i> 5 comments |
                                    <i class="bi bi-heart-fill like-btn" data-liked="false"></i> <span
                                        class="like-count">2</span> likes
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- My Threads -->
                <div id="my-threads" class="d-none">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header">My Threads</div>
                        <div class="card-body">
                            <div class="thread" data-thread-id="1">
                                <h5 class="thread-title">Thread Title 1</h5>
                                <p class="thread-meta">
                                    <i class="bi bi-person-fill"></i> Posted by User1 |
                                    <i class="bi bi-chat-fill"></i> 10 comments |
                                    <i class="bi bi-heart-fill like-btn" data-liked="false"></i> <span
                                        class="like-count">5</span> likes
                                </p>
                            </div>
                            <button class="btn btn-outline-primary mt-3 back-btn" data-target="main-content">Back to
                                Main</button>
                        </div>
                    </div>
                </div>
                <!-- Notifications -->
                <div id="notifications" class="d-none">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header">Notifications</div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">Notification 1</li>
                                <li class="list-group-item">Notification 2</li>
                            </ul>
                            <button class="btn btn-outline-primary mt-3 back-btn" data-target="main-content">Back to
                                Main</button>
                        </div>
                    </div>
                </div>
                <!-- My Comments -->
                <div id="my-comments" class="d-none">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header">My Comments</div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">Comment 1</li>
                                <li class="list-group-item">Comment 2</li>
                            </ul>
                            <button class="btn btn-outline-primary mt-3 back-btn" data-target="main-content">Back to
                                Main</button>
                        </div>
                    </div>
                </div>
                <!-- Edit Profile -->
                <div id="edit-profile" class="d-none">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header">Edit Profile</div>
                        <div class="card-body">
                            <form id="edit-profile-form">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" value="CurrentUsername"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" value="CurrentEmail@example.com"
                                        disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea class="form-control" id="bio" rows="3">Current bio...</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="profileImage" class="form-label">Profile Image</label>
                                    <input class="form-control" type="file" id="profileImage" accept="image/*">
                                    <img id="profile-image-preview" class="mt-2 rounded-circle d-none"
                                        style="width: 100px; height: 100px;" alt="Profile Preview">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="password">
                                </div>
                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirmPassword">
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                            <div class="alert alert-success d-none mt-3" id="profile-success">Profile updated
                                successfully!</div>
                            <button class="btn btn-outline-primary mt-3 back-btn" data-target="main-content">Back to
                                Main</button>
                        </div>
                    </div>
                </div>
                <!-- Report Manager (สำหรับ Moderator และ Admin) -->
                <div id="report-manager" class="d-none">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header">Report Manager</div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">Report 1: Spam thread <button
                                        class="btn btn-sm btn-danger float-end">Resolve</button></li>
                                <li class="list-group-item">Report 2: Offensive comment <button
                                        class="btn btn-sm btn-danger float-end">Resolve</button></li>
                            </ul>
                            <button class="btn btn-outline-primary mt-3 back-btn" data-target="main-content">Back to
                                Main</button>
                        </div>
                    </div>
                </div>
                <!-- Category Manager (สำหรับ Moderator และ Admin) -->
                <div id="category-manager" class="d-none">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header">Category Manager</div>
                        <div class="card-body">
                            <form id="add-category-form">
                                <div class="mb-3">
                                    <label for="new-category" class="form-label">New Category</label>
                                    <input type="text" class="form-control" id="new-category" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Category</button>
                            </form>
                            <ul class="list-group mt-3">
                                <li class="list-group-item">General <button
                                        class="btn btn-sm btn-danger float-end">Delete</button></li>
                                <li class="list-group-item">Academics <button
                                        class="btn btn-sm btn-danger float-end">Delete</button></li>
                            </ul>
                            <button class="btn btn-outline-primary mt-3 back-btn" data-target="main-content">Back to
                                Main</button>
                        </div>
                    </div>
                </div>
                <!-- User Manager (สำหรับ Admin เท่านั้น) -->
                <div id="user-manager" class="d-none">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header">User Manager</div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">User1 <button
                                        class="btn btn-sm btn-warning float-end">Ban</button></li>
                                <li class="list-group-item">User2 <button
                                        class="btn btn-sm btn-warning float-end">Ban</button></li>
                            </ul>
                            <button class="btn btn-outline-primary mt-3 back-btn" data-target="main-content">Back to
                                Main</button>
                        </div>
                    </div>
                </div>
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