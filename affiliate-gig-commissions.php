<?php
/**
 * Plugin Name: Affiliate Gig Commissions
 * Description: Affiliate plugin for Workreap gigs/services. Tracks referrals, awards commissions, and provides dashboards for affiliates and freelancers.
 * Version: 1.2.4
 * Author: ClintonRono
 * Text Domain: affiliate-gig-commissions
 */

if (!defined('ABSPATH')) exit;

// Include core files
require_once plugin_dir_path(__FILE__) . 'includes/class-affiliate-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-commission-tracker.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-dashboard.php';
require_once plugin_dir_path(__FILE__) . 'includes/install.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-dashboard-rest.php';
require_once plugin_dir_path(__FILE__) . 'includes/affiliate-deactivation-auth.php';
require_once plugin_dir_path(__FILE__) . 'includes/affiliate-helpers.php';


// Activation: Setup tables/roles
register_activation_hook(__FILE__, 'agc_plugin_install');
function agc_plugin_install() {
    include_once plugin_dir_path(__FILE__) . 'includes/install.php';
    agc_install();
}

// Init plugin
add_action('plugins_loaded', function() {
    AGC_Affiliate_Manager::init();
    AGC_Commission_Tracker::init();
    AGC_Dashboard::init();
});

// STEP 1: Track aff_ref from URL in a cookie for 30 days
add_action('init', function() {
    if (isset($_GET['aff_ref']) && !empty($_GET['aff_ref'])) {
        setcookie('agc_aff_ref', sanitize_user($_GET['aff_ref']), time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
        $_COOKIE['agc_aff_ref'] = sanitize_user($_GET['aff_ref']); // Immediate access for this request
    }
});

// STEP 2 & 3: Save aff_ref to order meta and award commission when Workreap order is completed
add_action('workreap_complete_task_order_activity', function($task_id, $order_id) {
    // 1. Save aff_ref to order meta if present
    if (isset($_POST['aff_ref']) && !empty($order_id)) {
        update_post_meta(
            intval($order_id),
            '_agc_aff_ref',
            sanitize_text_field($_POST['aff_ref'])
        );
        error_log('[AGC] aff_ref from AJAX saved to order ' . intval($order_id) . ': ' . sanitize_text_field($_POST['aff_ref']));
    }

    // 2. Get affiliate username from order meta (just saved above)
    $affiliate_ref = get_post_meta($order_id, '_agc_aff_ref', true);
    error_log('[AGC DBG] affiliate_ref: ' . print_r($affiliate_ref, true));
    if (!$affiliate_ref) {
        error_log('[AGC] No affiliate ref found for order ' . intval($order_id));
        return;
    }

    // 3. Get affiliate user
    $affiliate = get_user_by('login', $affiliate_ref);
    error_log('[AGC DBG] affiliate: ' . print_r($affiliate, true));
    if (!$affiliate) return;

    // 4. Get gig/service and total using correct meta keys
    $product_id    = get_post_meta($order_id, 'task_product_id', true); // Correct key for gig/task ID
    $order_total   = get_post_meta($order_id, '_order_total', true);    // Correct key for order total
    $freelancer_id = get_post_meta($order_id, 'freelancer_id', true);   // Correct key for freelancer ID

    error_log('[AGC DBG] product_id: ' . print_r($product_id, true));
    error_log('[AGC DBG] order_total: ' . print_r($order_total, true));
    error_log('[AGC DBG] freelancer_id: ' . print_r($freelancer_id, true));

    if (!$product_id || !$order_total || !is_numeric($order_total) || $order_total <= 0 || !$freelancer_id) return;

    // 5. Get freelancer's affiliate rate
    $rate = get_user_meta($freelancer_id, 'agc_affiliate_rate', true);
    error_log('[AGC DBG] rate: ' . print_r($rate, true));
    $rate = ($rate !== '' && is_numeric($rate)) ? floatval($rate) : 0;
    if ($rate <= 0) return;

    // 6. Calculate commission
    $commission = round($order_total * ($rate / 100), 2);

    global $wpdb;

    // 7. Check for duplicates
    $existing = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}agc_commissions WHERE order_id = %d AND affiliate_id = %d",
            $order_id,
            $affiliate->ID
        )
    );
    error_log('[AGC DBG] existing commission count: ' . $existing);

    if (!$existing) {
        $wpdb->insert(
            $wpdb->prefix . 'agc_commissions',
            [
                'affiliate_id' => $affiliate->ID,
                'user_id'      => $freelancer_id,
                'product_id'   => $product_id,
                'order_id'     => $order_id,
                'amount'       => $commission,
                'commission'   => $commission,
                'status'       => 'pending',
                'created_at'   => current_time('mysql', 1),
            ],
            ['%d','%d','%d','%d','%f','%f','%s','%s']
        );
        if ($wpdb->last_error) {
            error_log('[AGC SQL ERROR] ' . $wpdb->last_error);
        }
        error_log('[AGC] Commission recorded: affiliate ' . $affiliate->ID . ' for order ' . $order_id . ', amount ' . $commission);
    } else {
        error_log('[AGC] Commission already exists for order ' . $order_id . ' and affiliate ' . $affiliate->ID);
    }
}, 10, 2);

