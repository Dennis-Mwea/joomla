// Scripts for firebase and firebase messaging
// importScripts('https://www.gstatic.com/firebasejs/8.2.0/firebase-app.js');
// importScripts('https://www.gstatic.com/firebasejs/8.2.0/firebase-messaging.js');

self.addEventListener("push", function (i) {
    var t = i.data.json(), n = t.notification, o = n.title,
        a = n.body, c = n.icon, l = n.title, t = {url: {clickurl: n.click_action}};
    i.waitUntil(self.registration.showNotification(o, {
        body: a, icon: c, tag: l,
        data: t
    }))
})

self.addEventListener("notificationclick", function (i) {
    i.notification.close();
    var t = i.notification.data.url, n = t.clickurl;
    i.waitUntil(clients.openWindow(n))
});

self.importScripts("/wkpwa_sw.js");
