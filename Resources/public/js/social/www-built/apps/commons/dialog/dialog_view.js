define(["app","text!apps/commons/dialog/templates/dialog_template.html","backbone-bootstrap-modal"],function(e,t){return e.module("Commons.Views",function(e,n,r,i,s,o){var u=r.View.extend({tagName:"p",render:function(){return this.$el.html(this.template),this}}),a=r.BootstrapModal.extend({animate:!0});e.confirm=function(e,n){n=n||"Confirm";var r=u.extend({template:e}),i=new r;i.render();var f=new a({content:i,title:n,template:o.template(t)}),l,c=s.Deferred();return f.on("ok",function(){c.resolve(!0)}),f.on("cancel",function(){c.resolve(!1)}),f.open(),c.promise()}}),e.Commons.Views});