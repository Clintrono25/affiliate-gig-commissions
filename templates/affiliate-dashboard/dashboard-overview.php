<div class="tab-pane fade show active">
    <?php if ($agc_withdrawal_msg) echo $agc_withdrawal_msg; ?>
    <div class="row gy-3 agc-overview-cards">
        <div class="col-md-3 col-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="fa-solid fa-sack-dollar agc-card-icon"></i>
                    <div>
                        <div class="fw-bold fs-5">Total Income</div>
                        <div class="fs-4"><?php echo wc_price($agc_total_commissions); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="fa-solid fa-hand-holding-dollar agc-card-icon"></i>
                    <div>
                        <div class="fw-bold fs-5">Available</div>
                        <div class="fs-4"><?php echo wc_price($agc_available); ?></div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="#" class="link-primary small" data-bs-toggle="modal" data-bs-target="#withdrawModal">Withdraw now</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="fa-solid fa-clock agc-card-icon"></i>
                    <div>
                        <div class="fw-bold fs-5">Pending</div>
                        <div class="fs-4"><?php echo wc_price($agc_pending); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="fa-solid fa-money-bill-transfer agc-card-icon"></i>
                    <div>
                        <div class="fw-bold fs-5">Withdrawn</div>
                        <div class="fs-4"><?php echo wc_price($agc_total_withdrawn); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Withdraw Modal -->
    <div class="modal fade agc-modal" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form method="post" class="modal-content">
          <?php wp_nonce_field('agc_withdrawal_request'); ?>
          <div class="modal-header">
            <h5 class="modal-title" id="withdrawModalLabel">Withdraw Money</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <label>Amount:</label>
            <input type="number" class="form-control mb-2" value="<?php echo esc_attr($agc_available); ?>" min="<?php echo $agc_min_withdrawal; ?>" max="<?php echo esc_attr($agc_available); ?>" readonly>
            <label>Payout method:</label>
            <input type="text" class="form-control mb-2" value="<?php echo esc_attr($agc_payout_method['type'] ?? 'Not set'); ?>" readonly>
            <div class="small text-muted mt-1">Minimum withdrawal: <?php echo wc_price($agc_min_withdrawal); ?></div>
          </div>
          <div class="modal-footer">
            <input type="hidden" name="agc_withdrawal_request" value="1">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success" <?php echo ($agc_available < $agc_min_withdrawal || empty($agc_payout_method['type'])) ? 'disabled' : ''; ?>>Withdraw now</button>
          </div>
        </form>
      </div>
    </div>
    <div class="row my-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">Earnings (Last 30 Days)</div>
                <div class="card-body">
                    <canvas id="agc-earnings-chart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4" id="agc-withdrawals">
            <div class="card mb-4">
                <div class="card-header">Recent Withdrawals</div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php if ($agc_withdrawals): foreach ($agc_withdrawals as $w): ?>
                            <li class="list-group-item">
                                <strong><?php echo wc_price($w->amount); ?></strong>
                                &mdash; <?php echo esc_html(ucfirst($w->status)); ?> (<?php echo esc_html($w->method ?? 'N/A'); ?>)
                                <span class="float-end"><?php echo esc_html(date('Y-m-d', strtotime($w->requested_at))); ?></span>
                            </li>
                        <?php endforeach; else: ?>
                            <li class="list-group-item">No withdrawals yet.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>