var usersFemale = [];
var usersMale = [];
var usersOther = [];
var usersAll = [];
var users = [];
var current_user = null;
var last_score = 0;

$('button[data-id="btn-gender"]').click(function( event ) {
    $('[data-id="btn-gender"]').each(function( index ){
        $( this ).removeClass('btn-success')
    });
    $( this ).addClass('btn-success');
    var buttonSelected = $( this ).val();


    if(buttonSelected == 'All'){
        users = usersAll;
    }else if(buttonSelected == 'Male'){
        users = usersMale;
    }else if(buttonSelected == 'Female'){
        users = usersFemale;
    }else if(buttonSelected == 'Other'){
        users = usersOther;
    }else{
        users = usersAll;
    }
    showUser();
});

//this function return all users type male
function getUsers(callback) {
    $.when(
        $.ajax({
                url: window.api_endpoint + '/api/users?limit=30&offset=0&order=randomly%3Dasc&filters=language%3Des%2Chas_profile_photo%3D1',
                beforeSend: function(xhr, settings) { xhr.setRequestHeader('Authorization','Bearer ' + window.token); },
                success: function(data, status) {
                    usersAll = data.resources;
                    users = data.resources;
                    current_user = data.resources[0];
                    if( callback != undefined) callback(usersAll);
                }
            }
        ),

        $.ajax({
                url: window.api_endpoint + '/api/users?limit=30&offset=0&order=randomly%3Dasc&filters=language%3Des%2Chas_profile_photo%3D1%2Cgender=Female',
                beforeSend: function(xhr, settings) { xhr.setRequestHeader('Authorization','Bearer ' + window.token); },
                success: function(data, status) {
                    usersFemale = data.resources;
                    current_user = data.resources[0];
                    if( callback != undefined) callback(usersFemale);
                }
            }
        ),

        $.ajax({
                url: window.api_endpoint + '/api/users?limit=30&offset=0&order=randomly%3Dasc&filters=language%3Des%2Chas_profile_photo%3D1%2Cgender=Male',
                beforeSend: function(xhr, settings) { xhr.setRequestHeader('Authorization','Bearer ' + window.token); },
                success: function(data, status) {
                    usersMale = data.resources;
                    if( callback != undefined) callback(usersMale);
                }
            }
        ),

        $.ajax({
                url: window.api_endpoint + '/api/users?limit=30&offset=0&order=randomly%3Dasc&filters=language%3Des%2Chas_profile_photo%3D1%2Cgender=Other',
                beforeSend: function(xhr, settings) { xhr.setRequestHeader('Authorization','Bearer ' + window.token); },
                success: function(data, status) {
                    usersOther = data.resources;
                    if( callback != undefined) callback(usersOther);
                }
            }
        )
    ).done(function(){
            showUser();
    });
}

$("#go_to_next").click(function () {
    nextUser();
});



$("#go_to_report").click(function () {
    window.location = app_request_base_url +"/usuarios/"+current_user.id+"/fotos/"+current_user.profile.profile_photo.id+"/reportar";
});


function nextUser() {
    if(users.length == 0) getUsers();
    else {
        showUser();
    }
}

function previsualizeUser(user, container, next) {
    var title= "";
    $(container).empty();
    if(next) {
        title = users_photos_messages[app_request_locale].photo_title_next;
    } else {
        title = users_photos_messages[app_request_locale].photo_title_previous;
    }
    var txt = $('<legend>');
    txt.html(title);
    txt.appendTo(container);

    var img = $('<img>');
    img.attr('src', user.profile.profile_photo.path_medium);
    img.appendTo(container);

    if(!next) {
        var score = parseFloat(user.profile.profile_photo.score) || 0;
        var number_votes = parseInt(user.profile.profile_photo.number_votes);

        new_score = ((score * number_votes) + last_score) / (number_votes + 1);

        number_votes++;
        var txt = $('<p>');
        var msg =  users_photos_messages[app_request_locale].label_votes + " " + number_votes + " " + users_photos_messages[app_request_locale].label_score + " ";
        txt.html(msg);
        txt.appendTo(container);

    }
}

function showUser() {
    if (current_user != null) {
        previsualizeUser(current_user,"#previus_user", false);
    }
    current_user = users.shift();

    $("#username").html(current_user.username);
    $("#profile_photo").attr("src",current_user.profile.profile_photo.path_large);

    if(users[0] != undefined) {
        previsualizeUser(users[0],"#next_user", true);
    } else {
        getUsers(function (users) {
            previsualizeUser(users[0],"#next_user", true);
        });
    }

}

function sendScore(score, user) {
    $.ajax(
        {
            type: "POST",
            accept: "application/json",
            url: window.api_endpoint + "/api/users/" + app_user_id+ "/vote",
            data: {"_format":"json", "vote[photo]": user.profile.profile_photo.id, "vote[score]": score },
            beforeSend: function(xhr, settings) {
                xhr.setRequestHeader('Authorization','Bearer ' + token);
            }
        }
    );

}

$('#raty').raty({
    number: 10, path: '/bundles/apisocial/raty/images/',
    click: function(score, evt) {

        last_score = score;
        console.log(score);
        nextUser();
        sendScore(score, current_user);
        return false;
    }
});

getUsers();