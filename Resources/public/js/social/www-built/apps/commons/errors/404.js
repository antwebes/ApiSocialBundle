define(["app","text!apps/commons/errors/templates/404.html","handlebars"],function(e,t,n){return e.module("Commons.Errors.TemplateNotFound",function(e,r,i,s,o,u){e.NotFound=s.ItemView.extend({template:n.compile(t),events:{"click #goChat":"goChat"},goChat:function(){r.trigger("chat:show")}})}),e.Commons.Errors.TemplateNotFound});