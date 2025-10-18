<!-- modals.php -->
<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="login-form">
      <div class="modal-header">
        <h5 class="modal-title">Login</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Username</label>
          <input type="text" id="login-username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" id="login-password" class="form-control" required>
        </div>
        <div id="login-error" class="text-danger"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="submit">Login</button>
      </div>
    </form>
  </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="register-form">
      <div class="modal-header">
        <h5 class="modal-title">Register</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3"><label>Username</label><input id="register-username" class="form-control" required></div>
        <div class="mb-3"><label>Email</label><input id="register-email" type="email" class="form-control" required></div>
        <div class="mb-3"><label>Password</label><input id="register-password" type="password" class="form-control" required></div>
        <div class="mb-3"><label>Confirm</label><input id="register-confirm-password" type="password" class="form-control" required></div>
        <div id="register-error" class="text-danger"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="submit">Register</button>
      </div>
    </form>
  </div>
</div>
