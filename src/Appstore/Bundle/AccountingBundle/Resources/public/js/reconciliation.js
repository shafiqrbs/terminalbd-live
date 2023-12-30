function AccountingApproveProcess(){}

$('.horizontal-form').submit(function(){
    $("button[type='submit']", this)
        .html("Please Wait...")
        .attr('disabled', 'disabled');
    return true;
});

$(document).on("click", ".delete", function() {
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url).done(function( data ) {
                location.reload();
            });
        }
    });
});

$(document).on("click", "#cash-reconciliation", function() {
    var particular = $('#particular').val();
    var amount = $('#amount').val();
    var method = $('#method').val();
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url,{particular:particular,amount:amount,method:method}).done(function( data ) {
               /* location.reload();*/
            });
        }
    });
});


$('.amount,.updateNoteAmount').on('click', function(event) {
    $(this).val('');
});

$(document).on('keyup', ".amount", function() {
    var sum = 0;
    $(".amount").each(function(){
        sum += +parseFloat($(this).val());
    });
    $("#total").html(sum);
});

$(document).on('keyup', ".bankAmount", function() {
    var sum = 0;
    $(".bankAmount").each(function(){
        sum += +parseFloat($(this).val());
    });
    $("#bankTotal").html(sum);
});
$(document).on('keyup', ".mobileAmount", function() {
    var sum = 0;
    $(".mobileAmount").each(function(){
        sum += +parseFloat($(this).val());
    });
    $("#mobileTotal").html(sum);
});

$(document).on("change", ".updateAmount", function() {
    var amount = $(this).val();
    var id = $(this).attr("data-id");
    var metaKey = $('#metaKey-'+id).val();
    var url = $(this).attr("data-url");
    $.get(url,{amount:amount,note:0,metaKey:metaKey}).done(function( data ) {
        setTimeout(pageReload, 1000);
        location.reload();
    });
});
$(document).on("change", ".updateNoteAmount", function() {
    var note = $(this).val();
    var id = $(this).attr("data-content");
    var noteType = $(this).attr("data-id");
    var amount = (note * noteType);
    $('#update-amount-'+ id).val(amount);
    var url = $(this).attr("data-url");
    $.get(url,{amount:amount,note:note,metaKey:''}).done(function( data ) {
        setTimeout(pageReload, 1000);
        location.reload();
    });

});

function pageReload() {
    var sum = 0;
    $(".amount").each(function(){
        sum += +parseFloat($(this).val());
    });
    $("#total").html(sum);
}




