<?php
/**
 * Plugin Name: Affiliate Gig Commissions
 * Description: Affiliate plugin for WooCommerce gigs/products. Tracks referrals, awards commissions, and provides dashboards for affiliates and freelancers.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: affiliate-gig-commissions
 */

if (!defined('ABSPATH')) exit;

// Include core files
require_once plugin_dir_path(__FILE__) . 'includes/class-affiliate-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-commission-tracker.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-dashboard.php';
require_once plugin_dir_path(__FILE__) . 'includes/install.php';

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