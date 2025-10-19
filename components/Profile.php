<div class="profile card bg-base-100 shadow-md p-6 text-center mb-4">
    <h3 class="card-title justify-center"><?php echo $user_data['username']; ?></h3>
    <img src="<?php echo $user_data['profile_image']; ?>" alt="Profile Image" class="w-24 h-24 mx-auto">
    <p><?php echo $user_data['bio']; ?></p>
</div>