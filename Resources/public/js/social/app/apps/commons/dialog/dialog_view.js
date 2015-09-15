define([
    "app",
    "text!apps/commons/dialog/templates/dialog_template.html",
    "backbone-bootstrap-modal"
    ],
function(App, DialogTemplate) {

    App.module("Commons.Views", function(Views, App, Backbone, Marionette, $, _){
        var ModalBodyView = Backbone.View.extend({
            tagName: 'p',
            render: function() {
                this.$el.html(this.template);
                return this;
            }
        });

        var Modal = Backbone.BootstrapModal.extend({
            animate: true
        });

        Views.confirm = function(message, title){
            title = title || "Confirm";
            var ModalView = ModalBodyView.extend({template: message});
            var bodyView = new ModalView();
            bodyView.render();
            
            var modal = new Modal({content: bodyView, title: title, template: _.template(DialogTemplate)});

            var result;
            var deferred = $.Deferred();

            modal.on('ok', function(){
                deferred.resolve(true);
            });

            modal.on('cancel', function(){
                deferred.resolve(false);
            });

            modal.open();

            return deferred.promise();
        };
    });
    return App.Commons.Views;
});