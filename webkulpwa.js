
jQuery.noConflict();


function onNetworkChangeStatus(offline) {
    var status = jQuery("#webkulpwa_offline_bar");
    var condition = navigator.onLine ? status.hide() : status.show();
}

window.addEventListener('load', function() {
    window.addEventListener("offline", onNetworkChangeStatus);
    window.addEventListener("online", onNetworkChangeStatus);
    onNetworkChangeStatus(false);
});


jQuery(function () {
    // page is loaded, it is safe to hide loading animation

    jQuery(window).on('beforeunload', function () {
        // user has triggered a navigation, show the loading animation
        if (jQuery("#wkpwa-loader-overlay").length>0) {
            jQuery("#wkpwa-loader-overlay").show();
        }
    });
});
