define([
    "entities/messages",
    "entities/threads"],
    function(Messages, Threads){
        var thread1 = new Backbone.Model({
                id: 1,
                subject: 'el titulo del mensaje 1',
                is_readed: false,
                created_at: '2013-08-26T15:01:01+0000',
                created_by: {
                    username: 'alex',
                    profile: {
                        profile_photo: {
                            path_icon: 'foto_icon.jpg'
                        }
                    }
                }
            });
        var thread2 = new Backbone.Model({
                id: 2,
                subject: 'el titulo del mensaje2',
                is_readed: true,
                created_at: '2013-08-28T15:01:01+0000',
                created_by: {
                    username: 'alex',
                    profile: {
                        profile_photo: {
                            path_icon: 'foto_icon.jpg'
                        }
                    }
                }
            });
        var threadsCollection = new Backbone.Collection([thread1, thread2]);

        var parentThread = { id: 1 };
        var message1 = new Messages.Message({
                id: 1,
                body: 'el body 1',
                is_readed: true,
                created_at: '2013-08-26T15:01:01+0000',
                thread: parentThread,
                sender: {
                    username: 'alex',
                    profile: {
                        profile_photo: {
                            path_icon: 'foto_icon.jpg'
                        }
                    }
                }
            });
        var message2 = new Messages.Message({
                id: 2,
                body: 'el body 2',
                is_readed: false,
                created_at: '2013-08-26T15:01:01+0000',
                thread: parentThread,
                sender: {
                    username: 'alex2',
                    profile: {
                        profile_photo: {
                            path_icon: 'foto_icon.jpg'
                        }
                    }
                }
            });
        var messagesCollection = new Backbone.Collection([message1, message2]);

        return {
            threads: {
                thread1: thread1,
                thread2: thread2,
                collection: threadsCollection
            },
            messages: {
                message1: message1,
                message2: message2,
                collection: messagesCollection
            }
        };
    });