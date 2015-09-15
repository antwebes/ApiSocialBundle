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

/*global window, console */

/* adapted to fix error with r.js */
;(function () {
    'use strict';

    var isNumeric = function(obj) { return !isNaN(parseFloat(obj)) && isFinite(obj); };
    var isObject = function(obj) { return typeof obj === 'object' && obj !== null; };
    var isString = function(obj) { return Object.prototype.toString.call(obj) === '[object String]'; };

    //funtion to wrap log
    var log = function(){
        var console = window.console;
        var _log;
        _log = console ? console.log : function () {
        };

        _log.apply( console, arguments );
    };

    window.libTranslate = {
        getTranslationFunction: function(messageObject, options) {
            options = isObject(options) ? options : {};

            var debug = options.debug;
            var namespaceSplitter = options.namespaceSplitter || '::';
            var logIfDebugMode = function(){
                if(debug){
                   log.apply(null, arguments);
                }
            };

            function getTranslationValue(translationKey) {
                if(messageObject[translationKey]) {
                    return messageObject[translationKey];
                }

                var components = translationKey.split(namespaceSplitter); //@todo make this more robust. maybe support more levels?
                var namespace = components[0];
                var key = components[1];
             
                if(messageObject[namespace] && messageObject[namespace][key]) {
                    return messageObject[namespace][key];
                }

                return null;
            }

            function getPluralValue(translation, count) {
                if (isObject(translation)) {
                    var keys = Object.keys(translation);
                    var upperCap;

                    if(keys.length === 0) {
                        logIfDebugMode('[Translation] No plural forms found.');
                        return null;
                    }

                    for(var i = 0; i < keys.length; i++) {
                        if(keys[i].indexOf('gt') === 0) {
                            upperCap = parseInt(keys[i].replace('gt', ''), 10);
                        }
                    }

                    if(translation[count]){
                        translation = translation[count];
                    } else if(count > upperCap) { //int > undefined returns false
                        translation = translation['gt' + upperCap];
                    } else if(translation.n) {
                        translation = translation.n;
                    } else {
                        logIfDebugMode('[Translation] No plural forms found for count:"' + count + '" in', translation);
                        translation = translation[Object.keys(translation).reverse()[0]];
                    }
                }

                return translation;
            }

            function replacePlaceholders(translation, replacements) {
                if (isString(translation)) {
                    return translation.replace(/\{(\w*)\}/g, function (match, key) {
                        if(!replacements.hasOwnProperty(key)) {
                            logIfDebugMode('Could not find replacement "' + key + '" in provided replacements object:', replacements);

                            return '{' + key + '}';
                        }

                        return replacements.hasOwnProperty(key) ? replacements[key] : key;
                    });
                }

                return translation;
            }

            return function (translationKey) {
                var replacements = isObject(arguments[1]) ? arguments[1] : (isObject(arguments[2]) ? arguments[2] : {});
                var count = isNumeric(arguments[1]) ? arguments[1] : (isNumeric(arguments[2]) ? arguments[2] : null);

                var translation = getTranslationValue(translationKey);

                if (count !== null) {
                    replacements.n = replacements.n ? replacements.n : count;

                    //get appropriate plural translation string
                    translation = getPluralValue(translation, count);
                }

                //replace {placeholders}
                translation = replacePlaceholders(translation, replacements);

                if (translation === null) {
                    translation = debug ? '@@' + translationKey + '@@' : translationKey;

                    logIfDebugMode('Translation for "' + translationKey + '" not found.');
                }

                return translation;
            };
        }
    };
})();