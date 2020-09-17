/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';

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

// $(document).ready(function(){
//     setInterval(function(){
//         $("#msg-form").load(window.location.href + " #msg-form" );
//     }, 10000);
// });


// import * as firebase from 'firebase/app';
// import 'firebase/firestore';
//
// firebase.initializeApp({
//     apiKey: "AIzaSyDStPeaO_aqYK8Stc-XX-KtUk4vuPPWHvs",
//     authDomain: "graduatetask.firebaseapp.com",
//     projectId: "graduatetask"
// });
//
// const db = firebase.firestore();

// (function () {
//     function addData() {
//         db.collection("cities").doc("LA").set({
//             name: "Los Angeles",
//             state: "CA",
//             country: "USA"
//         })
//             .then(function() {
//                 console.log("Document successfully written!");
//             })
//             .catch(function(error) {
//                 console.error("Error writing document: ", error);
//             });
//     }
//     document.getElementById('submit_form').addEventListener('click', addData, true);
// })();



// var docRef = db.collection("Users").doc("23");
//
// (function () {
//     function getData() {
//         docRef.get().then(function(doc) {
//             if (doc.exists) {
//                 console.log("Document data:", doc.data());
//             } else {
//                 // doc.data() will be undefined in this case
//                 console.log("No such document!");
//             }
//         }).catch(function(error) {
//             console.log("Error getting document:", error);
//         });
//     }
//     document.getElementById('submit_form').addEventListener('click', getData, true);
// })();





