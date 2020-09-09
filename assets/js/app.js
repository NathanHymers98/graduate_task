/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

// The JS below changes the file upload area to the name of the file which is being uploaded
$('.custom-file-input').on('change', function(event) {
    var inputFileSystem = event.currentTarget;
    $(inputFileSystem).parent()
        .find('.custom-file-label')
        .html(inputFileSystem.files[0].name)
});
