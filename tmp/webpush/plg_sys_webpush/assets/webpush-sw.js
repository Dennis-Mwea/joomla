(function () {
    'use strict'

    let staticCacheName = `pwa-v${new Date().getTime()}`

    const WebPush = {
        _filesToCache: [
            '/media/com_webpush/pwa/icons/icon-72x72.png',
            '/media/com_webpush/pwa/icons/icon-96x96.png',
            '/media/com_webpush/pwa/icons/icon-128x128.png',
            '/media/com_webpush/pwa/icons/icon-144x144.png',
            '/media/com_webpush/pwa/icons/icon-152x152.png',
            '/media/com_webpush/pwa/icons/icon-192x192.png',
            '/media/com_webpush/pwa/icons/icon-384x384.png',
            '/media/com_webpush/pwa/icons/icon-512x512.png',
            '/media/com_webpush/pwa/splash/splash-640x1136.png',
            '/media/com_webpush/pwa/splash/splash-750x1334.png',
            '/media/com_webpush/pwa/splash/splash-1242x2208.png',
            '/media/com_webpush/pwa/splash/splash-1125x2436.png',
            '/media/com_webpush/pwa/splash/splash-828x1792.png',
            '/media/com_webpush/pwa/splash/splash-1242x2688.png',
            '/media/com_webpush/pwa/splash/splash-1536x2048.png',
            '/media/com_webpush/pwa/splash/splash-1668x2224.png',
            '/media/com_webpush/pwa/splash/splash-1668x2388.png',
            '/media/com_webpush/pwa/splash/splash-2048x2732.png'
        ],

        init() {
            self.addEventListener('install', this.serviceWorkerInstalled.bind(this))
            self.addEventListener('activate', this.serviceWorkerActivated.bind(this))
            self.addEventListener('fetch', this.serviceWorkerFetching.bind(this))
            self.addEventListener('push', this.notificationPush.bind(this))
            self.addEventListener('notificationclick', this.notificationClick.bind(this))
            self.addEventListener('notificationclose', this.notificationClose.bind(this))
        },

        serviceWorkerInstalled(event) {
            self.skipWaiting()
            event.waitUntil(caches.open(staticCacheName).then(cache => cache.addAll(this._filesToCache)))
        },

        serviceWorkerActivated(event) {
            event.waitUntil(
                caches.keys().then(cacheNames => Promise.all(cacheNames
                    .filter(cacheName => (cacheName.startsWith('pwa')))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))))
            )
        },

        serviceWorkerFetching(event) {
            event.respondWith(
                caches.match(event.requests)
                    .then(response => response || fetch(event.request))
                    .catch(() => caches.match('offline'))
            )
        },

        /**
         * Handle notification push event.
         *
         * https://developer.mozilla.org/en-US/docs/Web/Events/push
         *
         * @param {NotificationEvent} event
         */
        notificationPush(event) {
            if (!(self.Notification && self.Notification.permission === 'granted')) {
                // Notifications are not supported or permission is denied
                return;
            }

            if (event.data) {
                event.waitUntil(
                    this.sendNotification(event.data.json())
                )
            }
        },

        /**
         * Handle notification click event.
         *
         * https://developer.mozilla.org/en-US/docs/Web/Events/notificationclick
         *
         * @param {NotificationEvent} event
         */
        notificationClick(event) {
            if (event.action === 'some_action') {
                // Do something...
            } else {
                self.clients.openWindow('/')
            }
        },

        /**
         * Handle notification close event (Chrome 50+, Firefox 55+).
         *
         * https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerGlobalScope/onnotificationclose
         *
         * @param {NotificationEvent} event
         */
        notificationClose(event) {
            self.registration.pushManager.getSubscription().then(subscription => {
                if (subscription) {
                    this.dismissNotification(event, subscription)
                }
            })
        },

        /**
         * Send notification to the user.
         *
         * https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerRegistration/showNotification
         *
         * @param {PushMessageData|Object} data
         */
        sendNotification(data,) {
            return self.registration.showNotification(data.title, data)
        },

        /**
         * Send request to server to dismiss a notification.
         *
         * @param  {NotificationEvent} event
         * @param  {String} subscription.endpoint
         * @return {Response}
         */
        dismissNotification({notification}, {endpoint}) {
            if (!notification.data || !notification.data.id) {
                return;
            }

            // Send a request to the server to mark the notification as read.
            fetch(`/notifications/${notification.data.id}/dismiss`, {
                body: {endpoint: endpoint},
                method: 'POST',
            }).then(resp => resp.json()).then(console.log)
        }
    }

    WebPush.init()
})()
