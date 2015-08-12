/**
 * Backbone Forms v0.13.0
 *
 * NOTE:
 * This version is for use with RequireJS
 * If using regular <script> tags to include your files, use backbone-forms.min.js
 *
 * Copyright (c) 2013 Charles Davison, Pow Media Ltd
 * 
 * License and more information at:
 * http://github.com/powmedia/backbone-forms
 */

define(["jquery","underscore","backbone"],function(e,t,n){var r=n.View.extend({initialize:function(e){var n=this;e=e||{};var r=this.schema=function(){if(e.schema)return t.result(e,"schema");var r=e.model;return r&&r.schema?t.isFunction(r.schema)?r.schema():r.schema:n.schema?t.isFunction(n.schema)?n.schema():n.schema:{}}();t.extend(this,t.pick(e,"model","data","idPrefix","templateData"));var i=this.constructor;this.template=e.template||this.template||i.template,this.Fieldset=e.Fieldset||this.Fieldset||i.Fieldset,this.Field=e.Field||this.Field||i.Field,this.NestedField=e.NestedField||this.NestedField||i.NestedField;var s=this.selectedFields=e.fields||t.keys(r),o=this.fields={};t.each(s,function(e){var t=r[e];o[e]=this.createField(e,t)},this);var u=e.fieldsets||[s],a=this.fieldsets=[];t.each(u,function(e){this.fieldsets.push(this.createFieldset(e))},this)},createFieldset:function(e){var t={schema:e,fields:this.fields};return new this.Fieldset(t)},createField:function(e,t){var n={form:this,key:e,schema:t,idPrefix:this.idPrefix};this.model?n.model=this.model:this.data?n.value=this.data[e]:n.value=null;var r=new this.Field(n);return this.listenTo(r.editor,"all",this.handleEditorEvent),r},handleEditorEvent:function(e,n){var r=n.key+":"+e;this.trigger.call(this,r,this,n,Array.prototype.slice.call(arguments,2));switch(e){case"change":this.trigger("change",this);break;case"focus":this.hasFocus||this.trigger("focus",this);break;case"blur":if(this.hasFocus){var i=this;setTimeout(function(){var e=t.find(i.fields,function(e){return e.editor.hasFocus});e||i.trigger("blur",i)},0)}}},render:function(){var n=this,r=this.fields,i=e(e.trim(this.template(t.result(this,"templateData"))));return i.find("[data-editors]").add(i).each(function(i,s){var o=e(s),u=o.attr("data-editors");if(t.isUndefined(u))return;var a=u=="*"?n.selectedFields||t.keys(r):u.split(",");t.each(a,function(e){var t=r[e];o.append(t.editor.render().el)})}),i.find("[data-fields]").add(i).each(function(i,s){var o=e(s),u=o.attr("data-fields");if(t.isUndefined(u))return;var a=u=="*"?n.selectedFields||t.keys(r):u.split(",");t.each(a,function(e){var t=r[e];o.append(t.render().el)})}),i.find("[data-fieldsets]").add(i).each(function(r,i){var s=e(i),o=s.attr("data-fieldsets");if(t.isUndefined(o))return;t.each(n.fieldsets,function(e){s.append(e.render().el)})}),this.setElement(i),i.addClass(this.className),this},validate:function(e){var n=this,r=this.fields,i=this.model,s={};e=e||{},t.each(r,function(e){var t=e.validate();t&&(s[e.key]=t)});if(!e.skipModelValidate&&i&&i.validate){var o=i.validate(this.getValue());if(o){var u=t.isObject(o)&&!t.isArray(o);u||(s._others=s._others||[],s._others.push(o)),u&&t.each(o,function(e,t){if(r[t]&&!s[t])r[t].setError(e),s[t]=e;else{s._others=s._others||[];var n={};n[t]=e,s._others.push(n)}})}}return t.isEmpty(s)?null:s},commit:function(e){e=e||{};var n={skipModelValidate:!e.validate},r=this.validate(n);if(r)return r;var i,s=t.extend({error:function(e,t){i=t}},e);this.model.set(this.getValue(),s);if(i)return i},getValue:function(e){if(e)return this.fields[e].getValue();var n={};return t.each(this.fields,function(e){n[e.key]=e.getValue()}),n},setValue:function(e,t){var n={};typeof e=="string"?n[e]=t:n=e;var r;for(r in this.schema)n[r]!==undefined&&this.fields[r].setValue(n[r])},getEditor:function(e){var t=this.fields[e];if(!t)throw new Error("Field not found: "+e);return t.editor},focus:function(){if(this.hasFocus)return;var e=this.fieldsets[0],t=e.getFieldAt(0);if(!t)return;t.editor.focus()},blur:function(){if(!this.hasFocus)return;var e=t.find(this.fields,function(e){return e.editor.hasFocus});e&&e.editor.blur()},trigger:function(e){return e==="focus"?this.hasFocus=!0:e==="blur"&&(this.hasFocus=!1),n.View.prototype.trigger.apply(this,arguments)},remove:function(){return t.each(this.fieldsets,function(e){e.remove()}),t.each(this.fields,function(e){e.remove()}),n.View.prototype.remove.apply(this,arguments)}},{template:t.template("    <form data-fieldsets></form>  ",null,this.templateSettings),templateSettings:{evaluate:/<%([\s\S]+?)%>/g,interpolate:/<%=([\s\S]+?)%>/g,escape:/<%-([\s\S]+?)%>/g},editors:{}});return r.validators=function(){var e={};return e.errMessages={required:"Required",regexp:"Invalid",email:"Invalid email address",url:"Invalid URL",match:t.template('Must match field "<%= field %>"',null,r.templateSettings)},e.required=function(e){return e=t.extend({type:"required",message:this.errMessages.required},e),function(r){e.value=r;var i={type:e.type,message:t.isFunction(e.message)?e.message(e):e.message};if(r===null||r===undefined||r===!1||r==="")return i}},e.regexp=function(e){if(!e.regexp)throw new Error('Missing required "regexp" option for "regexp" validator');return e=t.extend({type:"regexp",match:!0,message:this.errMessages.regexp},e),function(r){e.value=r;var i={type:e.type,message:t.isFunction(e.message)?e.message(e):e.message};if(r===null||r===undefined||r==="")return;if(e.match?!e.regexp.test(r):e.regexp.test(r))return i}},e.email=function(n){return n=t.extend({type:"email",message:this.errMessages.email,regexp:/^[\w\-]{1,}([\w\-\+.]{1,1}[\w\-]{1,}){0,}[@][\w\-]{1,}([.]([\w\-]{1,})){1,3}$/},n),e.regexp(n)},e.url=function(n){return n=t.extend({type:"url",message:this.errMessages.url,regexp:/^(http|https):\/\/(([A-Z0-9][A-Z0-9_\-]*)(\.[A-Z0-9][A-Z0-9_\-]*)+)(:(\d+))?\/?/i},n),e.regexp(n)},e.match=function(e){if(!e.field)throw new Error('Missing required "field" options for "match" validator');return e=t.extend({type:"match",message:this.errMessages.match},e),function(r,i){e.value=r;var s={type:e.type,message:t.isFunction(e.message)?e.message(e):e.message};if(r===null||r===undefined||r==="")return;if(r!==i[e.field])return s}},e}(),r.Fieldset=n.View.extend({initialize:function(e){e=e||{};var n=this.schema=this.createSchema(e.schema);this.fields=t.pick(e.fields,n.fields),this.template=e.template||this.constructor.template},createSchema:function(e){return t.isArray(e)&&(e={fields:e}),e.legend=e.legend||null,e},getFieldAt:function(e){var t=this.schema.fields[e];return this.fields[t]},templateData:function(){return this.schema},render:function(){var n=this.schema,r=this.fields,i=e(e.trim(this.template(t.result(this,"templateData"))));return i.find("[data-fields]").add(i).each(function(n,i){var s=e(i),o=s.attr("data-fields");if(t.isUndefined(o))return;t.each(r,function(e){s.append(e.render().el)})}),this.setElement(i),this},remove:function(){t.each(this.fields,function(e){e.remove()}),n.View.prototype.remove.call(this)}},{template:t.template("    <fieldset data-fields>      <% if (legend) { %>        <legend><%= legend %></legend>      <% } %>    </fieldset>  ",null,r.templateSettings)}),r.Field=n.View.extend({initialize:function(e){e=e||{},t.extend(this,t.pick(e,"form","key","model","value","idPrefix"));var n=this.schema=this.createSchema(e.schema);this.template=e.template||n.template||this.constructor.template,this.errorClassName=e.errorClassName||this.constructor.errorClassName,this.editor=this.createEditor()},createSchema:function(e){return t.isString(e)&&(e={type:e}),e=t.extend({type:"Text",title:this.createTitle()},e),e.type=t.isString(e.type)?r.editors[e.type]:e.type,e},createEditor:function(){var e=t.extend(t.pick(this,"schema","form","key","model","value"),{id:this.createEditorId()}),n=this.schema.type;return new n(e)},createEditorId:function(){var e=this.idPrefix,n=this.key;return n=n.replace(/\./g,"_"),t.isString(e)||t.isNumber(e)?e+n:t.isNull(e)?n:this.model?this.model.cid+"_"+n:n},createTitle:function(){var e=this.key;return e=e.replace(/([A-Z])/g," $1"),e=e.replace(/^./,function(e){return e.toUpperCase()}),e},templateData:function(){var e=this.schema;return{help:e.help||"",title:e.title,fieldAttrs:e.fieldAttrs,editorAttrs:e.editorAttrs,key:this.key,editorId:this.editor.id}},render:function(){var n=this.schema,i=this.editor;if(n.type==r.editors.Hidden)return this.setElement(i.render().el);var s=e(e.trim(this.template(t.result(this,"templateData"))));return n.fieldClass&&s.addClass(n.fieldClass),n.fieldAttrs&&s.attr(n.fieldAttrs),s.find("[data-editor]").add(s).each(function(n,r){var s=e(r),o=s.attr("data-editor");if(t.isUndefined(o))return;s.append(i.render().el)}),this.setElement(s),this},validate:function(){var e=this.editor.validate();return e?this.setError(e.message):this.clearError(),e},setError:function(e){if(this.editor.hasNestedForm)return;this.$el.addClass(this.errorClassName),this.$("[data-error]").html(e)},clearError:function(){this.$el.removeClass(this.errorClassName),this.$("[data-error]").empty()},commit:function(){return this.editor.commit()},getValue:function(){return this.editor.getValue()},setValue:function(e){this.editor.setValue(e)},focus:function(){this.editor.focus()},blur:function(){this.editor.blur()},remove:function(){this.editor.remove(),n.View.prototype.remove.call(this)}},{template:t.template('    <div>      <label for="<%= editorId %>"><%= title %></label>      <div>        <span data-editor></span>        <div data-error></div>        <div><%= help %></div>      </div>    </div>  ',null,r.templateSettings),errorClassName:"error"}),r.NestedField=r.Field.extend({template:t.template(e.trim("    <div>      <span data-editor></span>      <% if (help) { %>        <div><%= help %></div>      <% } %>      <div data-error></div>    </div>  "),null,r.templateSettings)}),r.Editor=r.editors.Base=n.View.extend({defaultValue:null,hasFocus:!1,initialize:function(e){var e=e||{};if(e.model){if(!e.key)throw new Error("Missing option: 'key'");this.model=e.model,this.value=this.model.get(e.key)}else e.value!==undefined&&(this.value=e.value);this.value===undefined&&(this.value=this.defaultValue),t.extend(this,t.pick(e,"key","form"));var n=this.schema=e.schema||{};this.validators=e.validators||n.validators,this.$el.attr("id",this.id),this.$el.attr("name",this.getName()),n.editorClass&&this.$el.addClass(n.editorClass),n.editorAttrs&&this.$el.attr(n.editorAttrs)},getName:function(){var e=this.key||"";return e.replace(/\./g,"_")},getValue:function(){return this.value},setValue:function(e){this.value=e},focus:function(){throw new Error("Not implemented")},blur:function(){throw new Error("Not implemented")},commit:function(e){var t=this.validate();if(t)return t;this.listenTo(this.model,"invalid",function(e,n){t=n}),this.model.set(this.key,this.getValue(),e);if(t)return t},validate:function(){var e=this.$el,n=null,r=this.getValue(),i=this.form?this.form.getValue():{},s=this.validators,o=this.getValidator;return s&&t.every(s,function(e){return n=o(e)(r,i),n?!1:!0}),n},trigger:function(e){return e==="focus"?this.hasFocus=!0:e==="blur"&&(this.hasFocus=!1),n.View.prototype.trigger.apply(this,arguments)},getValidator:function(e){var n=r.validators;if(t.isRegExp(e))return n.regexp({regexp:e});if(t.isString(e)){if(!n[e])throw new Error('Validator "'+e+'" not found');return n[e]()}if(t.isFunction(e))return e;if(t.isObject(e)&&e.type){var i=e;return n[i.type](i)}throw new Error("Invalid validator: "+e)}}),r.editors.Text=r.Editor.extend({tagName:"input",defaultValue:"",previousValue:"",events:{keyup:"determineChange",keypress:function(e){var t=this;setTimeout(function(){t.determineChange()},0)},select:function(e){this.trigger("select",this)},focus:function(e){this.trigger("focus",this)},blur:function(e){this.trigger("blur",this)}},initialize:function(e){r.editors.Base.prototype.initialize.call(this,e);var t=this.schema,n="text";t&&t.editorAttrs&&t.editorAttrs.type&&(n=t.editorAttrs.type),t&&t.dataType&&(n=t.dataType),this.$el.attr("type",n)},render:function(){return this.setValue(this.value),this},determineChange:function(e){var t=this.$el.val(),n=t!==this.previousValue;n&&(this.previousValue=t,this.trigger("change",this))},getValue:function(){return this.$el.val()},setValue:function(e){this.$el.val(e)},focus:function(){if(this.hasFocus)return;this.$el.focus()},blur:function(){if(!this.hasFocus)return;this.$el.blur()},select:function(){this.$el.select()}}),r.editors.TextArea=r.editors.Text.extend({tagName:"textarea",initialize:function(e){r.editors.Base.prototype.initialize.call(this,e)}}),r.editors.Password=r.editors.Text.extend({initialize:function(e){r.editors.Text.prototype.initialize.call(this,e),this.$el.attr("type","password")}}),r.editors.Number=r.editors.Text.extend({defaultValue:0,events:t.extend({},r.editors.Text.prototype.events,{keypress:"onKeyPress",change:"onKeyPress"}),initialize:function(e){r.editors.Text.prototype.initialize.call(this,e);var t=this.schema;this.$el.attr("type","number"),(!t||!t.editorAttrs||!t.editorAttrs.step)&&this.$el.attr("step","any")},onKeyPress:function(e){var t=this,n=function(){setTimeout(function(){t.determineChange()},0)};if(e.charCode===0){n();return}var r=this.$el.val();e.charCode!=undefined&&(r+=String.fromCharCode(e.charCode));var i=/^[0-9]*\.?[0-9]*?$/.test(r);i?n():e.preventDefault()},getValue:function(){var e=this.$el.val();return e===""?null:parseFloat(e,10)},setValue:function(e){e=function(){return t.isNumber(e)?e:t.isString(e)&&e!==""?parseFloat(e,10):null}(),t.isNaN(e)&&(e=null),r.editors.Text.prototype.setValue.call(this,e)}}),r.editors.Hidden=r.editors.Text.extend({defaultValue:"",initialize:function(e){r.editors.Text.prototype.initialize.call(this,e),this.$el.attr("type","hidden")},focus:function(){},blur:function(){}}),r.editors.Checkbox=r.editors.Base.extend({defaultValue:!1,tagName:"input",events:{click:function(e){this.trigger("change",this)},focus:function(e){this.trigger("focus",this)},blur:function(e){this.trigger("blur",this)}},initialize:function(e){r.editors.Base.prototype.initialize.call(this,e),this.$el.attr("type","checkbox")},render:function(){return this.setValue(this.value),this},getValue:function(){return this.$el.prop("checked")},setValue:function(e){e?this.$el.prop("checked",!0):this.$el.prop("checked",!1)},focus:function(){if(this.hasFocus)return;this.$el.focus()},blur:function(){if(!this.hasFocus)return;this.$el.blur()}}),r.editors.Select=r.editors.Base.extend({tagName:"select",events:{change:function(e){this.trigger("change",this)},focus:function(e){this.trigger("focus",this)},blur:function(e){this.trigger("blur",this)}},initialize:function(e){r.editors.Base.prototype.initialize.call(this,e);if(!this.schema||!this.schema.options)throw new Error("Missing required 'schema.options'")},render:function(){return this.setOptions(this.schema.options),this},setOptions:function(e){var r=this;if(e instanceof n.Collection){var i=e;i.length>0?this.renderOptions(e):i.fetch({success:function(t){r.renderOptions(e)}})}else t.isFunction(e)?e(function(e){r.renderOptions(e)},r):this.renderOptions(e)},renderOptions:function(e){var t=this.$el,n;n=this._getOptionsHtml(e),t.html(n),this.setValue(this.value)},_getOptionsHtml:function(e){var r;if(t.isString(e))r=e;else if(t.isArray(e))r=this._arrayToHtml(e);else if(e instanceof n.Collection)r=this._collectionToHtml(e);else if(t.isFunction(e)){var i;e(function(e){i=e},this),r=this._getOptionsHtml(i)}else r=this._objectToHtml(e);return r},getValue:function(){return this.$el.val()},setValue:function(e){this.$el.val(e)},focus:function(){if(this.hasFocus)return;this.$el.focus()},blur:function(){if(!this.hasFocus)return;this.$el.blur()},_collectionToHtml:function(e){var t=[];e.each(function(e){t.push({val:e.id,label:e.toString()})});var n=this._arrayToHtml(t);return n},_objectToHtml:function(e){var t=[];for(var n in e)e.hasOwnProperty(n)&&t.push({val:n,label:e[n]});var r=this._arrayToHtml(t);return r},_arrayToHtml:function(e){var n=[];return t.each(e,function(e){if(t.isObject(e))if(e.group)n.push('<optgroup label="'+e.group+'">'),n.push(this._getOptionsHtml(e.options)),n.push("</optgroup>");else{var r=e.val||e.val===0?e.val:"";n.push('<option value="'+r+'">'+e.label+"</option>")}else n.push("<option>"+e+"</option>")},this),n.join("")}}),r.editors.Radio=r.editors.Select.extend({tagName:"ul",events:{"change input[type=radio]":function(){this.trigger("change",this)},"focus input[type=radio]":function(){if(this.hasFocus)return;this.trigger("focus",this)},"blur input[type=radio]":function(){if(!this.hasFocus)return;var e=this;setTimeout(function(){if(e.$("input[type=radio]:focus")[0])return;e.trigger("blur",e)},0)}},getValue:function(){return this.$("input[type=radio]:checked").val()},setValue:function(e){this.$("input[type=radio]").val([e])},focus:function(){if(this.hasFocus)return;var e=this.$("input[type=radio]:checked");if(e[0]){e.focus();return}this.$("input[type=radio]").first().focus()},blur:function(){if(!this.hasFocus)return;this.$("input[type=radio]:focus").blur()},_arrayToHtml:function(e){var n=[],r=this;return t.each(e,function(e,i){var s="<li>";if(t.isObject(e)){var o=e.val||e.val===0?e.val:"";s+='<input type="radio" name="'+r.getName()+'" value="'+o+'" id="'+r.id+"-"+i+'" />',s+='<label for="'+r.id+"-"+i+'">'+e.label+"</label>"}else s+='<input type="radio" name="'+r.getName()+'" value="'+e+'" id="'+r.id+"-"+i+'" />',s+='<label for="'+r.id+"-"+i+'">'+e+"</label>";s+="</li>",n.push(s)}),n.join("")}}),r.editors.Checkboxes=r.editors.Select.extend({tagName:"ul",groupNumber:0,events:{"click input[type=checkbox]":function(){this.trigger("change",this)},"focus input[type=checkbox]":function(){if(this.hasFocus)return;this.trigger("focus",this)},"blur input[type=checkbox]":function(){if(!this.hasFocus)return;var e=this;setTimeout(function(){if(e.$("input[type=checkbox]:focus")[0])return;e.trigger("blur",e)},0)}},getValue:function(){var t=[];return this.$("input[type=checkbox]:checked").each(function(){t.push(e(this).val())}),t},setValue:function(e){t.isArray(e)||(e=[e]),this.$("input[type=checkbox]").val(e)},focus:function(){if(this.hasFocus)return;this.$("input[type=checkbox]").first().focus()},blur:function(){if(!this.hasFocus)return;this.$("input[type=checkbox]:focus").blur()},_arrayToHtml:function(e){var n=[],r=this;return t.each(e,function(e,i){var s="<li>",o=!0;if(t.isObject(e))if(e.group){var u=r.id;r.id+="-"+r.groupNumber++,s='<fieldset class="group"> <legend>'+e.group+"</legend>",s+=r._arrayToHtml(e.options),s+="</fieldset>",r.id=u,o=!1}else{var a=e.val||e.val===0?e.val:"";s+='<input type="checkbox" name="'+r.getName()+'" value="'+a+'" id="'+r.id+"-"+i+'" />',s+='<label for="'+r.id+"-"+i+'">'+e.label+"</label>"}else s+='<input type="checkbox" name="'+r.getName()+'" value="'+e+'" id="'+r.id+"-"+i+'" />',s+='<label for="'+r.id+"-"+i+'">'+e+"</label>";o&&(s+="</li>"),n.push(s)}),n.join("")}}),r.editors.Object=r.editors.Base.extend({hasNestedForm:!0,initialize:function(e){this.value={},r.editors.Base.prototype.initialize.call(this,e);if(!this.form)throw new Error('Missing required option "form"');if(!this.schema.subSchema)throw new Error("Missing required 'schema.subSchema' option for Object editor")},render:function(){var e=this.form.constructor;return this.nestedForm=new e({schema:this.schema.subSchema,data:this.value,idPrefix:this.id+"_",Field:e.NestedField}),this._observeFormEvents(),this.$el.html(this.nestedForm.render().el),this.hasFocus&&this.trigger("blur",this),this},getValue:function(){return this.nestedForm?this.nestedForm.getValue():this.value},setValue:function(e){this.value=e,this.render()},focus:function(){if(this.hasFocus)return;this.nestedForm.focus()},blur:function(){if(!this.hasFocus)return;this.nestedForm.blur()},remove:function(){this.nestedForm.remove(),n.View.prototype.remove.call(this)},validate:function(){return this.nestedForm.validate()},_observeFormEvents:function(){if(!this.nestedForm)return;this.nestedForm.on("all",function(){var e=t.toArray(arguments);e[1]=this,this.trigger.apply(this,e)},this)}}),r.editors.NestedModel=r.editors.Object.extend({initialize:function(e){r.editors.Base.prototype.initialize.call(this,e);if(!this.form)throw new Error('Missing required option "form"');if(!e.schema.model)throw new Error('Missing required "schema.model" option for NestedModel editor')},render:function(){var e=this.form.constructor,t=this.value||{},n=this.key,r=this.schema.model,i=t.constructor===r?t:new r(t);return this.nestedForm=new e({model:i,idPrefix:this.id+"_",fieldTemplate:"nestedField"}),this._observeFormEvents(),this.$el.html(this.nestedForm.render().el),this.hasFocus&&this.trigger("blur",this),this},commit:function(){var e=this.nestedForm.commit();return e?(this.$el.addClass("error"),e):r.editors.Object.prototype.commit.call(this)}}),r.editors.Date=r.editors.Base.extend({events:{"change select":function(){this.updateHidden(),this.trigger("change",this)},"focus select":function(){if(this.hasFocus)return;this.trigger("focus",this)},"blur select":function(){if(!this.hasFocus)return;var e=this;setTimeout(function(){if(e.$("select:focus")[0])return;e.trigger("blur",e)},0)}},initialize:function(e){e=e||{},r.editors.Base.prototype.initialize.call(this,e);var n=r.editors.Date,i=new Date;this.options=t.extend({monthNames:n.monthNames,showMonthNames:n.showMonthNames},e),this.schema=t.extend({yearStart:i.getFullYear()-100,yearEnd:i.getFullYear()},e.schema||{}),this.value&&!t.isDate(this.value)&&(this.value=new Date(this.value));if(!this.value){var s=new Date;s.setSeconds(0),s.setMilliseconds(0),this.value=s}this.template=e.template||this.constructor.template},render:function(){var n=this.options,r=this.schema,i=t.map(t.range(1,32),function(e){return'<option value="'+e+'">'+e+"</option>"}),s=t.map(t.range(0,12),function(e){var t=n.showMonthNames?n.monthNames[e]:e+1;return'<option value="'+e+'">'+t+"</option>"}),o=r.yearStart<r.yearEnd?t.range(r.yearStart,r.yearEnd+1):t.range(r.yearStart,r.yearEnd-1,-1),u=t.map(o,function(e){return'<option value="'+e+'">'+e+"</option>"}),a=e(e.trim(this.template({dates:i.join(""),months:s.join(""),years:u.join("")})));return this.$date=a.find('[data-type="date"]'),this.$month=a.find('[data-type="month"]'),this.$year=a.find('[data-type="year"]'),this.$hidden=e('<input type="hidden" name="'+this.key+'" />'),a.append(this.$hidden),this.setValue(this.value),this.setElement(a),this.$el.attr("id",this.id),this.$el.attr("name",this.getName()),this.hasFocus&&this.trigger("blur",this),this},getValue:function(){var e=this.$year.val(),t=this.$month.val(),n=this.$date.val();return!e||!t||!n?null:new Date(e,t,n)},setValue:function(e){this.$date.val(e.getDate()),this.$month.val(e.getMonth()),this.$year.val(e.getFullYear()),this.updateHidden()},focus:function(){if(this.hasFocus)return;this.$("select").first().focus()},blur:function(){if(!this.hasFocus)return;this.$("select:focus").blur()},updateHidden:function(){var e=this.getValue();t.isDate(e)&&(e=e.toISOString()),this.$hidden.val(e)}},{template:t.template('    <div>      <select data-type="date"><%= dates %></select>      <select data-type="month"><%= months %></select>      <select data-type="year"><%= years %></select>    </div>  ',null,r.templateSettings),showMonthNames:!0,monthNames:["January","February","March","April","May","June","July","August","September","October","November","December"]}),r.editors.DateTime=r.editors.Base.extend({events:{"change select":function(){this.updateHidden(),this.trigger("change",this)},"focus select":function(){if(this.hasFocus)return;this.trigger("focus",this)},"blur select":function(){if(!this.hasFocus)return;var e=this;setTimeout(function(){if(e.$("select:focus")[0])return;e.trigger("blur",e)},0)}},initialize:function(e){e=e||{},r.editors.Base.prototype.initialize.call(this,e),this.options=t.extend({DateEditor:r.editors.DateTime.DateEditor},e),this.schema=t.extend({minsInterval:15},e.schema||{}),this.dateEditor=new this.options.DateEditor(e),this.value=this.dateEditor.value,this.template=e.template||this.constructor.template},render:function(){function n(e){return e<10?"0"+e:e}var r=this.schema,i=t.map(t.range(0,24),function(e){return'<option value="'+e+'">'+n(e)+"</option>"}),s=t.map(t.range(0,60,r.minsInterval),function(e){return'<option value="'+e+'">'+n(e)+"</option>"}),o=e(e.trim(this.template({hours:i.join(),mins:s.join()})));return o.find("[data-date]").append(this.dateEditor.render().el),this.$hour=o.find('select[data-type="hour"]'),this.$min=o.find('select[data-type="min"]'),this.$hidden=o.find('input[type="hidden"]'),this.setValue(this.value),this.setElement(o),this.$el.attr("id",this.id),this.$el.attr("name",this.getName()),this.hasFocus&&this.trigger("blur",this),this},getValue:function(){var e=this.dateEditor.getValue(),t=this.$hour.val(),n=this.$min.val();return!e||!t||!n?null:(e.setHours(t),e.setMinutes(n),e)},setValue:function(e){t.isDate(e)||(e=new Date(e)),this.dateEditor.setValue(e),this.$hour.val(e.getHours()),this.$min.val(e.getMinutes()),this.updateHidden()},focus:function(){if(this.hasFocus)return;this.$("select").first().focus()},blur:function(){if(!this.hasFocus)return;this.$("select:focus").blur()},updateHidden:function(){var e=this.getValue();t.isDate(e)&&(e=e.toISOString()),this.$hidden.val(e)},remove:function(){this.dateEditor.remove(),r.editors.Base.prototype.remove.call(this)}},{template:t.template('    <div class="bbf-datetime">      <div class="bbf-date-container" data-date></div>      <select data-type="hour"><%= hours %></select>      :      <select data-type="min"><%= mins %></select>    </div>  ',null,r.templateSettings),DateEditor:r.editors.Date}),r.VERSION="0.13.0",n.Form=r,r});