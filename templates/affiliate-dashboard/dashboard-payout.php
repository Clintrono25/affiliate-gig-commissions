<div class="tab-pane fade show active">
    <div class="card mb-3">
        <div class="card-header">Update Payout Method</div>
        <div class="card-body">
            <?php if ($agc_payout_msg) echo $agc_payout_msg; ?>
            <?php if (!empty($agc_payout_method['type'])): ?>
                <div class="alert alert-secondary mb-3">
                    <strong>Your current payout method:</strong><br>
                    <?php if ($agc_payout_method['type'] === 'paypal'): ?>
                        <span>PayPal - <?php echo esc_html($agc_payout_method['email']); ?></span>
                    <?php elseif ($agc_payout_method['type'] === 'payoneer'): ?>
                        <span>Payoneer - <?php echo esc_html($agc_payout_method['email']); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <form method="post" class="row g-3" id="agc-payout-method-form">
                <?php wp_nonce_field('agc_update_payout'); ?>
                <div class="col-md-6">
                    <label for="payout_method" class="form-label">Payout Method</label>
                    <select name="payout_method" id="payout_method" class="form-select" required onchange="agcShowPayoutFields(this.value)">
                        <option value="">Select method</option>
                        <option value="paypal" <?php selected($agc_payout_method['type'] ?? '', 'paypal'); ?>>PayPal</option>
                        <option value="payoneer" <?php selected($agc_payout_method['type'] ?? '', 'payoneer'); ?>>Payoneer</option>
                    </select>
                </div>
                <div class="col-md-12 agc-payout-fields" id="agc-paypal-fields" style="display:none;">
                    <label for="paypal_email" class="form-label">PayPal Email</label>
                    <input type="email" class="form-control" name="paypal_email" id="paypal_email" value="<?php echo esc_attr($agc_payout_method['type'] === 'paypal' ? $agc_payout_method['email'] : ''); ?>">
                </div>
                <div class="col-md-12 agc-payout-fields" id="agc-payoneer-fields" style="display:none;">
                    <label for="payoneer_email" class="form-label">Payoneer Email</label>
                    <input type="email" class="form-control" name="payoneer_email" id="payoneer_email" value="<?php echo esc_attr($agc_payout_method['type'] === 'payoneer' ? $agc_payout_method['email'] : ''); ?>">
                </div>
                <div class="col-12">
                    <button type="submit" name="agc_update_payout" class="btn btn-success">Save Payout Method</button>
                </div>
            </form>
        </div>
    </div>
</div>