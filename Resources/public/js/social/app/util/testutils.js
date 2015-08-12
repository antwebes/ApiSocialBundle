var waitsForPresenceOfDOMElement = function(selector, message, timeout){
    message = message || "should see pressence of element matching the selector: "+selector;
    timeout = timeout || 3000;

    waitsFor(function(){
        return $(selector).length > 0;
    }, message, timeout);
};

var waitsForSpyToBeCalled = function(spy, message, timeout){
    message = message || "should call spy";
    timeout = timeout || 3000;

    waitsFor(function(){
        return spy.called;
    }, message, timeout);
};

var ensureNumberOfElementsArePresent = function(selector, number, timeout){
    timeout = timeout || 3000;
    var message = "Expected to see "+number+" elements matching the selector: "+selector;
    waitsFor(function(){
        return $(selector).length == number;
    }, message, timeout);

    runs(function(){});
};