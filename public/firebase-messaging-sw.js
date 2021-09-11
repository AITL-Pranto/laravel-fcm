// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.
importScripts('https://www.gstatic.com/firebasejs/7.9.3/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.9.3/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
    apiKey: "AIzaSyBa1etNsQg1wfFoal03iNKmYHbm7EyUhNs",
    authDomain: "laravel-fcm-6b5e3.firebaseapp.com",
    projectId: "laravel-fcm-6b5e3",
    storageBucket: "laravel-fcm-6b5e3.appspot.com",
    messagingSenderId: "662266151598",
    appId: "1:662266151598:web:f8810e26bb39398229a33f",
    measurementId: "G-ETR87GFZZD"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here

    const {title, body} = payload.notification;

    const notificationTitle = title;
    const notificationOptions = {
      body,
      icon: '/firebase-logo.png'
    };

    self.registration.showNotification(notificationTitle,
      notificationOptions);
  });
