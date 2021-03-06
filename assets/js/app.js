/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import '../css/app.scss';


// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import $ from 'jquery';

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

// The JS below changes the file upload area to the name of the file which is being uploaded
$('.custom-file-input').on('change', function(event) {
    var inputFileSystem = event.currentTarget;
    $(inputFileSystem).parent()
        .find('.custom-file-label')
        .html(inputFileSystem.files[0].name)
});

$(document).ready(function(){
    setInterval(function(){
        $("#msg-form").load(window.location.href + " #msg-form" );
    }, 10000);
});

import * as firebase from 'firebase/app';
import 'firebase/messaging';
import 'firebase/firestore';

var config = {
    apiKey: "AIzaSyDStPeaO_aqYK8Stc-XX-KtUk4vuPPWHvs",
    authDomain: "graduatetask.firebaseapp.com",
    projectId: "graduatetask",
    databaseURL: "https://graduatetask.firebaseio.com",
    messagingSenderId: "370767289258",
    appId: "1:370767289258:web:88e162f1c214b6123f69d3"
};

firebase.initializeApp(config);

const db = firebase.firestore();
const messaging = firebase.messaging();
messaging.usePublicVapidKey("BP_Z9fw3y6-SxTSiwDVBT6Q7s0mkoz9UEmC9XtSl2jCtBpZEEht0jghk7giyqQ68JMemOvoap0VehiyC1yo2KMA");
messaging.requestPermission()
    .then(function () {
        console.log('Have perms');
        return messaging.getToken();
    })
    .then(function (token) {
        saveToken(token)
    })
    .catch(function (err) {
        console.log('Error occured');
    });

function saveToken(token) {
    db.collection('device').doc('token').set({
        token: token
    })
        .then(function () {
            console.log('Token has been saved')
        });
}

messaging.onMessage(function (payload) {
    console.log('onMessage: ', payload);
});





