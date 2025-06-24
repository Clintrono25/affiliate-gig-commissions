<div class="tab-pane fade show active">
    <div class="card mb-4" id="agc-commissions">
        <div class="card-header">Commissions</div>
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Gig/Product</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Referral Link</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($agc_commissions): foreach ($agc_commissions as $row): ?>
                        <?php
                        $permalink = get_permalink($row->product_id);
                        $affiliate_link = add_query_arg('aff_ref', $agc_user->user_login, $permalink);
                        $title = get_the_title($row->product_id);
                        ?>
                        <tr>
                            <td><?php echo esc_html(date('Y-m-d', strtotime($row->created_at))); ?></td>
                            <td><?php echo esc_html($title); ?></td>
                            <td><?php echo wc_price($row->amount); ?></td>
                            <td><?php echo esc_html(ucfirst($row->status)); ?></td>
<td>
    <div class="d-flex align-items-center">
        <input 
            type="text" 
            value="<?php echo esc_url($affiliate_link); ?>"
            class="form-control w-75" 
            readonly
        />
        <button 
            type="button"
            title="Copy link"
            class="border-0 p-0 ms-2"
            style="background: none; font-size:1.25rem; cursor:pointer;"
            data-copied="false"
            onClick="
                const btn = this;
                const text = btn.previousElementSibling.value;

                navigator.clipboard.writeText(text).then(() => {
                    btn.textContent = '✔️';
                    setTimeout(() => {
                        btn.textContent = '📋';
                    }, 1500);
                });
            "
        >
            📋
        </button>
    </div>
</td>

                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="5">No commissions found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>