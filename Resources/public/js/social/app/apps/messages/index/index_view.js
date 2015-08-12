define([
    "app",
    "text!apps/messages/index/templates/layout.html",
    "text!apps/messages/index/templates/index.html",
    "text!apps/messages/index/templates/item.html",
    "text!apps/messages/index/templates/messages.html",
    "text!apps/messages/index/templates/message.html",
    "text!apps/messages/index/templates/no_messages.html",
    "handlebars"
    ],
    function(App, Layout, IndexTemplate, ItemTemplate, MessagesTemplate, MessageTemplate, NoMessagesTemplate, Handlebars){
        App.module("MessagesApp.Index.View", function(View, App, Backbone, Marionette, $, _){
            View.Layout = Marionette.Layout.extend({
                template: Layout,
                events: {
                    "click #compose": "composeLinkClicked",
                    "click #inbox-tab": "inboxClicked",
                    "click #sent-tab": "sentClicked"
                },
                regions: {
                  mainRegion: "#main"
                },
                composeLinkClicked: function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    this.trigger("thread:compose");
                    return false;
                },
                inboxClicked: function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    this.trigger('inbox');
                    return false;
                },
                sentClicked: function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    this.trigger('sent');
                    return false;
                },
                setActiveTab: function(tab){
                    var self = this;

                    var doSetActiveTab = function(){
                        self.$el.find('#inbox-tabs li').removeClass('active');
                        self.$el.find('#'+tab).addClass('active');
                    };

                    this.on("item:rendered", function(){
                        doSetActiveTab();
                        self.hasBeenRendered = true;
                    });

                    if(this.hasBeenRendered){
                        doSetActiveTab();
                    }
                }
            });

            //Crea las regiones donde vamos a meter los canales paginados
            View.Thread = Marionette.ItemView.extend({
                tagName: "div",
                template: Handlebars.compile(ItemTemplate),
                triggers: {
                    "click .message-item": "thread:show"
                }
            });

            View.Message = Marionette.ItemView.extend({
                tagName: "div",
                template: Handlebars.compile(MessageTemplate)
                /*events: {
                    'click .message-header-wrapper': "expandBody"
                },
                expandBody: function(){
                    it(!this.body.isShowen){
                    this.$el.find('.message-body-wrapper').show();
                    this.$el.find('.message-more').hide();
                    }
                }*/
            });

            View.NoMessages = Marionette.ItemView.extend({
                template: Handlebars.compile(NoMessagesTemplate)
            });

            View.Threads = Marionette.CompositeView.extend({
                tagName: 'div',
                template: IndexTemplate,
                itemView: View.Thread,
                emptyView: View.NoMessages
            });

            View.Messages = Marionette.CompositeView.extend({
                template: Handlebars.compile(MessagesTemplate),
                itemView: View.Message,
                itemViewContainer: "#messages",

                events: {
                    "click #reply": "replyClicked",
                    "click #delete": "deleteClicked",
                    "click .btn-back-message-list": "back"
                },

                appendHtml: function(collectionView, itemView, index){
                    var childrenContainer = this.$el.find('#messages');
                    childrenContainer.prepend(itemView.el);
                },
                replyClicked: function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    this.trigger("thread:reply", this.collection.models[0].get('thread'));
                },
                deleteClicked: function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    var self = this;

                    require(["apps/commons/dialog/dialog_view"],function(DialogView){
                        DialogView
                            .confirm("Are you sure you want to delete this thread?", "Delete thread")
                            .then(function(confirmed){console.log('me han borrado');
                                if(confirmed){
                                    self.trigger("thread:delete", self.collection.models[0].get('thread'));
                                }
                            });
                    });
                },
                /*onCompositeRendered: function(){
                    var $messagesBodies = this.$el.find('.message-body-wrapper');
                    $messagesBodies.hide();
                    $messagesBodies.first().show();
                    $messagesBodies.first()
                        .parent()
                        .find('.message-more').hide();
                },*/
                showReply: function(view){
                    this.$el.find('#replay-container').html(view.render().el);
                },
                back: function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    this.trigger('thread:back');
                    return false;
                }
            });
        });

        return App.MessagesApp.Index.View;
    });