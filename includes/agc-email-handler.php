<?php
if (!function_exists('agc_send_confirmation_email')) {
    function agc_send_confirmation_email($user_id, $email, $confirm_link) {
        // Load the template
        $template_path = plugin_dir_path(__FILE__) . '../templates/email-confirmation.php';
        if (file_exists($template_path)) {
            ob_start();
            include $template_path;
            $body = ob_get_clean();
        } else {
            // fallback to plain text
            $body = "Thank you for registering!\n\nPlease confirm your email:\n$confirm_link\n\n";
        }

        $subject = "Confirm your Affiliate Account";
        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($email, $subject, $body, $headers);
    }
}