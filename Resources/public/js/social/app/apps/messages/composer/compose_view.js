define([
    "app",
    "text!apps/messages/composer/templates/compose.html",
    "text!apps/messages/composer/templates/compose_form.html",
    "text!apps/messages/composer/templates/message_successfull_sended.html",
    "backbone-bootstrap",
    "backbone-forms",
    "backbone.autocomplete",
    "entities/messages",
    "handlebars"
    ],
    function(App, ComposeTemplate, FormTemplate, MessageSuccessfullSendedTemplate, BackboneBootstrap, BackboneForm, BackboneAutocomplete, Messages, Handlebars){
        function loadCss(url) {
            var link = document.createElement("link");
            link.type = "text/css";
            link.rel = "stylesheet";
            link.href = url;
            document.getElementsByTagName("head")[0].appendChild(link);
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
                },
                onRender: function () {
                    loadCss(require.toUrl('backbone_autocomplete_css'));
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
                    var errors = form.commit({ validate: true });

                    if(typeof errors === "undefined"){
                        showAlert(this.$el.find('#alert-success'), this.$el.find('#alert-danger'), 'Sending messsage...');
                        this.trigger("compose:submit", this.model);
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

                    if (xhr.responseJSON !== undefined){
                        response = xhr.responseJSON;
                    } else {
                        response = JSON.parse(xhr.responseText);
                    }

                    //poner una condicion de que sea obligatorio que existe xhr.responseJSON.errors, ahora si no existe peta
                    if (response.code === 32 || response.code === 40) {
                        error = xhr.responseJSON.errors;
                    } else {
                        $.each(response.errors,function(index, value){
                            error += index + ' : ' + value.message + '\n';
                        });
                    }

                    showAlert(this.$el.find('#alert-danger'), this.$el.find('#alert-success'), error);
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