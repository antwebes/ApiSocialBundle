var users = [];
var current_user = null;
var last_score = 0;

$('[data-js-gender]').click(function( event ) {
    if($( this ).attr('data-js-gender').toLowerCase() != ''){
        var parameter_gender_key = users_photos_messages[app_request_locale].parameter_gender;
        window.location = vote_photos_path + '?' +parameter_gender_key+'='+$( this ).attr('data-js-gender').toLowerCase();
    }else{
    	window.location = vote_photos_path;
    }

});

function getUsers(callback) {

    var parameters = {
        "es":{
            "hombres":"Male",
             "mujeres":"Female",
             "otros": "Other",
        },
        "en":{
            "man":"Male",
            "women":"Female",
            "other": "Other",
        }
    };

    var url_photo_endpoint = window.api_endpoint + '/api/users?limit=30&offset=0&order=randomly%3Dasc&filters=language%3Des%2Chas_profile_photo%3D1';
    if (gender_selected != undefined && gender_selected != ''){
        var parameter_gender_value=  parameters[app_request_locale][gender_selected];
        url_photo_endpoint += '%2Cgender%3D'+parameter_gender_value;
    }
    $.ajax({
            url: url_photo_endpoint,
            beforeSend: function(xhr, settings) { xhr.setRequestHeader('Authorization','Bearer ' + window.token); },
            success: function(data, status) {
                users = data.resources;
                showUser();
                if( callback != undefined) callback(users);
            }
        }
    );
}
$("#go_to_next").click(function () {
    nextUser();
});
$("#go_to_report").click(function () {
    window.location = app_request_base_url +"/usuarios/"+current_user.id+"/fotos/"+current_user.profile.profile_photo.id+"/reportar";
});
function nextUser() {
    if(users.length == 0){
    	getUsers();
    } else {
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

        user_link = app_request_base_url+ "/usuarios/"+user.username+"-"+user.id;
        title = title + '<br><a title="user.view_profile{trans_js}'+user.username+'" href="'+user_link+'">'+user.username+'</a>';
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
    user_link = app_request_base_url+ "/usuarios/"+current_user.username+"-"+current_user.id;
    $("#username").html('<a title="user.view_profile{trans_js}'+current_user.username+'" href="'+user_link+'">'+current_user.username+'</a>');
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
            url: window.api_endpoint + "/api/users/"+app_user_id+"/vote",
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
        sendScore(score, current_user);
        nextUser();
        return false;
    }
});
nextUser();