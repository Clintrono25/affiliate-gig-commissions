<?php
add_action('rest_api_init', function () {
    register_rest_route('agc/v1', '/affiliate-dashboard', [
        'methods' => 'GET',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
        'callback' => function () {
            $user_id = get_current_user_id();
            global $wpdb;

            // Total earned
            $total_earned = $wpdb->get_var($wpdb->prepare(
                "SELECT SUM(amount) FROM {$wpdb->prefix}agc_commissions WHERE affiliate_id = %d",
                $user_id
            ));
            // Total withdrawn
            $total_withdrawn = $wpdb->get_var($wpdb->prepare(
                "SELECT SUM(amount) FROM {$wpdb->prefix}agc_withdrawals WHERE affiliate_id = %d AND status = 'paid'",
                $user_id
            ));
            // Pending
            $pending = $wpdb->get_var($wpdb->prepare(
                "SELECT SUM(amount) FROM {$wpdb->prefix}agc_commissions WHERE affiliate_id = %d AND status = 'pending'",
                $user_id
            ));
            // Available for withdrawal
            $available = $total_earned - $total_withdrawn;

            // Recent commissions
            $recent = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}agc_commissions WHERE affiliate_id = %d ORDER BY created_at DESC LIMIT 30",
                $user_id
            ));

            // Recent withdrawals
            $withdrawals = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}agc_withdrawals WHERE affiliate_id = %d ORDER BY requested_at DESC LIMIT 10",
                $user_id
            ));

            // Earnings over 30 days
            $earnings = $wpdb->get_results($wpdb->prepare(
                "SELECT DATE(created_at) as day, SUM(amount) as total FROM {$wpdb->prefix}agc_commissions WHERE affiliate_id = %d GROUP BY day ORDER BY day DESC LIMIT 30",
                $user_id
            ));

            return [
                'total_earned' => floatval($total_earned),
                'total_withdrawn' => floatval($total_withdrawn),
                'pending' => floatval($pending),
                'available' => floatval($available),
                'recent' => $recent,
                'withdrawals' => $withdrawals,
                'earnings_over_time' => $earnings,
            ];
        }
    ]);
});