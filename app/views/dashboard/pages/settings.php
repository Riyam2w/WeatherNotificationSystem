<div class="settings-page">
    <div class="page-header">
        <h1>Account Settings</h1>
        <p>Manage your personal information and account security.</p>
    </div>

    <!-- Profile Information -->
    <div class="settings-card">
        <h2>Profile Information</h2>
        <form class="profile-form" id="profile-form">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" id="btn-save-profile">Save Changes</button>
        </form>
    </div>

    <!-- Password & Security -->
    <div class="settings-card">
        <h2>Password & Security</h2>
        <form class="password-form" id="password-form">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-group full-width">
                <label for="current_password">Current Password</label>
                <div class="input-wrapper">
                    <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Enter current password" required>
                    <span id="toggleCurrentPassword" class="toggle-password fa fa-eye-slash"></span>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Min 6 chars, 1 letter, 1 number" required>
                        <span id="toggleNewPassword" class="toggle-password fa fa-eye-slash"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                        <span id="toggleConfirmPassword" class="toggle-password fa fa-eye-slash"></span>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" id="btn-change-password">Change Password</button>
        </form>
    </div>
</div>

<!-- Page Specific CSS -->
<link rel="stylesheet" href="/assets/css/dashboard/settings.css">
