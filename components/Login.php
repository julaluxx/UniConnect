<div class="card bg-base-100 shadow-md mb-4">
    <div class="card-body">
        <h2 class="card-title text-xl mb-4">Login</h2>
        <form method="post" action="login_handler.php" class="space-y-4">
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
            Don't have an account? <a href="index.php?action=register" class="link link-primary">Register here</a>
        </p>
    </div>
</div>