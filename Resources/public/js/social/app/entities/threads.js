define([
    "app"
    ],function(App){
        App.module("Entities", function(Entities, App, Backbone, Marionette, $, _){
            Entities.Thread = Backbone.Model.extend({
                urlRoot: window.api_endpoint + '/api/users/'+window.user_id+'/threads/inbox',
                schema: {
                    subject: { validators: ['required'] }
                }
            });

            Entities.ThreadCollection = Backbone.Collection.extend({
                url: window.api_endpoint + '/api/users/'+window.user_id+'/threads/',
                model: Entities.Thread,
                initialize: function(options){
                    this.url = this.url + options.box;
                }
            });

            Entities.NewThread = Backbone.Model.extend({
                urlRoot: window.api_endpoint + '/api/users/'+window.user_id+'/threads',
                schema: {
                    recipient: {
                        validators: [ { type: 'required', message: App.request('trans', "messages::required") } ],
                        editorAttrs: {
                            id: 'form-field-recipient',
                            type: "email",
                            placeholder: App.request('trans', "messages::recipient")
                        }
                    },
                    subject: {
                        validators: [ { type: 'required', message: App.request('trans', "messages::required") } ],
                        editorAttrs: {
                            placeholder: App.request('trans', "messages::subject")
                        }
                    },
                    body: {
                        validators: [ { type: 'required', message: App.request('trans', "messages::required") } ],
                        type: 'TextArea',
                        editorClass: "wysiwyg-editor",
                        editorAttrs: {
                            cols: "100",
                            rows: "10",
                            placeholder: App.request('trans', "messages::message")
                        }
                    }
                },
                toJSON: function(){
                    var data = Backbone.Model.prototype.toJSON.apply(this, arguments);
                    return { message: data };
                }
            });

            var fetchThreads = function(box){
                var threads = new Entities.ThreadCollection({box: box});
                var defer = $.Deferred();

                threads.fetch({
                  success: function(data){
                    defer.resolve(data);
                  }
                });
                    
                var promise = defer.promise();
                    
                return promise;
            };

            var API = {
                getThreads: function(){
                    return fetchThreads('inbox');
                },
                getUnreadedThreads: function(){
                    var fetchInbox = fetchThreads('inbox');
                    var defer = $.Deferred();

                    $.when(fetchInbox).done(function(inbox){
                        var unreaded = inbox.filter(function(thread){
                                return !thread.get('is_readed');
                            });
                        
                        defer.resolve(unreaded);
                    });

                    var promise = defer.promise();
                    return promise;
                },
                getSentThreads: function(){
                    return fetchThreads('sent');
                },
                deleteThread: function(thread){
                    var defer = $.Deferred();
                    var threadToDelete = new Entities.Thread();
                    threadToDelete.set('id', thread.id);
                    threadToDelete.urlRoot = window.api_endpoint + '/api/users/'+window.user_id+'/threads/';

                    threadToDelete.destroy({
                      success: function(){
                        defer.resolve();
                      }
                    });
                        
                    var promise = defer.promise();
                        
                    return promise;
                    
                }
            };

            App.reqres.setHandler("thread:entities", function(){
              return API.getThreads();
            });

            App.reqres.setHandler("thread:entities:sent", function(){
              return API.getSentThreads();
            });

            App.reqres.setHandler("thread:entities:unreaded", function(){
              return API.getUnreadedThreads();
            });

            App.reqres.setHandler("thread:delete", function(thread){
              return API.deleteThread(thread);
            });
        });
        return App.Entities;
    });