// Enqueue affiliate-workreap.js for AJAX integration (all frontend pages)
add_action('wp_enqueue_scripts', function() {
    if (!is_admin()) {
        wp_enqueue_script(
            'agc-affiliate-workreap',
            plugins_url('assets/js/affiliate-workreap.js', __FILE__),
            array('jquery'),
            '1.0',
            true
        );
    }
    // Registration page rate field JS
    if (is_page('affiliate-register')) {
        wp_enqueue_script(
            'agc-affiliate-role-rate',
            plugins_url('assets/js/affiliate-role-rate.js', __FILE__),
            array('jquery'),
            '1.0',
            true
        );
    }
});

// Shortcodes for login, register, dashboard
add_shortcode('affiliate_register', 'agc_affiliate_register_shortcode');
add_shortcode('affiliate_login', 'agc_affiliate_login_shortcode');
add_shortcode('affiliate_dashboard', 'agc_affiliate_dashboard_shortcode');
function agc_affiliate_dashboard_shortcode() {
    if (!is_user_logged_in()) {
        wp_redirect(site_url('/affiliate-login/'));
        exit;
    }
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/affiliate-dashboard/main-dashboard.php';
    return ob_get_clean();
}
function agc_affiliate_register_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/affiliate-register.php';
    return ob_get_clean();
}
add_filter('authenticate', function($user, $username, $password) {
    if (is_wp_error($user)) return $user;
    $user_obj = get_user_by('login', $username) ?: get_user_by('email', $username);
    if ($user_obj && get_user_meta($user_obj->ID, 'agc_affiliate_deactivated', true)) {
        return new WP_Error('agc_affiliate_deactivated', 'Your affiliate account is deactivated. Contact support to reactivate.');
    }
    return $user;
}, 40, 3);

// Registration Handler (POST)
add_action('init', function() {
    if (isset($_POST['agc_affiliate_register'])) {
        $current_user = wp_get_current_user();
        $logged_in = is_user_logged_in() && $current_user->exists();

        $username = $logged_in ? $current_user->user_login : sanitize_user($_POST['username']);
        $email = $logged_in ? $current_user->user_email : sanitize_email($_POST['email']);
        $role = $logged_in
            ? (in_array('freelancer', $current_user->roles) ? 'freelancer' : (in_array('employer', $current_user->roles) ? 'employer' : 'subscriber'))
            : (isset($_POST['role']) ? sanitize_text_field($_POST['role']) : 'subscriber');
        $rate = $logged_in && $role === 'freelancer'
            ? get_user_meta($current_user->ID, 'agc_affiliate_rate', true)
            : (isset($_POST['agc_affiliate_rate']) ? intval($_POST['agc_affiliate_rate']) : null);

        $errors = new WP_Error();

        if (!$logged_in && username_exists($username)) $errors->add('username', 'Username already exists');
        if (!$logged_in && email_exists($email)) $errors->add('email', 'Email already exists');
        if (!$logged_in && (empty($username) || empty($email) || empty($_POST['password']))) $errors->add('required', 'All fields are required');
        if ($role === 'freelancer') {
            if ($rate === null || $rate === '') {
                $errors->add('rate', 'Affiliate rate is required for freelancers.');
            } else {
                if ($rate > 40) $errors->add('rate', 'Affiliate rate cannot exceed 40%');
                if ($rate < 0) $errors->add('rate', 'Affiliate rate cannot be negative');
            }
        }

        if (empty($errors->errors)) {
            if ($logged_in) {
                $user = $current_user;
                if (!in_array('affiliate', $user->roles)) {
                    $user->add_role('affiliate');
                }
                if ($role === 'freelancer' && $rate !== null) {
                    update_user_meta($user->ID, 'agc_affiliate_rate', $rate);
                }
                wp_redirect(site_url('/affiliate-dashboard/'));
                exit;
            } else {
                $password = $_POST['password'];
                $user_id = wp_create_user($username, $password, $email);
                if (is_wp_error($user_id)) {
                    $errors->add('wp_error', $user_id->get_error_message());
                } else {
                    $user = new WP_User($user_id);
                    $user->set_role($role);
                    $user->add_role('affiliate');
                    if ($role === 'freelancer' && $rate !== null) {
                        update_user_meta($user_id, 'agc_affiliate_rate', $rate);
                    }
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id);
                    wp_redirect(site_url('/affiliate-dashboard/'));
                    exit;
                }
            }
        }
        $GLOBALS['agc_affiliate_register_errors'] = $errors;
        $GLOBALS['agc_affiliate_register_prefill'] = compact('username', 'email', 'role', 'rate');
    }
});