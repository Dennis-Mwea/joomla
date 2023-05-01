/**
 * 2010-2018 Webkul.
 *
 * NOTICE OF LICENSE
 *
 * All right is reserved,
 * Please go through this link for complete license : https://store.webkul.com/license.html
 *
 *
 *  @author    Webkul IN <support@webkul.com>
 *  @copyright 2010-2018 Webkul IN
 *  @license   https://store.webkul.com/license.html
 */

var url                 ='';
var wkPwaVersion        = 'v1.0';
var staticCacheName     = 'wk-joomla-pwa-static-' + wkPwaVersion;
var dynamicCacheName    = 'wk-joomla-pwa-dynamic-' + wkPwaVersion;

self.addEventListener('install', function(e) {
    e.waitUntil(
        self.skipWaiting()
    );
});

self.addEventListener('activate', function(e) {
    e.waitUntil(
        // Get all the cache keys (keyList)
        caches.keys().then(function(keyList) {
            return Promise.all(keyList.map(function(key) {
                // If a cached item is saved under a previous cacheName
                if (key !== staticCacheName && key !== dynamicCacheName) {
                    // Delete that cached file
                    return caches.delete(key);
                }
            }));
        })
    );
    return self.clients.claim();
});

self.addEventListener('fetch', function(event) {
    if (event.request.method == 'POST') {
        return;
    }

    var requestUrl = new URL(event.request.url);

    if (requestUrl.pathname == '/wkpwa_sw.js' || (requestUrl.pathname.indexOf('.mp4') > -1)) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then(function(resp) {
            return fetch(event.request).then(function(response) {
                return caches.open(dynamicCacheName).then(function(cache) {
                    if((event.request.url.indexOf('http') === 0)){
                        cache.put(event.request, response.clone());
                    }
                    return response;
                });
            }).catch(function(rejectMsg) {
                return resp || function() {
                };
            });
        }).catch(function() {
            return caches.match('Error');
        })
    );
});
