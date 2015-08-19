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
    }

})