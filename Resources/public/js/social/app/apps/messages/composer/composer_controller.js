define([
    "app"
    ],function(App){
        App.module("MessagesApp.Composer", function(Composer, App, Backbone, Marionette, $, _){
            Composer.Controller = {
                Compose: function(username){
                    requirejs(["entities/threads", "apps/messages/composer/compose_view"], function(Entities, View){
                        var newThread = new Entities.NewThread({recipient: username ? username : ''});
                        var composeView = new View.ComposeView({model: newThread});

                        composeView.on("compose:submit", function(newThread){
                            newThread.save(null, {
                                success: function(){
                                    App.trigger("messages:index");
                                }
                            });
                        });

                        App.body_container_main_Region.show(composeView);
                    });
                }
            };
        });
        return App.MessagesApp.Composer.Controller;
    });