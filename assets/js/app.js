/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
//import $ from 'jquery';

// The above import jquery statement is not working. So I will require jQuery normally this way
const $ = require('jquery');
// create global $ and jQuery variables (https://symfony.com/doc/current/frontend/encore/legacy-applications.html)
global.$ = global.jQuery = $;

https://symfony.com/doc/current/frontend/encore/bootstrap.html
// the bootstrap module doesn't export/return anything
require('bootstrap');

//console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

$(document).ready(function() {
    // This is how to work with input of type file and Bootstrap
    // https://getbootstrap.com/docs/4.0/components/forms/#file-browser
    // https://stackoverflow.com/questions/43250263/bootstrap-4-file-input
    $('.custom-file-label').html("Choose file...");
    $('.custom-file-input').on('change', function(e) {
        let fname = e.target.files[0].name;
        let nextSibling = e.target.nextElementSibling;
        if(nextSibling.classList.contains('custom-file-label')) {
            nextSibling.innerHTML = fname;
        }
    });
});
