(function($){
    // Helper: get cookie value by name
    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : '';
    }

    // Patch $.ajax to always add aff_ref if present and action is workreap_complete_task_order
    var orig_ajax = $.ajax;
    $.ajax = function(settings) {
        if (
            settings &&
            settings.data &&
            typeof settings.data === 'object' &&
            settings.data.action === 'workreap_complete_task_order'
        ) {
            var aff_ref = getCookie('agc_aff_ref');
            if (aff_ref && !settings.data.aff_ref) {
                settings.data.aff_ref = aff_ref;
            }
        }
        return orig_ajax.apply(this, arguments);
    };
})(jQuery);