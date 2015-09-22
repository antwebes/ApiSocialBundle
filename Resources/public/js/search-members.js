function initSearchBox(url_search_action)
{
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
            window.location = url_search_action;
        }
        if($('#members_search').val().length < 3){
            event.preventDefault();
        }
    });
    if($('[data-id="button_search_clear"]')){
        $('[data-id="button_search_clear"]').click(function(event){
            $('#members_search').val('');
            window.location = url_search_action;

        });
    }
}
