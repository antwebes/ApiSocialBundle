function RealTimeException(message) {
    this.message = message;
    // Use V8's native method if available, otherwise fallback
    if ("captureStackTrace" in Error)
        Error.captureStackTrace(this, RealTimeException);
    else
        this.stack = (new Error()).stack;
}

RealTimeException.prototype = Object.create(Error.prototype);
RealTimeException.prototype.name = "RealTimeException";
RealTimeException.prototype.constructor = RealTimeException;

function addClassUserOnLineOff(username, messages){

    var container = $('[data-js-id="userBadge"]');
    container.append('<i data-js-id="userOnLine" class="fa fa-circle fa-1x userOnLineOff" title="'+messages.user_off_line+'"></i>')

}
function addClassUserOnLineOn(username, messages)
{
    var container = $('[data-js-id="userBadge"]');
    container.append('<i data-js-id="userOnLine" class="fa fa-circle fa-1x userOnLineOn" title="'+messages.user_on_line+'"></i>')
}

function findUserOnline(api_real_time_endpoint, username, messages) {
    $.ajax({
        //move parameters
        url: api_real_time_endpoint + '/users',
        dataType: 'json',
        data: {nick: username },
        method: 'GET'
    }).done(function(  data, textStatus, jqXHR ) {
            var dataObjects = jqXHR.responseJSON['hydra:member'];
            var totalItems = jqXHR.responseJSON['hydra:totalItems'];

            if(totalItems > 0){ //onLine
                addClassUserOnLineOn(username, messages);
            }else{
                addClassUserOnLineOff(username, messages)
            }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        throw new RealTimeException(jqXHR.responseText);
    });
}

