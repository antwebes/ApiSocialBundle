define(["app","text!apps/users/edit/templates/users_change_password_email_template_layout.html"],function(e,t,n,r){return e.module("UsersApp.Edit.View",function(e,n,r,i,s,o){e.LayoutChangePasswordEmail=i.Layout.extend({template:Handlebars.compile(t),regions:{change_password_Region:"#container_change_password",change_email_Region:"#container_change_email"}})}),e.UsersApp.Edit.View});