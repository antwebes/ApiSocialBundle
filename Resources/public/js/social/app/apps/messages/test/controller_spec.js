define([
    "apps/messages/index/index_controller",
    "app",
    "apps/messages/test/fixtures",
    "apps/messages/messages_app",
    "util/testutils",
    "jasmine-fixture"],
    function(Controller, App, Fixtures){
        describe("MessagesApp.Controller", function(){
            var makeResolvedPromiseOfData = function(fakeData){
                var defer = $.Deferred();
                var promise = defer.promise();
                defer.resolve(fakeData);

                return promise;
            };

            beforeEach(function(){
                Controller._sendMessage = sinon.spy();

                var promise = makeResolvedPromiseOfData(Fixtures.threads.collection);
                var promiseMessages = makeResolvedPromiseOfData(Fixtures.messages.collection);
                this.parentThread = Fixtures.messages.message1.get('thread');

                this.stub = sinon.stub(App, "request");

                this.stub.withArgs("thread:entities").returns(promise);
                this.stub.withArgs("thread:entities:sent").returns(promise);
                this.stub.withArgs("thread:messages", 1).returns(promiseMessages);

                App.navigate('social-dinamic/app/SpecRunner.html');
            });

            afterEach(function(){
                this.stub.restore();

                App.navigate('social-dinamic/app/SpecRunner.html');
            });

            describe("Index", function(){
                it("should show the inbox", function(){
                    Controller.Index();
                    expect($('.message-item').length).toEqual(3);
                    expect($('.message-unread').length).toEqual(1);
                    expect($('.message-unread .summary span').html()).toEqual("el titulo del mensaje 1");
                });
            });

            describe("Sent", function(){
                it("should show the sended box", function(){
                    Controller.Sent();
                    expect($('.message-item').length).toEqual(3);
                    expect($('.message-unread').length).toEqual(1);
                    expect($('.message-unread .summary span').html()).toEqual("el titulo del mensaje 1");
                });
            });

            describe("ShowThread", function(){
                beforeEach(function(){
                    this._orgiginalDeleteThread = Controller._deleteThread;
                    Controller._deleteThread = sinon.spy();
                    
                    Controller.ShowThread(1);
                });

                afterEach(function(){
                    Controller._deleteThread = this._orgiginalDeleteThread;
                });

                it("should request the messegas for the thread from the server", function(){
                    expect(this.stub).toHaveBeenCalledWith("thread:messages", 1);
                });

                it("should show the messages of a thread", function(){
                    expect($('.message-item').length).toEqual(2);
                    expect($('.message-unread').length).toEqual(1);
                    expect($('.message-unread .message-body').html()).toEqual('el body 2');
                });

                it("should show a reply button", function(){
                    expect($('#reply').length).toEqual(1);
                });

                it("should show a delete button", function(){
                    expect($('#delete').length).toEqual(1);
                });

                describe("reply button", function(){
                    var $replyButton;

                    it("should show a reply form", function(){
                        $replyButton = $('#reply');
                        $replyButton.trigger('click');

                        waitsForPresenceOfDOMElement('span.btn-send-message');
                    
                        runs(function(){
                            expect($('span.btn-send-message').length).toEqual(1);
                            expect($('#replay-container textarea').length).toEqual(1);
                        });
                    });
                });

                describe("delete button", function(){
                    it("should call the delete controller", function(){
                        $("#delete").trigger('click');

                        waitsForPresenceOfDOMElement('.modal-dialog');

                        runs(function(){
                            $(".ok").trigger('click');
                            expect(Controller._deleteThread).toHaveBeenCalledWith(this.parentThread);
                        });
                    });
                });

                describe("Write Mail button", function(){
                    var $writeMail;

                    beforeEach(function(){
                    });

                    it("should show the write mail form", function(){
                        $writeMail = $('#compose');
                        $writeMail.trigger('click');
                        waitsForPresenceOfDOMElement('input[name=recipient]');
                        runs(function(){
                            expect($('input[name=recipient]').length).toEqual(1);
                            expect($('input[name=subject]').length).toEqual(1);
                            expect($('textarea[name=body]').length).toEqual(1);
                            expect($('button#send').length).toEqual(1);
                        });
                    });
                });

                describe("Compose", function(){
                    it("should call the _sendMessage action when the form is filled in and the send button is pressed", function(){
                        Controller.Compose();
                        
                        var $recipient;
                        var $subject;
                        var $body;
                        var $sendButton;

                        waitsForPresenceOfDOMElement('input[name=recipient]');

                        runs(function(){
                            $recipient = $('input[name=recipient]');
                            $subject = $('input[name=subject]');
                            $body = $('textarea[name=body]');
                            $sendButton = $('button#send');

                            $recipient.val('therecipient');
                            $subject.val('thesubject');
                            $body.val('thebody');
                            $sendButton.trigger('click');
                        });

                        waitsForSpyToBeCalled(Controller._sendMessage);

                        runs(function(){
                            console.log('cargado');
                            expect(Controller._sendMessage).toHaveBeenCalled();
                        });
                    });
                });
            });
    });

    return {
        name: "messages_controller_spec"
    };
});