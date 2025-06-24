<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function agcShowPayoutFields(val) {
    document.querySelectorAll('.agc-payout-fields').forEach(e=>e.style.display='none');
    if(val==='paypal') document.getElementById('agc-paypal-fields').style.display='block';
    if(val==='payoneer') document.getElementById('agc-payoneer-fields').style.display='block';
}
document.addEventListener('DOMContentLoaded', function(){
    var sel = document.getElementById('payout_method');
    if(sel) agcShowPayoutFields(sel.value);
    // Chart
    const chartCtx = document.getElementById('agc-earnings-chart');
    if(chartCtx && typeof Chart!=="undefined") {
        const days = <?php echo json_encode(array_reverse(array_map(function($e){return $e->day;}, $agc_earnings_30d))); ?>;
        const totals = <?php echo json_encode(array_reverse(array_map(function($e){return floatval($e->total);}, $agc_earnings_30d))); ?>;
        new Chart(chartCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: days,
                datasets: [{
                    label: 'Earnings',
                    data: totals,
                    backgroundColor: 'rgba(46,125,50,0.1)',
                    borderColor: '#2E7D32',
                    tension: .35,
                    fill: true
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });
    }
});
</script>