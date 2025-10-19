<div class="card bg-base-100 shadow-md">
    <div class="card-body">
        <h2 class="card-title text-xl mb-4">Register</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-error text-sm mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <div>
                <label class="label">
                    <span class="label-text">Username</span>
                </label>
                <input type="text" name="username" class="input input-bordered w-full" required>
            </div>

            <div>
                <label class="label">
                    <span class="label-text">Email</span>
                </label>
                <input type="email" name="email" class="input input-bordered w-full" required>
            </div>

            <div>
                <label class="label">
                    <span class="label-text">Password</span>
                </label>
                <input type="password" name="password" class="input input-bordered w-full" required>
            </div>

            <button type="submit" class="btn btn-primary w-full">Register</button>
        </form>

        <p class="mt-4 text-sm text-center">
            Already have an account? <a href="login.php" class="link link-primary">Login here</a>
        </p>
    </div>
</div>