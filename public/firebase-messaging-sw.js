importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

// Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyDVyiFmcddqmtV4obWNHPBG4tNuVTrY5Po",
    authDomain: "push-notification-e7b2e.firebaseapp.com",
    projectId: "push-notification-e7b2e",
    storageBucket: "push-notification-e7b2e.firebasestorage.app",
    messagingSenderId: "349022740315",
    appId: "1:349022740315:web:75bbe62bb61a00a8b5c47e",
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Initialize Firebase Messaging
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage(function(payload) {
    console.log('Received background message: ', payload);
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        // icon: payload.notification.icon,
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
}
);
