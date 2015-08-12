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
                    'apps/header/header_app',
                    'apps/leftbar/leftbar_app',
                    'apps/channels/channels_app',
                    'apps/photos/photos_app',
                    'apps/users/users_app',
                    'apps/dashboard/dashboard_app',
                    'apps/chat/chat_app',
                    'apps/messages/messages_app',
                    'apps/static/static_app'
            ]
        },
        // {
        //     name: 'apps/photos/upload/upload_controller',
        //     exclude: ['main']
        // },
        {
            name: 'apps/photos/list/list_controller',
            exclude: ['main']
        },
        {
            name: 'apps/channels/list/list_controller',
            exclude: ['main']
        },
        {
            name: 'apps/channels/edit/edit_controller',
            exclude: ['main']
        },
        {
            name: 'apps/channels/show/show_controller',
            exclude: ['main']
        },
        {
            name: 'apps/users/list/list_controller',
            exclude: ['main', 'apps/commons/pagination/paginate']
        },
        {
            name: 'apps/users/show/show_controller',
            exclude: ['main', 'apps/commons/pagination/paginate']
        },
        {
            name: 'apps/users/index/index_controller',
            exclude: ['main', 'apps/commons/pagination/paginate']
        },
        {
            name: 'apps/commons/pagination/paginate',
            exclude: ['main']
        }
        // {
        //     name: 'apps/users_app',
        //     include: ['']
        // }
        //Now set up a build layer for each page, but exclude
        //the common one. "exclude" will exclude nested
        //the nested, built dependencies from "common". Any
        //"exclude" that includes built modules should be
        //listed before the build layer that wants to exclude it.
        //"include" the appropriate "app/main*" module since by default
        //it will not get added to the build since it is loaded by a nested
        //require in the page*.js files.
        // {
        //     //module names are relative to baseUrl/paths config
        //     name: 'apps/channels/channels_app',
        //     include: ['app', 'marionette'],
        //     exclude: ['main']
        // },

        // {
        //     //module names are relative to baseUrl
        //     name: '../page2',
        //     include: ['app/main2'],
        //     exclude: ['../common']
        // }

    ],
    paths: {
        backbone: "../vendor/bower/backbone/backbone",
        handlebars: "../vendor/bower/handlebars/handlebars.min",
        moment: "../vendor/bower/moment/min/moment-with-langs.min",
        // jquery: "../vendor/bower/jquery/dist/jquery.min",
        jquery: "../vendor/bower/jquery/dist/jquery.min",
        // jquery: "empty:",
    }

})