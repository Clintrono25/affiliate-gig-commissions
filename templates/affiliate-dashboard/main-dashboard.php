<?php
/* Template Name: Affiliate Dashboard */
get_header();
?>
<div class="container my-4" id="agc-affiliate-dashboard">
<?php
require_once __DIR__.'/dashboard-init.php';
if (!$agc_user_logged_in) {
    // Show guest message and stop
    require __DIR__.'/dashboard-guest.php';
    echo '</div>';
    get_footer();
    return;
}
// Continue for logged-in users
require __DIR__.'/dashboard-header.php';

// Tabs
switch($agc_tab){
    case 'commissions':
        require __DIR__.'/dashboard-commissions.php'; break;
    case 'withdrawals':
        require __DIR__.'/dashboard-withdrawals.php'; break;
    case 'payout':
        require __DIR__.'/dashboard-payout.php'; break;
    case 'settings':
        require __DIR__.'/dashboard-settings.php'; break;
    case 'overview':
    default:
        require __DIR__.'/dashboard-overview.php'; break;
}
require __DIR__.'/dashboard-js.php';
echo '</div>';
get_footer();
?>