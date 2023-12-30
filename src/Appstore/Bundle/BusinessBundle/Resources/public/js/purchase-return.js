
$(document).on("keyup", ".input-number", function() {

    var sum = 0;
    var qty = 0;
    var dataId = $(this).attr("data-id");
    var price = $(this).attr("data-value");
    var remainingQnt = parseInt($(this).attr("data-content"));
    var quantity = parseInt($(this).val());
    if(quantity > remainingQnt ){
        alert('Return quantity must be remaining quantity equal or less');
        $(this).val(0);
        $("#subTotal-"+dataId).html(0);
        $("#sub-"+dataId).val(0);
        return false;
    }
    var amount = (price * quantity);
    $("#subTotal-"+dataId).html(amount);
    $("#sub-"+dataId).val(amount);
    $(".subTotal").each(function(){
        sum += +parseFloat($(this).val());
    });

    $(".quantity").each(function(){
        qty += +parseFloat($(this).val());
    });
    $("#total").html(sum);
    $("#grandTotal").val(sum);
    $("#totalQTY").html(qty);
    if(sum > 0){
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