// libreria de traducción pillada de https://github.com/musterknabe/translate.js

define(['marionette', 'backbone', 'underscore', 'handlebars', 'moment', 'iecors', 'translate'],
    function (Marionette, Backbone, _, Handlebars, moment) {
        var originalBackboneModelParse = Backbone.Model.prototype.parse;

        Backbone.Model.prototype.parse = function(){
            var attrs = originalBackboneModelParse.apply(this, arguments);
            var iso8601Pattern = /(\d{4})-(\d{2})-(\d{2})T(\d{2})\:(\d{2})\:(\d{2})[+-](\d{4})/;

            var convertDateStringToDate = function(attrs){
                _.each(attrs, function(value, key) {
                    if(_.isString(value) && iso8601Pattern.test(value)){
                        attrs[key] = new Date(value);
                    }else if(_.isObject(value)){
                        convertDateStringToDate(attrs[key]);
                    }
                });
            };

            convertDateStringToDate(attrs);

            return attrs;
        };

        Handlebars.registerHelper("formatDate", function(datetime, format) {
            return moment(datetime).format(format);
        });

        Handlebars.registerHelper("relativeFromNow", function(datetime) {
            return moment(datetime).calendar();
        });

        Handlebars.registerHelper("globalVar", function(name){
            return window[name];
        });


        var App = new Marionette.Application();

        function isMobile() {
            var userAgent = navigator.userAgent || navigator.vendor || window.opera;
            return ((/iPhone|iPod|iPad|Android|BlackBerry|Opera Mini|IEMobile/).test(userAgent));
        }

        //Organize Application into regions corresponding to DOM elements
        //Regions can contain views, Layouts, or subregions nested as necessary
        App.addRegions({
            headerRegion: "header",
            leftRegion: "#sidebar",
            body_container_main_Region: "#body_container_main",
            container_center_Region:"#body_container_main",
            body_container_chat_Region:"#body_container_chat",
            // container_center_Region:"#body_container_center",
            container_left_Region:"#body_container_left"
        });

        App.navigate = function(route, options){
            options = options || {};
            Backbone.history.navigate(route, options);
            //App.onPostNavigate();

        };


        $.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    if(settings.url.indexOf(window.kiwi_server) == -1){
                        xhr.setRequestHeader('Authorization', 'Bearer ' + window.token);
                    }
                }
         });
     

        App.mobile = isMobile();
        if(window.debug_mode) App.root = "/app_dev.php/";
        else App.root = "/";

        App.startSubApp = function(appName, args){
            var currentApp = appName ? App.module(appName) : null;
            if (App.currentApp === currentApp){ return; }

            if (App.currentApp){
                App.currentApp.stop();
            }

            App.currentApp = currentApp;
            if(currentApp){
                currentApp.start(args);
            }
        };

        // After initialize
        App.on('initialize:after', function (options) {
            require(["apps/messages/messages_app"], function(){

                App.vent.trigger('socialapp:loaded');
            });

            var routes = options.routes || {};

            var route = function(options){
                var params = options.hash;
                var r = routes[params.route] || '';

                if(typeof params != 'undefined'){
                    for(param in params){
                        r = r.replace('{' + param + '}', params[param]);
                    }
                }

                return r;
            };

            Handlebars.registerHelper("route", route);
        });

        App.on('start', function(options){
            require(["text!translations/es.json", "text!translations/en.json"], function(tranlationsTextEs, tranlationsTextEn){
                var language = 'en';

                if(typeof options != 'undefined' && typeof options.lang != 'undefined'){
                    language = options.lang;
                }

                moment.lang(language);

                var translationsText = "{}";

                if(language == 'es'){
                    translationsText = tranlationsTextEs;
                }else if(language == 'en'){
                    translationsText = tranlationsTextEn;
                }

                var tranlations = JSON.parse(translationsText);
                var trans = window.libTranslate.getTranslationFunction(tranlations);
                var _translate = function(){
                    return trans.apply(null, arguments);
                };

                App.reqres.setHandler("trans", _translate);

                Handlebars.registerHelper("t", _translate);
            });
        });

        //prefente links from doing its default behaviour and send an marionette event instead
        $(document).on('click', 'a[data-marionette-event]', function(e){
            var appEvent = $(this).attr('data-marionette-event');
            App.trigger(appEvent);

            e.stopPropagation();
            e.preventDefault();
            return false;
        });


        return App;
    });