<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
:root { --agc-primary: #2E7D32; }
.agc-tabs .nav-link { color: #333; font-weight: 500; border: none; border-radius: 0; background: transparent; transition: color 0.2s;}
.agc-tabs .nav-link.active, .agc-tabs .nav-link:hover { color: var(--agc-primary); border-bottom: 3px solid var(--agc-primary); background: #f7faf8;}
.agc-overview-cards .card { min-height: 132px; border: 1.5px solid #e0e0e0; background: #fff; box-shadow: 0 2px 4px rgba(169,210,180,0.04); transition: box-shadow 0.2s;}
.agc-card-icon { font-size: 2.2em; color: var(--agc-primary); margin-right: 14px; vertical-align: middle;}
.agc-modal .modal-dialog { max-width: 400px; }
.agc-modal .modal-body label { font-weight: 500; }
a, .nav-link, .link-primary, .link-light, .card-footer a { text-decoration: none !important; }
@media (max-width: 767px) { .agc-overview-cards .col-md-3 { flex: 0 0 50%; max-width: 50%; } }
.agc-overview-cards .card .card-body {
    min-height: 92px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.agc-overview-cards .card .card-footer {
    margin-top: 0;
    padding-top: 0.25rem;
    background: #fff;
    border-top: none;
}
/* Hide bank payout option in select */
#payout_method option[value="bank"] { display: none; }
</style>
<h2 class="mb-4">Welcome, <?php echo esc_html($agc_user->display_name); ?> <small class="text-muted">(Affiliate Dashboard)</small></h2>
<ul class="nav nav-tabs agc-tabs mb-4 d-none d-md-flex" id="agcTabs" role="tablist">
  <li class="nav-item"><a class="nav-link<?php if($agc_tab=='overview')echo' active';?>" href="?agc_tab=overview">Overview</a></li>
  <li class="nav-item"><a class="nav-link<?php if($agc_tab=='commissions')echo' active';?>" href="?agc_tab=commissions">Commissions</a></li>
  <li class="nav-item"><a class="nav-link<?php if($agc_tab=='withdrawals')echo' active';?>" href="?agc_tab=withdrawals">Withdrawals</a></li>
  <li class="nav-item"><a class="nav-link<?php if($agc_tab=='payout')echo' active';?>" href="?agc_tab=payout">Payout Method</a></li>
  <li class="nav-item"><a class="nav-link<?php if($agc_tab=='settings')echo' active';?>" href="?agc_tab=settings">Settings</a></li>
  <li class="nav-item ms-auto"><a class="nav-link text-danger" href="<?php echo wp_logout_url(site_url('/affiliate-login/')); ?>">Logout</a></li>
</ul>
<div class="mb-3 d-md-none">
  <select class="form-select" onchange="location = this.value;">
    <option value="?agc_tab=overview" <?php if($agc_tab=='overview')echo'selected';?>>Overview</option>
    <option value="?agc_tab=commissions" <?php if($agc_tab=='commissions')echo'selected';?>>Commissions</option>
    <option value="?agc_tab=withdrawals" <?php if($agc_tab=='withdrawals')echo'selected';?>>Withdrawals</option>
    <option value="?agc_tab=payout" <?php if($agc_tab=='payout')echo'selected';?>>Payout Method</option>
    <option value="?agc_tab=settings" <?php if($agc_tab=='settings')echo'selected';?>>Settings</option>
  </select>
</div>