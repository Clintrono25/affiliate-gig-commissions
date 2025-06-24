<?php
if (!defined('ABSPATH')) exit;

function agc_install() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    // Table for tracking affiliate commissions
    $table_name = $wpdb->prefix . 'agc_commissions';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        affiliate_id BIGINT UNSIGNED NOT NULL,
        product_id BIGINT UNSIGNED NOT NULL,
        amount DECIMAL(16,4) NOT NULL DEFAULT 0,
        order_id BIGINT UNSIGNED DEFAULT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY affiliate_id (affiliate_id),
        KEY product_id (product_id),
        KEY order_id (order_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}