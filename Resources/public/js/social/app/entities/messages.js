define([
    "app"
    ],function(App){
        App.module("Entities", function(Entities, App, Backbone, Marionette, $, _){
            Entities.Message = Backbone.Model.extend({
                urlRoot: window.api_endpoint + '/api/users/'+window.user_id+'/threads',
                initialize: function(options){
                    if(options.thread){
                        this.urlRoot = this.urlRoot + '/' + options.thread.id;
                        //this.unset("thread");
                    }
                },
                schema: {
                    body:      { validators: ['required'], type: 'TextArea', editorClass: "wysiwyg-editor", editorAttrs: { "cols": "120", "rows": "10", "placeholder": "Message" } }
                },
                toJSON: function(){
                    var data = Backbone.Model.prototype.toJSON.apply(this, arguments);
                    return { message: data };
                }
            });

            Entities.MessageCollection = Backbone.Collection.extend({
                url: window.api_endpoint + '/api/users/'+window.user_id+'/threads/',
                model: Entities.Message,

                initialize: function(data){
                    if (typeof(data.thread_id) != "undefined"){
                        this.url = this.url + data.thread_id;
                    }
                },

                parse: function(response){
                    return response;
                }
            });

            Entities.Recipient = Backbone.Model.extend({
                label: function () {
                    return this.get("username");
                }
            });

            Entities.RecipientsCollection = Backbone.Collection.extend({
                model: Entities.Recipient,
                url: function(){
                    return window.api_endpoint + '/api/users/';
                },
                sync: function(method, model, options){
                    var name = options.data.query;
                    
                    delete options.data['query'];
                    options.url = this.url() + name + '/search';

                    Backbone.Collection.prototype.sync.call(this, method, model, options);
                }
            });

            var API = {
                getMessagesOfThread: function(thread_id){
                    var messages = new Entities.MessageCollection({thread_id: thread_id});
                    var defer = $.Deferred();

                    messages.fetch({
                      success: function(data){
                        defer.resolve(data);
                      },
                      error: function(data, err, statusCode){
                        defer.reject(err);
                      }
                    });
                    
                    var promise = defer.promise();
                    
                    return promise;
                }
            };

            App.reqres.setHandler("thread:messages", function(thread_id){
              return API.getMessagesOfThread(thread_id);
            });
        });
        return App.Entities;
    });