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

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
