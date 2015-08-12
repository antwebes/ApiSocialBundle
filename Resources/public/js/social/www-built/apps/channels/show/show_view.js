define(["app","text!apps/channels/show/templates/channel_details_template.html","text!apps/commons/chat/templates/enter_chat_template.html","text!apps/channels/show/templates/layout_template.html","apps/channels/form/formView","text!apps/channels/show/templates/channel_tabs.html","text!apps/channels/show/templates/layout_main_template.html","text!apps/dashboard/show/templates/list_photos.html","util/lazyloaderhelper","util/util"],function(e,t,n,r,i,s,o,u,a){return e.module("ChannelsApp.Show.View",function(e,n,i,u,f,l){e.Layout=u.Layout.extend({template:r,regions:{body_container_top_Region:"#body_container_top",channels_show_left_Region:"#channels_show_container_left",channels_show_container_center_Region:"#channels_show_container_center"}}),e.Tabs=u.ItemView.extend({template:s,triggers:{"click #moderators":"channel:show:moderators","click #fans":"channel:show:fans","click #edit":"channel:show:edit","click #show_channel":"channel:show"}}),e.LayoutMain=u.Layout.extend({template:o,regions:{main_container_top_Region:"#main_container_top",main_container_bottom_Region:"#main_container_bottom",main_container_widget_chat_Region:"#main_container_widget_chat",main_container_social_network:"#main_container_social_network",main_container_breadcrumbs_Region:".breadcrumbs_channel"}}),e.ChannelDetailsView=u.ItemView.extend({template:Handlebars.compile(t),initialize:function(){this.model.on("change",this.render,this),a(this),Handlebars.registerHelper("ifCond",function(e,t,n){return e===t?n.fn(this):n.inverse(this)})},onClose:function(){this.model.unbind("change",this.render)}})}),e.ChannelsApp.Show.View});