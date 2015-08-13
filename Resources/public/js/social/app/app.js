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
            var language = 'en';

            if(typeof options.lang != 'undefined'){
                language = options.lang;
            }

            moment.lang(language);

            require(["text!translations/"+language+".json"], function(tranlationsText){
                var tranlations = JSON.parse(tranlationsText);
                var trans = window.libTranslate.getTranslationFunction(tranlations);

                Handlebars.registerHelper("t", function(){
                    return trans.apply(null, arguments);
                });
            });

            require(["apps/messages/messages_app"], function(){

                Backbone.history.start({ pushState: true, root: App.root });

                if (Backbone.history && Backbone.history._hasPushState) {

                    // Use delegation to avoid initial DOM selection and allow all matching elements to bubble
                    $(document).delegate("a", "click", function(evt) {
                        if ($(this).hasClass('no_navigate') || $(this).attr('target') == '_blank'){
                            return;
                        }
                        // Get the anchor href and protcol
                        var href = $(this).attr("href");
                        var protocol = this.protocol + "//";



                        if (typeof(href) != "undefined"){
                            if(href.indexOf("/auth/logout") != -1) return;
                            // Ensure the protocol is not part of URL, meaning its relative.
                            // Stop the event bubbling to ensure the link will not cause a page refresh.
                            if (href.slice(protocol.length) !== protocol) {
                                evt.preventDefault();

                                // Note by using Backbone.history.navigate, router events will not be
                                // triggered. If this is a problem, change this to navigate on your
                                // router.

                                Backbone.history.navigate(href, true);

                                return false;
                            }
                        }
                    });
                }

                Backbone.history.on("route", function(name, id){
                    //Esto funciona cuando le doy para adelante y para atrás en el navegador
                    //en el navigate no
                    App.onPostNavigate();
                });

                App.vent.trigger('socialapp:loaded');
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