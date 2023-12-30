var EditableWithLoadInit = function () {

    var initEditables = function () {
        //global settings
        $.fn.editable.defaults.inputclass = 'm-wrap';
        $.fn.editable.defaults.url = '/post';
        $.fn.editable.defaults.placement = 'right';
        $.fn.editableform.buttons = '<button type="submit" class="btn blue"><i class="icon-ok"></i></button>';
        $.fn.editableform.buttons += '<button type="button" class="btn editable-cancel"><i class="icon-remove"></i></button>';
    }
    $(".editable").editable(initEditables());

}

var EditableInit = function () {

    var initEditables = function () {
        //global settings
        $.fn.editable.defaults.inputclass = 'm-wrap';
        $.fn.editable.defaults.url = '/post';
        $.fn.editable.defaults.placement = 'right';
        $.fn.editableform.buttons = '<button type="submit" class="btn blue editable-submit"><i class="icon-ok"></i></button>';
        $.fn.editableform.buttons += '<button type="button" class="btn editable-cancel"><i class="icon-remove"></i></button>';
    }
    $(".editable").editable(initEditables());
}

$(document).on("click", ".editable-submit", function() {
    setTimeout(pageReload, 3000);
});
function pageReload() {
    location.reload();
}

$(document).on("click", ".confirm", function() {
    var url = $(this).attr('data-url');
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


var count = 0;
$('.addmore').click(function(){

    var $el = $(this);
    var $cloneBlock = $('#clone-block');
    var $clone = $cloneBlock.find('.clone:eq(0)').clone();
    $clone.find('[id]').each(function(){this.id+='someotherpart'});
    $clone.find(':text,textarea' ).val("");
    $clone.attr('id', "added"+(++count));
    $clone.find('.remove').removeClass('hidden');
    $cloneBlock.append($clone);
    $('.numeric').numeric();
});

$('#clone-block').on('click', '.remove', function(){
    $(this).closest('.clone').remove();
});

$('.trash').on("click", ".remove", function() {

    var url = $(this).attr('data-url');
    var id = $(this).attr("id");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            if ('success' == response) {
                location.reload();
            }
        },
    })
});

$( ".migration" ).click(function() {

    var url = $(this).attr('data-action');
    $.MessageBox({
        input    : true,
        message  : "Enter Migration Domain:"
    }).done(function(data){
        if ($.trim(data)) {
            $.get(url,{option:data});
            location.reload();
        }
    });
});

$(document).on('click', '#btn-selected-preview', function() {
    $('.dialogModal_header').html('Selected Stock Item');
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: Routing.generate('medicine_stock_selected_preview'),
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                    datatableLoad();
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});

function datatableLoad() {
    $('#datatable').DataTable( {
        scrollY: "500px",
        scrollCollapse: true,
        scrollX: false,
        paging:false,
        info: false,
    } );
}




