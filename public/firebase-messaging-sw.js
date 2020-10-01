importScripts('https://www.gstatic.com/firebasejs/7.20.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.20.0/firebase-messaging.js');

firebase.initializeApp({
    apiKey: "AIzaSyDStPeaO_aqYK8Stc-XX-KtUk4vuPPWHvs",
    authDomain: "graduatetask.firebaseapp.com",
    projectId: "graduatetask",
    databaseURL: "https://graduatetask.firebaseio.com",
    messagingSenderId: "370767289258",
    appId: "1:370767289258:web:88e162f1c214b6123f69d3"
});

const messaging = firebase.messaging();

// if ('serviceWorker' in navigator) {
//     navigator.serviceWorker.register('../firebase-messaging-sw.js')
//         .then(function(registration) {
//             console.log('Registration successful, scope is:', registration.scope);
//         }).catch(function(err) {
//         console.log('Service worker registration failed, error:', err);
//     });
// }

messaging.setBackgroundMessageHandler(function (payload) {
    const title = 'New message';
    const options = {
        "notification": {
            "title": title,
            "body": payload.data.notification
        }
    };
    return self.registration.showNotification(title, options);
});

// curl -X POST -H "Authorization: key=AAAAVlNz26o:APA91bENeMsOMTfs1WcZ9h9TrnGXoNxmnVlPoNtrdKoKTEgBE2dRt32tsDQ0rNFQjDEw_htNHTC-VZStSfa4OKb9LWdMz-7lyCi56S46W-GHQ9hSWCV-psHmBqEtfO94IgATnKWJfelh" -H "Content-Type: application/json" -d '{
// "notification": {
//     "title": "Portugal vs. Denmark",
//         "body": "5 to 1",
//         "icon": "firebase-logo.png"
// },
// "to": "c_KiRuQ_u1-mQfY2HscSPI:APA91bF5mZG_jfiPXWeXZ5uZD0XZ_mfJH_KDd75d_lSR-qfuzxTcyP7Zq8fah8-BDOlzn7sDXrSdsrdmuDa6guU7LfwoXaT2K43Q2vva5S6EzbIrO9D3Qabb-qH0CKrhkNVgA6yNrue1"
// }' "https://fcm.googleapis.com/fcm/send"
