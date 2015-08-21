$(function(){
    $(document).on("click", ".open-DeleteDialog", function (e) {
        e.preventDefault();

        var $this = $(this);
        var dataPath = $this.data('path');
        var message = $this.data('message');
        var title = $this.data('title');

        $parent = $(e.target).parent();

        $("#delete-dialog .dialog-message").html(message);
        $("#delete-dialog .modal-title").html(title);
        $("#modal-delete-path").val( dataPath );

        return false;
    });

    $('#delete-dialog-ok').on('click', function (e) {
        $('#delete-dialog').modal('hide');
        window.location.href = $("#modal-delete-path").val();
    });
});