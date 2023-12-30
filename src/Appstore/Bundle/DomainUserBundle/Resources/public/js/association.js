function AssociationProcess() {


    $( ".sms" ).click(function() {
        var url = $(this).attr('data-url');
        var id = $(this).attr('data-id');
        var title = $(this).attr('data-title');
        $.MessageBox({
            input    : true,
            message  : "Send SMS To "+title
        }).done(function(data){
            if ($.trim(data)) {
                $.get(url,{sms : data});
            }
        });
    });


    $(document).on('click', '.view', function () {

        var url = $(this).attr("data-url");
        var title = $(this).attr("data-title");
        $('.dialogModal_header').html(title);
        $('.dialog_content').dialogModal({
            topOffset: 0,
            top: 0,
            type: '',
            onOkBut: function (event, el, current) {
            },
            onCancelBut: function (event, el, current) {
            },
            onLoad: function (el, current) {
                $.ajax({
                    url: url,
                    async: true,
                    success: function (response) {
                        datatableLoad();
                        el.find('.dialogModal_content').html(response);
                        datatableLoad();
                    }
                });
            },
            onClose: function (el, current) {
            },
            onChange: function (el, current) {
            }
        });

    });

    $(document).on("click", " .approve, .confirm, .remove , .process , .remove-tr , .item-disable , .item-remove", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get(url, function( data ) {
                    location.reload();
                });
            }
        });

    });
}

function datatableLoad() {

    $('.row-scroll').DataTable( {
        "scrollY":        "200px",
        "scrollCollapse": true,
        "paging":   false,
        "ordering": false,
        "info":     false
    } );

}

