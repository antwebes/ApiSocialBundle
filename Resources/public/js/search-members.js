$('#members_search')
    .unbind('keypress keyup')
    .bind('keypress keyup', function(e){
        if ($(this).val().length < 3 && e.keyCode != 13) {
         return;
        }
});


$('#search-members-form').submit(function( event ) {
    if($('#members_search').val() == ''){
        event.preventDefault();
        window.location = ant_user_user_users;
    }
    if($('#members_search').val().length < 3){
        event.preventDefault();
    }
});
if($('button[data-id="button_user_search_clear"]')){
    $('button[data-id="button_user_search_clear"]').click(function(event){
        $('#members_search').val('');
        window.location = ant_user_user_users;
    });
}
