<?php

add_filter('authenticate', function($user, $username, $password) {
    if (is_wp_error($user)) return $user;
    $user_obj = get_user_by('login', $username) ?: get_user_by('email', $username);
    if ($user_obj && get_user_meta($user_obj->ID, 'agc_affiliate_deactivated', true)) {
        return new WP_Error('agc_affiliate_deactivated', 'Your affiliate account is deactivated. Contact support to reactivate.');
    }
    return $user;
}, 40, 3);