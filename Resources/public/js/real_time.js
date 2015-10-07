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
            //removeClassUserOnLine();
            var dataObjects = jqXHR.responseJSON['hydra:member'];
            var totalItems = jqXHR.responseJSON['hydra:totalItems'];

            if(totalItems > 0){ //onLine
                addClassUserOnLineOn(username, messages);
            }else{
                addClassUserOnLineOff(username, messages)
            }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        if ( console && console.log ) {
            console.log( "Error:" );
            console.log(jqXHR);
            console.log( "Error textStatus:", textStatus );
            console.log( "Error errorThrown:", errorThrown );
        }
    });
}

