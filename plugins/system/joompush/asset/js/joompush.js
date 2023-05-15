jQuery(document).ready(function () {
    let config = {
        appId: appId,
        apiKey: apiKey,
        projectId: project_id,
        messagingSenderId: messagingSenderId,
        authDomain: `${project_id}.firebaseapp.com`,
        storageBucket: `${project_id}.appspot.com`,
        databaseURL: `https://${project_id}.firebaseio.com`,
    };

    if (!firebase.messaging.isSupported()) {
        console.warn('Firebase is not supported on your browser.');
        return;
    }
    firebase.initializeApp(config);

    // Retrieve Firebase Messaging object.
    const messaging = firebase.messaging();

    if (getCookie('jpManual') == 0) {
        jpInit();
    }

    function jpInit() {
        if (getCookie('jpsent') == 0) {
            // On load register service worker
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register(sw_url).then((registration) => {
                        // Successfully registers service worker
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                        messaging.useServiceWorker(registration);
                    }).then(() => {
                        if (jpgdpr == 1 && getCookie('jpManual') == 0) {
                            jQuery('#jp-overlay-backdrop').show();
                        }
                        // Requests user browser permission
                        return messaging.requestPermission();
                    }).then(() => {
                        // Gets token
                        return messaging.getToken({vapidKey: vapidKey});
                    }).then((token) => {
                        if (jpgdpr == 1 && getCookie('jpManual') == 0) {
                            jQuery('#jp-overlay-backdrop').hide();
                        }
                        // Simple ajax call to send user token to server for saving
                        let storeurl = baseurl + 'index.php?option=com_joompush&task=mynotifications.setSubscriber';
                        jQuery.ajax({
                            type: 'post',
                            url: storeurl,
                            data: {key: token, IsClient: isClient, Userid: userid},
                            success: (data) => {
                                console.log('Success ', data);
                                document.cookie = "jpsent = 1;"
                                if (jpgdpr_unsub == 1) {
                                    if (getCookie('jpManual') == 1) {
                                        location.reload();
                                    } else {
                                        document.cookie = "jpManual = 1;"
                                    }
                                }
                            },
                            error: (err) => {
                                console.log('Error ', err);
                            }
                        })
                    }).catch((err) => {
                        if (jpgdpr == 1) {
                            jQuery('#jp-overlay-backdrop').hide();
                        }
                        console.log('ServiceWorker registration failed: ', err);
                    });
                });
            }
        }
    }

    function jpDeleteToken() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register(sw_url).then((registration) => {
                console.log('ServiceWorker registration successful with scope: ', registration.scope);
                messaging.useServiceWorker(registration);
            }).then(() => {
                // Gets token
                return messaging.getToken();
            }).then((token) => {
                messaging.deleteToken(token).then(function () {
                    console.log('Token deleted.');
                    jQuery('#jppopmsg').empty();
                    jQuery('#jppopmsg').text(jpgdpr_unsub_msg);
                    jQuery('#jpcallSW').hide();
                    jQuery('#jploadimg').hide();
                    deleteCookie('jpsent');
                    document.cookie = "jpManual = 1;"
                    setTimeout(function () {
                        location.reload();
                    }, 5000);
                })
            }).catch((err) => {
                console.log('ServiceWorker registration failed: ', err);
            });
        }
    }

    if (jpgdpr_unsub == 1 && getCookie('jpManual') == 1) {
        jQuery("body").append(jpgdpr_show);

        jQuery("#jpcallSW").click(function () {
            jQuery('#jpcallSW').hide();
            jQuery('#jploadimg').show();
            if (getCookie('jpsent') == 1) {
                jpDeleteToken();
            } else {
                jpInit();
            }
        });

        var jpmodal = document.getElementById("jpmyModal");
        var jpspan = document.getElementById("jpmyimg");
        var jpspan1 = document.getElementsByClassName("jpclose")[0];
        jpspan.onclick = function () {
            jpmodal.style.display = "block";
        }
        jpspan1.onclick = function () {
            jpmodal.style.display = "none";
        }
        window.onclick = function (event) {
            if (event.target == jpmodal) {
                jpmodal.style.display = "none";
            }
        }
    }
});


function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function deleteCookie(cname) {
    document.cookie = cname + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}
