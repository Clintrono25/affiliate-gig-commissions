<?php
$errors = $GLOBALS['agc_affiliate_register_errors'] ?? new WP_Error();
$prefill = $GLOBALS['agc_affiliate_register_prefill'] ?? [];
$current_user = wp_get_current_user();
$logged_in = is_user_logged_in() && $current_user->exists();

if ($logged_in) {
    $user_type = get_user_meta($current_user->ID, '_user_type', true);
    if ($user_type === 'freelancers') {
        $role = 'freelancers';
        $role_display = 'Freelancer';
    } elseif ($user_type === 'employers') {
        $role = 'employers';
        $role_display = 'Employer';
    } else {
        $role = 'subscriber';
        $role_display = 'Affiliate Marketer';
    }
} else {
    $role = 'subscriber';
    $role_display = 'Affiliate Marketer';
}

$username = $logged_in ? $current_user->user_login : ($prefill['username'] ?? ($_POST['username'] ?? ''));
$email = $logged_in ? $current_user->user_email : ($prefill['email'] ?? ($_POST['email'] ?? ''));
$rate = $logged_in && $role === 'freelancers'
    ? get_user_meta($current_user->ID, 'agc_affiliate_rate', true)
    : ($prefill['rate'] ?? ($_POST['agc_affiliate_rate'] ?? ''));

$show_form = !isset($_GET['registered']) || $_GET['registered'] !== '1';

// Handle email confirmation (should be at the very top)
if (isset($_GET['confirm_email']) && $_GET['user_id'] && $_GET['key']) {
    $confirm_user_id = intval($_GET['user_id']);
    $key = sanitize_text_field($_GET['key']);
    $saved_key = get_user_meta($confirm_user_id, 'agc_email_confirm_key', true);
    if ($saved_key && $key === $saved_key) {
        update_user_meta($confirm_user_id, 'agc_email_confirmed', 1);
        delete_user_meta($confirm_user_id, 'agc_email_confirm_key');
        wp_redirect(site_url('/affiliate-login/?confirmed=1'));
        exit;
    } else {
        echo '<div class="alert alert-danger my-4">Invalid or expired confirmation link.</div>';
    }
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" />
<style>
.input-group-text {
    cursor: pointer;
    background: #eee;
}
</style>
<div class="container py-5" style="max-width:480px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="mb-4 text-center" style="color:#2E7D32;">Affiliate Registration</h2>
            <?php if ($errors && !empty($errors->errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors->get_error_messages() as $message): ?>
                        <div><?php echo esc_html($message); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!$show_form): ?>
                <div class="alert alert-success">Registration successful! Please check your email to confirm your account.</div>
            <?php else: ?>
            <form method="post" id="agc-affiliate-register" autocomplete="off" onsubmit="return validateAgcPassword();">
                <div class="mb-3">
                    <label for="username" class="form-label">Username*</label>
                    <input type="text" class="form-control" id="username" name="username" required value="<?php echo esc_attr($username); ?>" <?php echo $logged_in ? 'readonly' : ''; ?>>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email*</label>
                    <input type="email" class="form-control" id="email" name="email" required value="<?php echo esc_attr($email); ?>" <?php echo $logged_in ? 'readonly' : ''; ?>>
                </div>
                <?php if (!$logged_in): ?>
                <div class="mb-3">
                    <label for="password" class="form-label">Password*</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password"
                               pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{6,}$"
                               title="At least 6 characters, with upper, lower, number, and special character.">
                        <span class="input-group-text" onclick="toggleAgcShowPassword('password', this)">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                    <div class="form-text">
                        Must be at least 6 characters, include 1 uppercase, 1 lowercase, 1 number, and 1 special character.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Confirm Password*</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required autocomplete="new-password">
                        <span class="input-group-text" onclick="toggleAgcShowPassword('password_confirm', this)">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                </div>
                <div class="mb-2">
                    <div id="agc-password-error" class="text-danger small"></div>
                </div>
                <?php endif; ?>

                <!-- Account Type (Role) -->
                <div class="mb-3">
                    <label class="form-label">Account Type*</label>
                    <input type="text" class="form-control" value="<?php echo esc_attr($role_display); ?>" readonly>
                    <input type="hidden" name="role" value="<?php echo esc_attr($role); ?>">
                </div>

                <!-- Freelancer Affiliate Rate -->
                <div class="mb-3" id="affiliate-rate-field" style="<?php echo ($logged_in && $role === 'freelancers') ? '' : 'display:none;'; ?>">
                    <label for="agc_affiliate_rate" class="form-label">Affiliate Rate (%) <span style="color:#888">(max 40%, only for freelancers)</span></label>
                    <input type="number" name="agc_affiliate_rate" id="agc_affiliate_rate" min="0" max="40" step="1" class="form-control" value="<?php echo esc_attr($rate); ?>">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success" name="agc_affiliate_register"><?php echo $logged_in ? 'Join Affiliate Program' : 'Sign Up'; ?></button>
                </div>
                <?php if (!$logged_in): ?>
                <div class="mt-3 text-center">
                    Already have an account? <a href="<?php echo site_url('/affiliate-login/'); ?>" class="text-success">Login here</a>
                </div>
                <?php endif; ?>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<script>
function toggleAgcShowPassword(fieldId, el) {
    var field = document.getElementById(fieldId);
    var icon = el.querySelector('i');
    if (field.type === "password") {
        field.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function validateAgcPassword() {
    var pw = document.getElementById('password');
    var pwc = document.getElementById('password_confirm');
    var error = document.getElementById('agc-password-error');
    error.innerText = '';

    if (!pw || !pwc) return true;

    var val = pw.value;
    var re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{6,}$/;
    if (!re.test(val)) {
        error.innerText = 'Password must have at least 6 chars, one uppercase, one lowercase, one number, one special character.';
        pw.focus();
        return false;
    }
    if (val !== pwc.value) {
        error.innerText = 'Passwords do not match.';
        pwc.focus();
        return false;
    }
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    var rateField = document.getElementById('affiliate-rate-field');
    <?php if ($logged_in && $role === 'freelancers'): ?>
        if (rateField) rateField.style.display = 'block';
    <?php else: ?>
        if (rateField) rateField.style.display = 'none';
    <?php endif; ?>
});
</script>