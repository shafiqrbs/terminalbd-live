$(document).on("click", ".purchaseItemDelete", function() {

    var url = $(this).attr("rel");
    var id = $(this).attr("id");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            if ('success' == response) {
              location.reload();
            }
        },
    });
});


$(document).on("click", ".addPurchase", function() {

    var totalQnt =  $('#totalQnt').val();

    var quantity = 0;
    $("input[name='quantity[]']").each(function (index, element) {
        quantity = quantity + parseFloat(($(element).val() != '') ?  parseFloat($(element).val()) : 0);
    });

    var itemquantity = [];
    $("input[name='quantity[]']").each(function() {
        var value = $(this).val();
        if (value) {
            itemquantity.push(value);
        }
    });

    if(quantity != totalQnt ){
        $('#message').text('Not equal total quantity & item base total quantity');
        $('.quantity').focus();
        return false;
    }else if(itemquantity.length === 0 ){
        $('#message').text('Item quantity field values are blank');
        $('.quantity').focus();
        return false;
    }else{
        $(".addPurchase").attr("disabled", true);
        $('#purchaseBtn').removeClass('addPurchase');
        $('.horizontal-form').submit();
    }

});

$('#purchaseItem').on("click", ".delete", function() {
    var url = $(this).attr("rel");
    var id = $(this).attr("id");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            if ('success' == response) {
                $('#remove-' + id).hide();
            }
        },
    })
})


var count = 0;
$('.addmore').click(function(){

    var $el = $(this);
    $vendor_id = $el.data('ref-id');
    var $cloneBlock = $('#clone-block-'+ $vendor_id);

    var $clone = $cloneBlock.find('.clone:eq(0)').clone();
    $clone.find('[id]').each(function(){this.id+='someotherpart'});
    $clone.find(':text,textarea' ).val("");
    $clone.attr('id', "added"+(++count));
    $clone.find('.remove').removeClass('hidden');
    $cloneBlock.append($clone);
    $('.numeric').numeric();
});

$('form.addPurchase').on('click', '.remove', function(){
    $(this).closest('.clone').remove();
});

