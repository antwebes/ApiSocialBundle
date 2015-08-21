// Break out the application running from the configuration definition to
// assist with testing.
/*!
* #version
*/
// This is the runtime configuration file.  It complements the Gruntfile.js by
// supplementing shared properties.
require.config({
    config: {
        text:{
            useXhr: function (url, protocol, hostname, port) {
                return true;
            }
        }
    },
    // baseUrl: "./",
    paths: {
        // Make ../vendor easier to access.
        "vendor": "../vendor",

        // Almond is used to lighten the output filesize.
        // "almond": "../vendor/bower/almond/almond",

        // Opt for Lo-Dash Underscore compatibility build over Underscore.
        "underscore": "../vendor/bower/lodash/dist/lodash",
        "translate": "lib/translate",
        "iecors": "lib/iecors",
        // Map remaining ../vendor dependencies.
        "jquery": ["//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min",
                    "../vendor/bower/jquery/dist/jquery.min"],
        "backbone": ["../vendor/bower/backbone/backbone"],
        "backbone-forms": "../vendor/bower/backbone-forms/distribution.amd/backbone-forms",
        "backbone-bootstrap": "../vendor/bower/backbone-forms/distribution.amd/templates/bootstrap3",
        //"marionette": "../vendor/bower/marionette/lib/backbone.marionette.min",
        //es necesario la librería AMD porque es la única que funciona al comprimir, ojo en la version 2 de marionette no hay amd
        'marionette': '../vendor/bower/marionette/lib/core/amd/backbone.marionette.min',
        'backbone.babysitter': '../vendor/bower/backbone.babysitter/lib/backbone.babysitter',
        'backbone.wreqr': '../vendor/bower/backbone.wreqr/lib/backbone.wreqr',

        "list"           : "lib/list-form-amd",
        "text"           : "lib/text",
        "jquery.updater" : "lib/jquery.updater",
        "paginator"      : "../vendor/bower/backbone.paginator/lib/backbone.paginator.min",

        'jquery_iframe_transport': '../vendor/bower/jquery-file-upload/js/jquery.iframe-transport',
        'jquery.fileupload': '../vendor/bower/jquery-file-upload/js/jquery.fileupload',
        'jquery.fileupload-image': '../vendor/bower/jquery-file-upload/js/jquery.fileupload-image',
        'jquery.fileupload-process': '../vendor/bower/jquery-file-upload/js/jquery.fileupload-process',
        "jquery.ui.widget" : "../vendor/bower/jquery-file-upload/js/vendor/jquery.ui.widget",
        'load-image': '../vendor/bower/blueimp-load-image/js/load-image',

        'load-image-ios': '../vendor/bower/blueimp-load-image/js/load-image-ios',
        
        'load-image-meta': '../vendor/bower/blueimp-load-image/js/load-image-meta',
        'load-image-exif': '../vendor/bower/blueimp-load-image/js/load-image-exif',

        'canvas-to-blob': '../vendor/bower/blueimp-canvas-to-blob/js/canvas-to-blob',

        "handlebars":  ["http://cdnjs.cloudflare.com/ajax/libs/handlebars.js/1.3.0/handlebars.min" ,
                        "../vendor/bower/handlebars/handlebars.min"
                        ],
        "template"  : "templates",
        "util": "util",
        "jasmine-fixture": "../vendor/bower/jasmine-fixture/dist/jasmine-fixture",
        "moment": ["http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.5.1/moment-with-langs.min",
                "../vendor/bower/moment/min/moment-with-langs.min"],
        
        "bootstrap-modal": "../vendor/bower/bootstrap/js/modal",
        "backbone-bootstrap-modal": "../vendor/bower/backbone.bootstrap-modal/src/backbone.bootstrap-modal",
        "jquery-lazyload": "../vendor/bower/jquery.lazyload/jquery.lazyload.min.js",
        "jasny-fileinput": "../vendor/bower/jasny-bootstrap/js/fileinput",
        "ZeroClipboard": "../vendor/bower/zeroclipboard/ZeroClipboard.min.js",
        "ZeroClipboardSwf": "../vendor/bower/zeroclipboard/ZeroClipboard.swf",
        "FileApi": "../vendor/bower/jquery.fileapi/FileAPI/FileAPI.min.js",
        "FileApi.exif": "../vendor/bower/jquery.fileapi/FileAPI/FileAPI.exif.js",
        "jquery.fileapi": "../vendor/bower/jquery.fileapi/jquery.fileapi.min.js",
        "jquery.jcrop": "../vendor/bower/jquery.fileapi/jcrop/jquery.Jcrop.min.js",
        "jquery.modal": "../vendor/bower/jquery.fileapi/statics/jquery.modal.js",
        "jQuery.XDomainRequest": "../vendor/bower/jQuery.XDomainRequest/jQuery.XDomainRequest",
        "toastr": "../vendor/bower/toastr/toastr.min.js",
        "toastr_css": "../vendor/bower/toastr/toastr.min.css",
        "backbone.autocomplete": "../vendor/bower/backbone-autocomplete/src/backbone.autocomplete",
        "backbone_autocomplete_css": "../vendor/bower/backbone-autocomplete/src/backbone.autocomplete-min",
        'easyXDM': "../vendor/bower/brandymint-easyXDM/easyXDM"
    },
    // bundles: {
    //     'HeaderApp' : ['app']
    // },
    shim: {
        app: ['marionette'],
        // This is required to ensure Backbone works as expected within the AMD
        // environment.
        "backbone": {
            // These are the two hard dependencies that will be loaded first.
            deps: ["jquery", "underscore"],

            // This maps the global `Backbone` object to `require("backbone")`.
            exports: "Backbone"
        },
        "underscore" : {
            deps: ["text"],
            exports : '_'
        },
        "jquery.fileapi": {
          deps: ["FileApi", "jquery.jcrop", "jquery.modal", "FileApi.exif", "jquery"]
        },
        "FileApi.exif": {
            deps: ["FileApi"]
        },
        "paginator": {
            deps: ['backbone'],
            exports: 'Backbone.Paginator'
        },
        "handlebars": {
            exports: 'Handlebars'
        },
        "bootstrap-modal": {
            "deps": ["jquery", "backbone"]
        },
        "backbone-bootstrap-modal":{
            "deps":["jquery", "underscore", "backbone", "bootstrap-modal"]
        },
        //Marionette
        "marionette":{
            "deps":["jquery", "underscore", "backbone"],
            "exports": "Marionette"
        },
        "jasmine-fixture": {
            "deps": ["jquery"]
        },
        "jquery-lazyload": {
            "deps": ["jquery"]
        },
        "jquery.updater": {
            "deps": ["jquery"]
        },
        "backbone.autocomplete": {
            "deps": ["backbone"],
            "exports": "AutoCompleteView"
        },
    }
});

require(["app"],function(App) {
    // Define your master router on the application namespace and trigger all
    // navigation from this instance.
    App.addInitializer(function () {});
    App.start();

    if(typeof window.onSocialApp != 'undefined'){
        window.onSocialApp();
    }
});
