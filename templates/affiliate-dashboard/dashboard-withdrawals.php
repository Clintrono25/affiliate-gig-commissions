<div class="tab-pane fade show active">
    <div class="card mb-3">
        <div class="card-header">Payouts History</div>
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($agc_withdrawals): foreach ($agc_withdrawals as $w): ?>
                    <tr>
                        <td><?php echo esc_html(date('Y-m-d', strtotime($w->requested_at))); ?></td>
                        <td><?php echo wc_price($w->amount); ?></td>
                        <td><?php echo esc_html(ucfirst($w->status)); ?></td>
                        <td><?php echo esc_html($w->method ?? 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="4">No withdrawal requests yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>