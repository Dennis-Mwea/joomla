// Scripts for firebase and firebase messaging
// importScripts('https://www.gstatic.com/firebasejs/8.2.0/firebase-app.js');
// importScripts('https://www.gstatic.com/firebasejs/8.2.0/firebase-messaging.js');

self.addEventListener("push",function(i){
			  var t=i.data.json(),n=t.notification,o=n.title,
			  a=n.body,c=n.icon,l=n.title,t={url:{clickurl:n.click_action}};
			  i.waitUntil(self.registration.showNotification(o,{body:a,icon:c,tag:l,
			    data:t}))}),

			self.addEventListener("notificationclick",function(i){
			  i.notification.close();var t=i.notification.data.url,n=t.clickurl;
			  i.waitUntil(clients.openWindow(n))});

			  self.importScripts("/wkpwa_sw.js");

// initialize the firebase app
// firebase.initializeApp({
// 	projectId: 'sanify24push',
// 	messagingSenderId: '885960002000',
// 	apiKey: 'AIzaSyAqVMdWT7qV5R4c4bBnT631iBGmpYYVwCQ',
// 	appId: '1:885960002000:web:c35de44adf094256fb51d5',
// })

// // Retrieve firebase messaging
// const messaging = firebase.messaging()

// messaging.onBackgroundMessage(function (payload) {
// 	console.log("Received background message", payload);

// 	const notificationTitle = payload.notification.title;
// 	const notificationOptions = {
// 		body: payload.notification.body
// 	};

// 	self.registration.showNotification(notificationTitle, notificationOptions)
// })
