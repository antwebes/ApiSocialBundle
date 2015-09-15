/**
* Updater - jQuery plugin for timed ajax calls
*
* Based on PeriodicalUpdater (http://github.com/RobertFischer/JQuery-PeriodicalUpdater/tree/master)
*
* Copyright (c) 2009 by the following:
* * Robert Fischer (http://smokejumperit.com)
* * 360innovate (http://www.360innovate.co.uk)
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
* Version: 1.0
*/

(function(e){e.Updater=function(t,n,r){function u(){e.ajax(o)}var i=jQuery.extend(!0,{url:t,method:"get",data:"",type:"json",interval:"3000"},n),s=i.interval,o=jQuery.extend(!0,{},i);o.dataType=i.type,o.type=i.method,o.success=function(e){PeriodicalTimer=setTimeout(u,s),r&&r(e)},e(function(){u()})}})(jQuery);