/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../scss/app.scss');

// loads the Bootstrap jQuery plugins
/*import 'bootstrap-sass/assets/javascripts/bootstrap/transition.js';
import 'bootstrap-sass/assets/javascripts/bootstrap/alert.js';
import 'bootstrap-sass/assets/javascripts/bootstrap/collapse.js';
import 'bootstrap-sass/assets/javascripts/bootstrap/dropdown.js';
import 'bootstrap-sass/assets/javascripts/bootstrap/modal.js';*/
import 'jquery'
import './highlight.js';
import './doclinks.js';
import 'bs-custom-file-input/dist/bs-custom-file-input.js';
//global.bsCustomFileInput = bsCustomFileInput;

const $ = require('jquery');
global.$ = global.jQuery = $;
//require('bootstrap/dist/js/bootstrap-bundle');
require('bootstrap');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});


console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
