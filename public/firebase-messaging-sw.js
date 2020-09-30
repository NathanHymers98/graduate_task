// importScripts('https://www.gstatic.com/firebasejs/3.4.0/firebase-app.js');
// importScripts('https://www.gstatic.com/firebasejs/3.4.0/firebase-messaging.js');

// firebase.initializeApp({
//     apiKey: "AIzaSyDStPeaO_aqYK8Stc-XX-KtUk4vuPPWHvs",
//     authDomain: "graduatetask.firebaseapp.com",
//     projectId: "graduatetask",
//     databaseURL: "https://graduatetask.firebaseio.com",
//     messagingSenderId: "370767289258",
//     appId: "1:370767289258:web:88e162f1c214b6123f69d3"
// });
//
// const messaging = firebase.messaging();

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('../firebase-messaging-sw.js')
        .then(function(registration) {
            console.log('Registration successful, scope is:', registration.scope);
        }).catch(function(err) {
        console.log('Service worker registration failed, error:', err);
    });
}