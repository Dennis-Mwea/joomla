(function () {
    var isPushEnabled = false

    var EnablePushNotifications = {
        _pushButton: null,

        init: function () {
            var _this = this
            window.addEventListener('load', function () {
                _this._pushButton = document.querySelector('.notification-toggler');
                if (_this._pushButton) {
                    _this._pushButton.addEventListener('click', (event) => {
                        _this._initSubscriptionToggler(event)
                    })
                }

                var testBtn = document.querySelector('.test-notification')
                if (testBtn) {
                    testBtn.addEventListener('click', _this._sendTestNotification.bind(_this));
                }

                // register the service worker
                if (('serviceWorker' in navigator)) {
                    navigator.serviceWorker.register('/webpush-sw.js', {scope: "./"}).then(() => {
                        console.log('[SW] Registered service worker')
                        _this._pushInitialiseState();
                        _this._initPostMessageListener();
                    }, (e) => {
                        console.error('[SW] Oups...', e);
                        _this._changePushButtonState('incompatible');
                    })
                } else {
                    console.warn('[SW] Service workers are not yet supported by _this browser.');
                    _this._changePushButtonState('incompatible');
                }
            })
        },

        _initSubscriptionToggler: function (event) {
            if (isPushEnabled) {
                this._unsubscribe(event)
            } else {
                this._subscribe(event)
            }
        },

        _changePushButtonState: function (state) {
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

        _initPostMessageListener: function () {
            const onRefreshNotifications = function () {
                console.log('Refresh notifications');
            };

            const onRemoveNotifications = function () {
                console.log('Remove notifications');
            };

            navigator.serviceWorker.addEventListener('message', function (event) {
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
            });
        },

        _pushInitialiseState: function () {
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
            var _this = this
            navigator.serviceWorker.ready.then(function (registration) {
                // Do we already have a push message subscription?
                registration.pushManager.getSubscription().then(function (subscription) {
                    // Enable any UI which subscribes / unsubscribes from
                    // push messages.
                    _this._changePushButtonState('disabled');

                    if (!subscription) {
                        _this._initPushNotifications();
                        return;
                    }

                    // Keep your server in sync with the latest endpoint
                    _this._pushSendSubscriptionToServer(subscription, 'update');

                    // Set your UI to show they have subscribed for push messages
                    _this._changePushButtonState('enabled');
                }).catch(function (err) {
                    console.warn('[SW] Error during getSubscription()', err);
                });
            })
        },

        _pushSendSubscriptionToServer: function (subscription, state) {
            var form = new FormData()
            form.append('state', state)
            form.append('endpoint', subscription.endpoint)
            var key = subscription.getKey('p256dh');
            var token = subscription.getKey('auth');
            form.append('key', btoa(String.fromCharCode.apply(null, new Uint8Array(key))))
            form.append('token', btoa(String.fromCharCode.apply(null, new Uint8Array(token))))
            fetch(`/index.php?option=com_webpush&task=subscribe`, {
                method: 'POST',
                body: form,
            }).then(function (resp) {
                return resp.json();
            }).then(function (resp) {
                console.log(resp);
            }).catch(function (error) {
                console.error(error);
            })

            return true
        },

        _initPushNotifications: function () {
            var permission = Notification.permission;
            if (permission !== 'granted') {
                throw new Error('We weren\'t granted permissions.')
            }
            var _this = this
            navigator.serviceWorker.ready.then(function (registration) {
                return registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: _this._urlBase64ToUint8Array(webPushPublicKey),
                });
            }).then(function (subscription) {
                _this._pushSendSubscriptionToServer(subscription, 'create')
            }).catch(function (error) {
                if (Notification.permission === 'denied') {
                    console.warn('[SW] Notifications are not allowed by the user.');
                    _this._changePushButtonState('incompatible');
                } else {
                    console.error('[SW] Unable to subscribe to notifications.', error);
                    _this._changePushButtonState('disabled');
                }
            })
        },

        _urlBase64ToUint8Array(base64String) {
            var padding = '='.repeat((4 - base64String.length % 4) % 4);
            var base64 = (base64String + padding)
                .replace(/-/g, '+')
                .replace(/_/g, '/');

            var rawData = atob(base64);
            var outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        },

        _subscribe: function (event) {
            this._changePushButtonState('computing')
            var _this = this
            window.Notification.requestPermission().then(function (permission) {
                console.log('Notifications permission:', permission)
                if (permission === 'granted') {
                    navigator.serviceWorker.ready.then(function (registration) {
                        registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: _this._urlBase64ToUint8Array(webPushPublicKey)
                        }).then(function (subscription) {
                            // The subscription was successful
                            _this._changePushButtonState('enabled');

                            // on a la subscription, il faut l'enregistrer en BDD
                            return _this._pushSendSubscriptionToServer(subscription, 'create');
                        }).catch(function () {
                            if (Notification.permission === 'denied') {
                                console.warn('[SW] Notifications are not allowed by the user.');
                                _this._changePushButtonState('incompatible');
                            } else {
                                console.error('[SW] Unable to subscribe to notifications.', e);
                                _this._changePushButtonState('disabled');
                            }
                        })
                    })
                } else {
                    // The subscription was successful
                    _this._changePushButtonState('disabled');
                }
            }).catch(function (error) {
                console.log('Error requesting for notification permission:', error);
            });
        },

        _unsubscribe: function (event) {
            this._changePushButtonState('computing');
            navigator.serviceWorker.ready.then(registration => {
                // To unsubscribe from push messaging, you need get the
                // subscription object, which you can call unsubscribe() on.
                var _this = this;
                registration.pushManager.getSubscription().then(function (subscription) {
                    if (!subscription) {
                        _this._changePushButtonState('disabled')
                        return;
                    }

                    _this._pushSendSubscriptionToServer(subscription, 'delete');
                    subscription.unsubscribe().then(function (successful) {
                        _this._changePushButtonState('disabled')
                    }).catch(function (e) {
                        console.log('[SW] Error while unsubscribing to notifications: ', e);
                        _this._changePushButtonState('disabled');
                    });
                }).catch(function (e) {
                    console.error('[SW] Error while unsubscribing from notifications.', e);
                });
            });
        },

        _sendTestNotification: function (event) {
            var form = new FormData()
            form.append('title', 'Test Notification')
            form.append('message', 'This is a test notification to my devices')
            fetch(`/index.php?option=com_webpush&task=sendMessages`, {
                body: form,
                method: 'POST',
            }).then(function (resp) {
                return resp.json();
            }).then(function (resp) {
                console.log(resp);
            }).catch(function(error) {
                console.error(error)
            })
        }
    }

    EnablePushNotifications.init()
})()