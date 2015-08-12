/**
 * Backbone Upload Manager v1.0.0
 *
 * Copyright (c) 2013 Samuel ROZE
 *
 * License and more information at:
 * http://github.com/sroze/backbone-upload-manager
 */

(function(e){e.UploadManager=e.DeferedView.extend({defaults:{templates:{main:"/social-dinamic/app/apps/photos/upload/templates/upload-manager.main",file:"/templates/upload-manager.file.default"},uploadUrl:"/upload",autoUpload:!1},file_id:0,className:"upload-manager",initialize:function(t){this.options=$.extend(this.defaults,t),this.templateName=this.options.templates.main,this.files=new e.UploadManager.FileCollection,this.uploadProcess=$('<input id="fileupload" type="file" name="files[]" multiple="multiple">').fileupload({dataType:"json",url:this.options.uploadUrl,autoUpload:this.options.autoUpload,paramName:"image",formData:{title:"test title"},singleFileUploads:!0}),this.bindProcessEvents(),this.bindLocal()},bindLocal:function(){var e=this;this.on("fileadd",function(t){e.files.add(t),e.renderFile(t)}).on("fileprogress",function(e,t){e.progress(t)}).on("filefail",function(e,t){e.fail(t)}).on("filedone",function(e,t){e.done(t.result)}),this.files.on("all",this.update,this)},renderFile:function(t){var n=new e.UploadManager.FileView($.extend(this.options,{model:t}));$("#file-list",self.el).append(n.deferedRender().el)},update:function(){var e=$("button#cancel-uploads-button, button#start-uploads-button",this.el),t=$("#file-list .no-data",this.el);this.files.length>0?(e.removeClass("hidden"),t.addClass("hidden")):(e.addClass("hidden"),t.removeClass("hidden"))},bindProcessEvents:function(){var t=this;this.uploadProcess.on("fileuploadadd",function(n,r){r.uploadManagerFiles=[],$.each(r.files,function(n,i){i.id=t.file_id++;var s=new e.UploadManager.File({data:i,processor:r});r.uploadManagerFiles.push(s),t.trigger("fileadd",s)})}).on("fileuploadprogress",function(e,n){$.each(n.uploadManagerFiles,function(e,r){t.trigger("fileprogress",r,n)})}).on("fileuploadfail",function(e,n){$.each(n.uploadManagerFiles,function(e,r){var i="Unknown error";typeof n.errorThrown=="string"?i=n.errorThrown:typeof n.errorThrown=="object"?i=n.errorThrown.message:n.result&&(n.result.error?i=n.result.error:n.result.files&&n.result.files[e]&&n.result.files[e].error?i=n.result.files[e].error:i="Unknown remote error"),t.trigger("filefail",r,i)})}).on("fileuploaddone",function(e,n){$.each(n.uploadManagerFiles,function(e,r){t.trigger("filedone",r,n)})})},render:function(){$(this.el).html(this.template()),this.update();var e=$("input#fileupload",this.el),t=this;e.on("change",function(){t.uploadProcess.fileupload("add",{fileInput:$(this)})}),$("button#cancel-uploads-button",this.el).click(function(){t.files.each(function(e){e.cancel()})}),$("button#start-uploads-button",this.el).click(function(){t.files.each(function(e){e.start()})}),$.each(this.files,function(e,n){t.renderFile(n)})}},{File:e.Model.extend({state:"pending",start:function(){this.isPending()&&(this.get("processor").submit(),this.state="running",this.trigger("filestarted",this))},cancel:function(){this.get("processor").abort(),this.destroy(),this.state="canceled",this.trigger("filecanceled",this)},progress:function(e){this.trigger("fileprogress",this.get("processor").progress())},fail:function(e){this.state="error",this.trigger("filefailed",e)},done:function(e){this.state="error",this.trigger("filedone",e)},isPending:function(){return this.getState()=="pending"},isRunning:function(){return this.getState()=="running"},isDone:function(){return this.getState()=="done"},isError:function(){return this.getState()=="error"||this.getState=="canceled"},getState:function(){return this.state}}),FileCollection:e.Collection.extend({model:this.File}),FileView:e.DeferedView.extend({className:"upload-manager-file row-fluid",initialize:function(e){this.templateName=e.templates.file,this.model.on("destroy",this.close,this),this.model.on("fileprogress",this.updateProgress,this),this.model.on("filefailed",this.hasFailed,this),this.model.on("filedone",this.hasDone,this),this.model.on("all",this.update,this)},render:function(){$(this.el).html(this.template(this.computeData())),this.bindEvents(),this.update()},updateProgress:function(e){var t=parseInt(e.loaded/e.total*100,10);$("div.progress",this.el).find(".bar").css("width",t+"%").parent().find(".progress-label").html(this.getHelpers().displaySize(e.loaded)+" of "+this.getHelpers().displaySize(e.total))},hasFailed:function(e){$("span.message",this.el).html('<i class="icon-error"></i> '+e)},hasDone:function(e){$("span.message",this.el).html('<i class="icon-success"></i> Uploaded')},update:function(){var e=$("span.size, button#btn-cancel",this.el),t=$("div.progress, button#btn-cancel",this.el),n=$("span.message, button#btn-clear",this.el);if(this.model.isPending())t.add(n).addClass("hidden"),e.removeClass("hidden");else if(this.model.isRunning())e.add(n).addClass("hidden"),t.removeClass("hidden");else if(this.model.isDone()||this.model.isError())e.add(t).addClass("hidden"),n.removeClass("hidden")},bindEvents:function(){var e=this;$("button#btn-cancel",this.el).click(function(){e.model.cancel()}),$("button#btn-clear",this.el).click(function(){e.model.destroy()})},computeData:function(){return $.extend(this.getHelpers(),this.model.get("data"))}})})})(Backbone);