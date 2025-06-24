<?php

if (isset($_GET['deactivated']) && $_GET['deactivated'] == 1) {
    echo '<div class="alert alert-warning my-4">Your affiliate account has been deactivated. Contact support if this is a mistake.</div>';
}

$error = '';
if (isset($_GET['login']) && $_GET['login'] === 'failed') {
    $error = 'Incorrect username or password.';
}
if (isset($_GET['confirmed']) && $_GET['confirmed'] === '1') {
    echo '<div class="alert alert-success my-4">Your email has been confirmed! Please log in.</div>';
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
<div class="container py-5" style="max-width:420px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="mb-4 text-center" style="color:#2E7D32;">Affiliate Login</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo esc_html($error); ?></div>
            <?php endif; ?>
            <form method="post" action="<?php echo esc_url(site_url('/wp-login.php')); ?>">
                <div class="mb-3">
                    <label for="log" class="form-label">Username or Email</label>
                    <input type="text" class="form-control" name="log" id="log" required>
                </div>
                <div class="mb-3">
                    <label for="pwd" class="form-label">Password</label>
                    <input type="password" class="form-control" name="pwd" id="pwd" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Login</button>
                </div>
                <input type="hidden" name="redirect_to" value="/affiliate-dashboard/" />
            </form>
            <div class="mt-3 text-center">
                Don't have an account? <a href="/affiliate-register/" class="text-success">Register</a>
            </div>
        </div>
    </div>
</div>