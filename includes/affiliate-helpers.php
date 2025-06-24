<?php
function agc_affiliate_require_login() {
    if (!is_user_logged_in()) {
        wp_redirect(site_url('/affiliate-register/'));
        exit;
    }
    $current_user = wp_get_current_user();
    if (!$current_user || !$current_user->exists()) {
        wp_redirect(site_url('/affiliate-register/'));
        exit;
    }
    if (get_user_meta($current_user->ID, 'agc_affiliate_deactivated', true)) {
        echo '<div class="alert alert-danger my-4">Your affiliate account is deactivated. Contact support to reactivate.</div>';
        exit;
    }
}