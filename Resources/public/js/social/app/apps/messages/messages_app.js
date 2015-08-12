define(["app"],
    function(App, IndexController){
        App.module("MessagesApp", function(MessagesApp, App, Backbone, Marionette, $, _){
            MessagesApp.startWithParent = false;
        });

        App.module("MessagesApp", function(MessagesApp, App, Backbone, Marionette, $, _){
            MessagesApp.Router = Marionette.AppRouter.extend({
                appRoutes: {
                    "me/messages/": "index",
                    "me/messages/sent": "sent",
                    "me/messages/compose": "compose",
                    "me/messages/:threadId": "showThread"
                }
            });

            var requireAndStartSubApp = function(scripts, callback){
                App.startSubApp("MessagesApp");
                requirejs(scripts, callback);
            };

            var API = {
                index: function(){
                    requireAndStartSubApp(["apps/messages/index/index_controller"], function(IndexController){
                        IndexController.Index();
                    });
                },
                sent: function(){
                    requireAndStartSubApp(["apps/messages/index/index_controller"], function(IndexController){
                        IndexController.Sent();
                    });
                },
                showThread: function(threadId){
                    requireAndStartSubApp(["apps/messages/index/index_controller"], function(IndexController){
                        IndexController.ShowThread(threadId);
                    });
                },
                compose: function(username, noLayout){
                    requireAndStartSubApp(["apps/messages/index/index_controller"], function(IndexController){
                        IndexController.Compose(username, noLayout);
                    });
                }
            };

            App.on("messages:index", function(){
                API.index();
            });

            App.on("messages:sent", function(){
                API.sent();
            });

            App.on("messages:showThread", function(threadId){
                API.showThread(threadId);
            });

            App.on("messages:compose", function(username, noLayout){
                API.compose(username, noLayout);
            });

            App.addInitializer(function(){
                new MessagesApp.Router({
                    controller: API
                });
            });
        });

        return App.MessagesApp;
    });