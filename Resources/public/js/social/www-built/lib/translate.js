/**
 * Microlib for translations with support for placeholders and multiple plural forms.
 *
 * v1.1.0
 *
 * Usage:
 * var messages = {
 *   translationKey: 'translationValue',
 *   moduleA: {
 *       translationKey: 'value123'
 *   }
 * }
 * 
 * var options = {
 *     // These are the defaults:
 *     debug: false, //[Boolean]: Logs missing translations to console and adds @@-markers around output.
 *     namespaceSplitter: '::' //[String|RegExp]: You can customize the part which splits namespace and translationKeys.
 * }
 * 
 * var t = libTranslate.getTranslationFunction(messages, [options])
 * 
 * t('translationKey')
 * t('translationKey', count)
 * t('translationKey', {replaceKey: 'replacevalue'})
 * t('translationKey', count, {replaceKey: 'replacevalue'})
 * t('translationKey', {replaceKey: 'replacevalue'}, count)
 * t('moduleA::translationKey')
 *
 *
 * @author Jonas Girnatis <dermusterknabe@gmail.com>
 * @licence May be freely distributed under the MIT license.
 */

(function(){"use strict";var e=function(e){return!isNaN(parseFloat(e))&&isFinite(e)},t=function(e){return typeof e=="object"&&e!==null},n=function(e){return Object.prototype.toString.call(e)==="[object String]"},r=function(){var e=window.console,t;t=e?e.log:function(){},t.apply(e,arguments)};window.libTranslate={getTranslationFunction:function(i,s){function f(e){if(i[e])return i[e];var t=e.split(u),n=t[0],r=t[1];return i[n]&&i[n][r]?i[n][r]:null}function l(e,n){if(t(e)){var r=Object.keys(e),i;if(r.length===0)return a("[Translation] No plural forms found."),null;for(var s=0;s<r.length;s++)r[s].indexOf("gt")===0&&(i=parseInt(r[s].replace("gt",""),10));e[n]?e=e[n]:n>i?e=e["gt"+i]:e.n?e=e.n:(a('[Translation] No plural forms found for count:"'+n+'" in',e),e=e[Object.keys(e).reverse()[0]])}return e}function c(e,t){return n(e)?e.replace(/\{(\w*)\}/g,function(e,n){return t.hasOwnProperty(n)?t.hasOwnProperty(n)?t[n]:n:(a('Could not find replacement "'+n+'" in provided replacements object:',t),"{"+n+"}")}):e}s=t(s)?s:{};var o=s.debug,u=s.namespaceSplitter||"::",a=function(){o&&r.apply(null,arguments)};return function(n){var r=t(arguments[1])?arguments[1]:t(arguments[2])?arguments[2]:{},i=e(arguments[1])?arguments[1]:e(arguments[2])?arguments[2]:null,s=f(n);return i!==null&&(r.n=r.n?r.n:i,s=l(s,i)),s=c(s,r),s===null&&(s=o?"@@"+n+"@@":n,a('Translation for "'+n+'" not found.')),s}}}})();