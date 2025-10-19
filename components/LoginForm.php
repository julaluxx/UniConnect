<div id="login-form" class="modal modal-open">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Login</h3>
        <form method="post" action="login_action.php" class="mt-4">
            <div class="mb-4">
                <label for="username" class="label">Username</label>
                <input type="text" name="username" class="input input-bordered w-full" required>
            </div>
            <div class="mb-4">
                <label for="password" class="label">Password</label>
                <input type="password" name="password" class="input input-bordered w-full" required>
            </div>
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Login</button>
                <label for="login-form" class="btn">Cancel</label>
            </div>
        </form>
    </div>
</div>