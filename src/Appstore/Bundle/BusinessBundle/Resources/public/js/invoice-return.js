$('.horizontal-form').submit(function(){
    $("button[type='submit']", this)
        .html("Please Wait...")
        .attr('disabled', 'disabled');
    return true;
});

$(document).on("keyup", ".input-number", function() {
    var sum = 0;
    var dataId = $(this).attr("data-id");
    var price = $('#price-'+dataId).val();
    var remainingQnt = parseInt($(this).attr("data-content"));
    var quantity = parseInt($('#quantity-'+dataId).val());
    if(quantity > remainingQnt ){
        alert('Return quantity must be remaining quantity equal or less');
        $(this).val(0);
        $("#subTotal-"+dataId).html(0);
        $("#sub-"+dataId).val(0);
        return false;
    }
    var bonusRemainingQnt = parseInt($(this).attr("data-value"));
    var bonusQty = parseInt($(this).val());
    if(bonusQty > bonusRemainingQnt ){
        alert('Return bonus quantity must be bonus remaining quantity equal or less');
        $(this).val(0);
        return false;
    }
    var amount = (price * quantity);
    $("#subTotal-"+dataId).html(amount);
    $("#sub-"+dataId).val(amount);
    $(".subTotal").each(function(){
        sum += +parseFloat($(this).val());
    });
    $("#total").html(sum);
    $("#grandTotal").val(sum);
});


$(document).on("keyup", "#payment , #adjustment", function() {
    var adjustment = parseInt($('#adjustment').val());
    var payment = parseInt($('#payment').val());
    var grandTotal = parseInt($('#grandTotal').val());
    sum = (adjustment+payment);
    if(grandTotal === sum){
        $("#submitBtn").attr("disabled", false);
    }else{
        $("#submitBtn").attr("disabled", true);
    }
});

$(document).on("click", ".approve", function() {
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $("#purchaseReturn").submit();
        }
    });
});