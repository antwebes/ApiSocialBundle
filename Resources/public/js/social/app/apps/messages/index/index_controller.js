define([
    "app",
    "apps/messages/index/index_view",
    "entities/messages",
    "entities/threads"
    ],function(App, View, Entities){


        App.module("MessagesApp.Index", function(Index, App, Backbone, Marionette, $, _){
            Index.Controller = {
                Index: function(){
                    this.activeBox = 'index';
                    var fetchingThreads = App.request("thread:entities");
                    var func = _.bind(this._showThreadsList, this);
                    
                    this._initLayout();
                    this.layout.setActiveTab('inbox-tab');
                    $.when(fetchingThreads)
                        .done(func)
                        .fail(function(err){
                            requirejs(["apps/commons/errors/generic"],
                                function(GenericErrorView){
                                    App.container_center_Region.show(new GenericErrorView.GenericErrorView({model: err}));
                                }
                            );
                        });
                },
                Sent: function(){
                    this.activeBox = 'sent';
                    var fetchingThreads = App.request("thread:entities:sent");
                    var func = _.bind(this._showThreadsList, this);
                    this._initLayout();
                    this.layout.setActiveTab('sent-tab');
                    $.when(fetchingThreads).done(func);
                },
                _showThreadsList: function(threads){
                    var threadsView = new View.Threads({collection: threads});

                    threadsView.on("itemview:thread:show", function(view){
                        App.trigger("messages:showThread",view.model.get('id'));
                    });

                    this._showInLayout(threadsView);
                },
                ShowThread: function(threadId){
                    var fetchingMessages = App.request("thread:messages", threadId);
                    var self = this;

                    $.when(fetchingMessages).done(function(messages){
                        var messagesView = new View.Messages({collection: messages});

                        var _showReplayThread = _.bind(self._showReplayThread, self);
                        var _deleteThread = _.bind(self._deleteThread, self);
                        var _triggerMessageActiveBox = _.bind(self._triggerMessageActiveBox, self);

                        messagesView.on("thread:reply", _showReplayThread);
                        messagesView.on("thread:delete", _deleteThread);
                        messagesView.on("thread:back", _triggerMessageActiveBox);
                        
                        self._showInLayout(messagesView);
                    });
                },
                _showReplayThread: function(thread){
                    var self = this;
                    requirejs([
                        "apps/messages/composer/compose_view",
                        "text!apps/messages/composer/templates/reply.html",
                        "text!apps/messages/composer/templates/reply_form.html"],
                        function(View, ReplyTemplate, FormTemplate){
                            var message = new Entities.Message({thread: thread});
                            var composeView = new View.ComposeView({model: message, formTemplate: FormTemplate});
                            var callback = _.bind(self._replayThread, self, thread);

                            composeView.on("compose:submit", callback);
                            composeView.on("compose:back", function(){
                                composeView.remove();
                            });

                            self.layout.mainRegion.currentView.showReply(composeView);
                        });
                },
                _deleteThread: function(thread){
                    var self = this;
                    App
                        .request("thread:delete", thread)
                        .done(_.bind(self._triggerMessageActiveBox, self));
                },
                _replayThread: function(thread, message){
                    message.unset('thread');
                    message.save(null, {
                        success: function(){
                            App.trigger("messages:showThread", thread.id);
                        }
                    });
                },
                Compose: function(username, noLayout){
                    var self = this;
                    if(typeof noLayout == 'undefined'){
                        noLayout = false;
                    }

                    requirejs(["entities/threads", "apps/messages/composer/compose_view"], function(Entities, View){
                        var newThread = new Entities.NewThread({recipient: username ? username : ''});
                        var composeView = new View.ComposeView({model: newThread});

                        composeView.on("compose:submit", _.bind(self._sendMessage, self, noLayout));
                        composeView.on("compose:back", _.bind(self._triggerMessageActiveBox, self, noLayout, true));

                        if(noLayout){
                            App.body_container_main_Region.show(composeView);
                        }else{
                            Index.Controller._showInLayout(composeView);
                        }
                    });
                },
                _sendMessage: function(noLayout, newThread){
                    var self = this;
                    newThread.save(null, {
                        success: function(){
                            //search the user recipient to find the id, to send with event to analytics
                            var participants = newThread.get('participants');
                            var idUser;
                            for (var i in participants){
                                if (participants[i].username == newThread.get('recipient')){
                                    idUser = participants[i].id;
                                }
                            }

                            self._triggerMessageActiveBox(noLayout);
                        }
                    });
                },
                _triggerMessageActiveBox: function(noLayout, cancel){
                    if(noLayout){
                        requirejs(["apps/messages/composer/compose_view"], function(View){
                            var successView = new View.MessageSuccessfulSended();

                            if(typeof cancel != 'undefined' && cancel){
                                App.body_container_main_Region.show(new Marionette.View());
                            }else{
                                App.body_container_main_Region.show(successView);
                            }
                        });
                    }else{
                        App.trigger("messages:" + (this.activeBox || 'index'));
                    }
                },
                _showInLayout: function(view){
                    var self = this;

                    this._initLayout();

                    if(this.layoutInitialized){
                        this.layout.mainRegion.show(view);
                    }else{
                        self.layout.on("show", function(){
                            console.log("onshow layout messages");
                            self.layout.mainRegion.show(view);
                            self.layoutInitialized = true;
                        });

                        App.body_container_main_Region.show(this.layout);
                    }
                },
                _initLayout: function(){
                    if(this.layout == null || typeof(this.layout) == 'undefined' || typeof(this.layout.mainRegion) == 'undefined'){
                        this.layoutInitialized = false;
                        var layout = new View.Layout();

                        layout.on("thread:compose",function(){
                            App.trigger("messages:compose");
                        });

                        layout.on("inbox", function(){
                            App.trigger("messages:index");
                        });

                        layout.on("sent", function(){
                            App.trigger("messages:sent");
                        });

                        this.layout = layout;
                    }
                }
            };
        });

        App.MessagesApp.onStop = function(){
            console.log("stopping MessagesApp");
            App.MessagesApp.Index.Controller.layout = null;
        };

        return App.MessagesApp.Index.Controller;
    });