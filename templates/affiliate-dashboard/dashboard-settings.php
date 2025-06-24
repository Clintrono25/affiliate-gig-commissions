<div class="tab-pane fade show active">
    <?php if (!$agc_is_deactivated): ?>
    <div class="card mb-4">
        <div class="card-header text-danger">Deactivate Affiliate Account</div>
        <div class="card-body">
            <p>
                If you deactivate your affiliate account, you will lose access to the affiliate dashboard and related features.<br>
                This action cannot be undone from your dashboard. Contact support to reactivate your account.
            </p>
            <form method="post" onsubmit="return confirm('Are you sure you want to deactivate your affiliate account?');">
                <?php wp_nonce_field('agc_deactivate_affiliate'); ?>
                <button type="submit" name="agc_deactivate_affiliate" class="btn btn-outline-danger">Deactivate Account</button>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-warning">Your affiliate account is deactivated.</div>
    <?php endif; ?>
    <?php if ($agc_user_type === 'freelancers'): ?>
    <div class="card mb-3">
        <div class="card-header">Affiliate Commission Rate</div>
        <div class="card-body">
            <?php echo $agc_commission_rate_message; ?>
            <form method="post" class="row g-3 align-items-center">
                <?php wp_nonce_field('agc_commission_rate_update'); ?>
                <div class="col-auto">
                    <label for="agc_affiliate_rate" class="col-form-label">Commission Rate (%)</label>
                </div>
                <div class="col-auto">
                    <input type="number" name="agc_affiliate_rate" id="agc_affiliate_rate" value="<?php echo esc_attr($agc_current_rate); ?>" min="0" max="40" step="1" class="form-control" required> %
                </div>
                <div class="col-auto">
                    <input type="submit" name="agc_update_commission_rate" value="Save Rate" class="btn btn-primary">
                </div>
            </form>
            <small class="text-muted">This is the percentage affiliates will earn when promoting your gigs. Max 40%.</small>
            <div style="margin-top:10px;">
                <strong>Your current commission rate:</strong>
                <?php echo esc_html($agc_current_rate); ?>%
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>