({
    appDir: '../app',
    baseUrl: './',
    mainConfigFile: '../app/main.js',
    dir: '../www-built',
    onBuildWrite: function (moduleName, path, contents) {
        if ( moduleName === 'main' )
        {
            // perform transformations on the original source
            contents = contents.replace( /#version/i, new Date().toString() );
        }
        return contents.replace(/console.log(.*);/g, '');
    },
    //preserveLicenseComments: false,
    //optimize: "none",
    modules: [
        //First set up the common build layer.
        {
            //module names are relative to baseUrl
            name: 'main',
            //List common dependencies here. Only need to list
            //top level dependencies, "include" will find
            //nested dependencies.
            include: [
                'app',
                'apps/messages/messages_app',
                'apps/messages/index/index_controller',
                'text!translations/es.json',
                'text!translations/en.json'
            ]
        },
        {
            name: 'apps/messages/index/index_controller',
            include: [
                'apps/messages/composer/compose_view',
                'text!apps/messages/composer/templates/reply.html',
                'text!apps/messages/composer/templates/reply_form.html'
            ]
        }
    ],
    paths: {
        backbone: "../vendor/bower/backbone/backbone",
        handlebars: "../vendor/bower/handlebars/handlebars.min",
        moment: "../vendor/bower/moment/min/moment-with-langs.min",
        // jquery: "../vendor/bower/jquery/dist/jquery.min",
        jquery: "../vendor/bower/jquery/dist/jquery.min"
        // jquery: "empty:",
    },
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
            "exports":"Marionette"
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
        }
    },
    wrapShim: true
})