
/**
 * Check if all parameters are defined
 * @param configUserVoyeur
 * var configUserVoyeur = {
 *  api_endpoint: '',
 *  resource_route: '',
 *  token: '',
 *  noPhotoSource : '',
 *  limit: '',
 *  showUserPath: '',
 *  access_pretty: false
 *  };
 */
function checkParameters(configUserVoyeur)
{
    if(typeof(configUserVoyeur.api_endpoint) === 'undefined'){
        throw new Exception('The attribute api_endpoint is undefined');
    }

    if(typeof(configUserVoyeur.resource_route) === 'undefined'){
        throw new Exception('The attribute resource_route is undefined');
    }

    if(typeof(configUserVoyeur.token) === 'undefined'){
        throw new Exception('The attribute token is undefined');
    }

    if(typeof(configUserVoyeur.noPhotoSource) === 'undefined'){
        throw new Exception('The attribute noPhotoSource is undefined');
    }

    if(typeof(configUserVoyeur.limit) === 'undefined'){
        throw new Exception('The attribute limit is undefined');
    }

    if(typeof(configUserVoyeur.showUserPath) === 'undefined'){
        throw new Exception('The attribute showUserPath is undefined');
    }
}
/**
 * Find user voyeur and render template
 *
 * @param configUserVoyeur
 * var configUserVoyeur = {
 *  api_endpoint: '',
 *  resource_route: '',
 *  token: '',
 *  noPhotoSource : '',
 *  limit: '',
 *  showUserPath: '',
 *  access_pretty: false
 *  };
 */
function findUserVoyeur(configUserVoyeur)
{
    checkParameters(configUserVoyeur);

    var api_call = configUserVoyeur.api_endpoint + configUserVoyeur.resource_route;

    $.ajax({
        url: api_call,
        method: 'GET',
        dataType: 'JSON',
        data: { limit: configUserVoyeur.limit },
        beforeSend: function( xhr ) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + configUserVoyeur.token);
        }
    }).done(function(data, textStatus, jqXHR) {
        var responseData = jqXHR.responseJSON;
        if(responseData.total > 0){
            if(configUserVoyeur.access_pretty == true){
                renderUserVoyeurPretty(responseData.resources,configUserVoyeur.noPhotoSource, configUserVoyeur.showUserPath);
            }else{
                renderUserVoyeur(responseData.resources,configUserVoyeur.noPhotoSource, configUserVoyeur.showUserPath);

            }

        }

    }).fail(function ( jqXHR, textStatus, errorThrown) {
        throw errorThrown;
    });
}

/**
 * Render template userVoyeurTemplate
 * @param users collection users
 * @param noPhotoSource the no photo path
 * @param showUserPath the user show path
 */
function renderUserVoyeur(users,noPhotoSource, showUserPath){
    $.each(users,function(index, value){
        var htmlItem = userVoyeurTemplate(value.participant,noPhotoSource, showUserPath);
        $('[data-js-id="rowVoyeur"]').append(htmlItem);
    });

}

function userVoyeurTemplate(visitor,noPhotoSource, showUserPath)
{
    var showUserPath = showUserPath.replace('%7Bparam%7D',  visitor.username );


    if(visitor.profile != null && visitor.profile.profile_photo != null){
        imageSource = '<img src="'+visitor.profile.profile_photo.path_icon+'" class="avatar avatar-50 photo" width="50" height="50" title="' + visitor.username + '">';
    }else{
        imageSource = '<img src="'+noPhotoSource+' " title="'+ visitor.username + '">';
    }

    var html = '' +
        '<div class="col-lg-4">' +
        '   <a href="'+showUserPath+'" title="'+visitor.username+'">' +
        '       <div>' +
        imageSource +
        '       </div>' +
        '       <div><div>'+
        visitor.username +
        '         </div></div>' +
        '   </a>' +
        '</div>';

    return html;
}

function renderUserVoyeurPretty(users,noPhotoSource, showUserPath)
{
    $.each(users,function(index, value){
        var visitor = value.participant;
        var participantName = visitor.username;
        var userPath = showUserPath.replace('%7Bparam%7D',  participantName );
        var imageSource = noPhotoSource;

        if(visitor.profile != null && visitor.profile.profile_photo != null){
            imageSource = visitor.profile.profile_photo.path_icon;
        }
        
        var elementUsername = $('[data-js-id="voyeurUsername-'+index+'"]');
        var elementProfilePath = $('[data-js-id="voyeurProfilePath-'+index+'"]');
        var elementImage = $('[data-js-id="voyeurImage-'+index+'"]');

        if(elementImage != undefined){
            elementImage.attr('src',imageSource);
            elementImage.attr('title',participantName);
        }

        if(elementProfilePath != undefined){
            elementProfilePath.attr('href',userPath);
            elementProfilePath.attr('title',participantName);
        }

        if(elementUsername != undefined){
            elementUsername.append(participantName)
        }
    });
}