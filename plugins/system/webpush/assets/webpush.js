(function () {
    let isPushEnabled = false

    const EnablePushNotifications = {
        _pushButton: null,

        init() {
            window.addEventListener('load', () => {
                this._pushButton = document.querySelector('.notification-toggler');
                if (this._pushButton) {
                    this._pushButton.addEventListener('click', (event) => {
                        this._initSubscriptionToggler(event)
                    })
                }

                // register the service worker
                if (('serviceWorker' in navigator)) {
                    navigator.serviceWorker.register('/webpush-sw.js', {scope: "./"}).then(() => {
                        console.log('[SW] Registered service worker')
                        this._pushInitialiseState();
                        this._initPostMessageListener();
                    }, (e) => {
                        console.error('[SW] Oups...', e);
                        this._changePushButtonState('incompatible');
                    })
                } else {
                    console.warn('[SW] Service workers are not yet supported by this browser.');
                    this._changePushButtonState('incompatible');
                }
            })
        },

        _initSubscriptionToggler(event) {
            if (isPushEnabled) {
                this._unsubscribe(event)
            } else {
                this._subscribe(event)
            }
        },

        _changePushButtonState(state) {
            if (this._pushButton) {
                switch (state) {
                    case 'enabled':
                        this._pushButton.removeAttribute('disabled');
                        this._pushButton.innerHTML = "Notifications Push activated";
                        this._pushButton.classList.add("active");
                        isPushEnabled = true;
                        break;
                    case 'disabled':
                        this._pushButton.removeAttribute('disabled');
                        this._pushButton.innerHTML = "Notifications Push disabled";
                        this._pushButton.classList.remove("active")
                        isPushEnabled = false;
                        break;
                    case 'computing':
                        this._pushButton.setAttribute('disabled', 'true');
                        this._pushButton.innerHTML = "Chargement...";
                        break;
                    case 'incompatible':
                        this._pushButton.setAttribute('disabled', 'true');
                        this._pushButton.innerHTML = "Push notifications not available (browser not compatible)";
                        break;
                    default:
                        console.error('Unhandled push button state', state);
                        break;
                }
            }
        },

        _initPostMessageListener() {
            const onRefreshNotifications = () => {
                console.log('Refresh notifications');
            };

            const onRemoveNotifications = () => {
                console.log('Remove notifications');
            };

            navigator.serviceWorker.addEventListener('message', (event) => {
                switch (event.data) {
                    case 'reload':
                        window.location.reload();
                        break;
                    case 'refreshNotifications':
                        onRefreshNotifications();
                        break;
                    case 'removeNotifications':
                        onRemoveNotifications();
                        break;
                    default:
                        console.warn(`Message '${event.data}' not handled.`);
                        break;
                }
            })
        },

        _pushInitialiseState() {
            // Are Notifications supported in the service worker?
            if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
                console.warn('[SW] Notifications are not supported by this browser.');
                this._changePushButtonState('incompatible');
                return;
            }

            // Check the current Notification permission.
            // If its denied, it's a permanent block until the
            // user changes the permission
            if (Notification.permission === 'denied') {
                console.warn('[SW] Notifications are not allowed by the user.');
                this._changePushButtonState('disabled');
                return;
            }

            // Check if push messaging is supported
            if (!('PushManager' in window)) {
                console.warn('[SW] Push messages are not supported by this browser.');
                this._changePushButtonState('incompatible');
                return;
            }

            // We need the service worker registration to check for a subscription
            navigator.serviceWorker.ready.then((registration) => {
                // Do we already have a push message subscription?
                registration.pushManager.getSubscription().then((subscription) => {
                    // Enable any UI which subscribes / unsubscribes from
                    // push messages.
                    this._changePushButtonState('disabled');

                    if (!subscription) {
                        this._initPushNotifications()
                        return;
                    }

                    // Keep your server in sync with the latest endpoint
                    this._pushSendSubscriptionToServer(subscription, 'update');

                    // Set your UI to show they have subscribed for push messages
                    this._changePushButtonState('enabled');
                }).catch((err) => (console.warn('[SW] Error during getSubscription()', err)))
            })
        },

        _pushSendSubscriptionToServer(subscription, state) {
            const form = new FormData()
            form.append('state', state)
            form.append('endpoint', subscription.endpoint)
            const key = subscription.getKey('p256dh');
            const token = subscription.getKey('auth');
            form.append('key', btoa(String.fromCharCode.apply(null, new Uint8Array(key))))
            form.append('token', btoa(String.fromCharCode.apply(null, new Uint8Array(token))))
            fetch(`/index.php?option=com_webpush&task=subscribe`, {
                method: 'POST',
                body: form,
            }).then((resp) => resp.json()).then(console.log).catch(console.error)

            return true
        },

        _initPushNotifications() {
            const permission = Notification.permission;
            if (permission !== 'granted') {
                throw new Error('We weren\'t granted permissions.')
            }
            navigator.serviceWorker.ready.then((registration) => registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this._urlBase64ToUint8Array(webPushPublicKey)
            })).then((subscription) => {
                this._pushSendSubscriptionToServer(subscription, 'create')
            }).catch(error => {
                if (Notification.permission === 'denied') {
                    console.warn('[SW] Notifications are not allowed by the user.');
                    this._changePushButtonState('incompatible');
                } else {
                    console.error('[SW] Unable to subscribe to notifications.', error);
                    this._changePushButtonState('disabled');
                }
            })
        },

        _urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/-/g, '+')
                .replace(/_/g, '/');

            const rawData = atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        },

        _subscribe(event) {
            this._changePushButtonState('computing')
            window.Notification.requestPermission().then(permission => {
                console.log('Notifications permission:', permission)
                if (permission === 'granted') {
                    navigator.serviceWorker.ready.then(registration => {
                        registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: this._urlBase64ToUint8Array(webPushPublicKey)
                        }).then(subscription => {
                            // The subscription was successful
                            this._changePushButtonState('enabled');

                            // on a la subscription, il faut l'enregistrer en BDD
                            return this._pushSendSubscriptionToServer(subscription, 'create');
                        }).catch(error => {
                            if (Notification.permission === 'denied') {
                                // The user denied the notification permission which
                                // means we failed to subscribe and the user will need
                                // to manually change the notification permission to
                                // subscribe to push messages
                                console.warn('[SW] Notifications are not allowed by the user.');
                                this._changePushButtonState('incompatible');
                            } else {
                                // A problem occurred with the subscription; common reasons
                                // include network errors, and lacking gcm_sender_id and/or
                                // gcm_user_visible_only in the manifest.
                                console.error('[SW] Unable to subscribe to notifications.', e);
                                this._changePushButtonState('disabled');
                            }
                        })
                    })
                } else {
                    // The subscription was successful
                    this._changePushButtonState('disabled');
                }
            }).catch(error => (console.log('Error requesting for notification permission:', error)))
        },

        _unsubscribe(event) {
            this._changePushButtonState('computing');
            navigator.serviceWorker.ready.then(registration => {
                // To unsubscribe from push messaging, you need get the
                // subscription object, which you can call unsubscribe() on.
                registration.pushManager.getSubscription().then(subscription => {
                    if (!subscription) {
                        this._changePushButtonState('disabled')
                        return;
                    }

                    this._pushSendSubscriptionToServer(subscription, 'delete');
                    subscription.unsubscribe().then(successful => {
                        this._changePushButtonState('disabled')
                    }).catch(e => {
                        // We failed to unsubscribe, this can lead to
                        // an unusual state, so may be best to remove
                        // the users data from your data store and
                        // inform the user that you have done so
                        console.log('[SW] Error while unsubscribing to notifications: ', e);
                        this._changePushButtonState('disabled');
                    })
                }).catch(e => {
                    console.error('[SW] Error while unsubscribing from notifications.', e);
                })
            })
        },
    }

    EnablePushNotifications.init()
})()