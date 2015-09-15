define([
    "app",
    "text!apps/messages/composer/templates/compose.html",
    "text!apps/messages/composer/templates/compose_form.html",
    "text!apps/messages/composer/templates/message_successfull_sended.html",
    "backbone-bootstrap",
    "backbone-forms",
    "entities/messages",
    "handlebars",
    "text!backbone_autocomplete_css.css",
    "backbone.autocomplete"
    ],
    function(App, ComposeTemplate, FormTemplate, MessageSuccessfullSendedTemplate, BackboneBootstrap, BackboneForm, Messages, Handlebars, AutocompleteCss){
        var cssTags = {};

        function loadCss(tag, css) {
            if(typeof cssTags[tag] != 'undefined'){
                return;
            }

            var head = document.head || document.getElementsByTagName('head')[0],
                style = document.createElement('style');

            style.type = 'text/css';
            if (style.styleSheet){
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }

            head.appendChild(style);

            cssTags[tag] = true;
        }

        App.module("MessagesApp.Composer.View", function(View, App, Backbone, Marionette, $, _){
            var form = null;

            View.ComposeView = Marionette.ItemView.extend({
                tagName: "div",
                template: ComposeTemplate,
                formTemplate: FormTemplate,
                events: {
                    "click #send": "submit",
                    "click #cancel-send": "back"
                },
                initialize: function (options){
                    this.template = Handlebars.compile(this.template);
                    this.model.on('error', this.onError, this);

                    if(options.formTemplate){
                        this.formTemplate = options.formTemplate;
                    }

                    this.sending = false;
                },
                onRender: function () {
                    loadCss("autocompleteCss", AutocompleteCss);
                    form = new BackboneForm({
                            model: this.model,
                            template: Handlebars.compile(this.formTemplate)
                        });

                    this.$el.find('.compose-wrapper').html(form.render().el);

                    var recipeintsCollection = new Messages.RecipientsCollection();

                    setTimeout(function(){
                        new AutoCompleteView({
                            input: $('#form-field-recipient'),
                            model: recipeintsCollection
                        }).render();
                    }, 100);

                    return this;
                },
                submit: function(){
                    if(this.sending){
                        return;
                    }

                    for(field in form.fields){
                        $field = this.$el.find('*[data-editors="'+field+'"]');
                        $field.removeClass('has-error');
                        $field.find('*[data-error="'+field+'"]').remove();
                    }

                    var errors = form.commit({ validate: true });


                    if(typeof errors === "undefined"){
                        this.sending = true;

                        showAlert(this.$el.find('#alert-success'), this.$el.find('#alert-danger'), this.trans('messages::sending_message')+'...');
                        this.trigger("compose:submit", this.model);
                    }else{
                        for(field in errors){
                            $field = this.$el.find('*[data-editors="'+field+'"]');
                            $field.addClass('has-error');
                            $field.append('<p class="help-block" data-error="' + field + '">' + errors[field].message + '</p>');
                        }
                    }
                },
                back: function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    this.trigger('compose:back');
                    return false;
                },
                onError : function(model, xhr, options) {
                    var self = this;
                    var error = '';

                    self.sending = false;

                    if (xhr.responseJSON !== undefined){
                        response = xhr.responseJSON;
                    } else {
                        response = JSON.parse(xhr.responseText);
                    }

                    //poner una condicion de que sea obligatorio que existe xhr.responseJSON.errors, ahora si no existe peta
                    if (response.code === 32 || response.code === 40) {
                        error = self.trans(xhr.responseJSON.errors);
                    } else {
                        $.each(response.errors,function(index, value){
                            error += self.trans(index) + ' : ' + self.trans(value.message) + '<br>';
                        });
                    }

                    showAlert(this.$el.find('#alert-danger'), this.$el.find('#alert-success'), error);
                },
                trans: function(msg){
                    var map = {
                        'body': 'messages::message',
                        'recipient': 'messages::recipient',
                        'You cannot send a message to yourself': 'messages::cannot_send_to_youself',
                        'The body is too short': 'messages::message_too_short',
                        'subject': 'messages::subject',
                        'The subject is too short': 'messages::subject_too_short',
                        'You can\'t post more messages, limit exceced': 'messages::limit_exceded',
                        'No recipient specified': 'messages::no_recipient_specified'
                    };

                    if(typeof map[msg] != 'undefined'){
                        msg = map[msg];
                    }

                    return App.request("trans", msg);
                }
            });

            View.MessageSuccessfulSended = Marionette.ItemView.extend({
                template: Handlebars.compile(MessageSuccessfullSendedTemplate)
            });
        });

        function showAlert(region_show_el, region_hide_el, data) {
            $(region_hide_el).hide();
            $(region_show_el).html('');
            $(region_show_el).removeClass('hide');
            $(region_show_el).show();
            $(region_show_el).html(data);
        }

        return App.MessagesApp.Composer.View;
    });