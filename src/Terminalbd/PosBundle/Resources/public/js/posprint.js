$(document).on('change', '#branchItem', function() {

    var item = $('#branchItem').val();
    if(item == ''){
        return false;
    }
    $.ajax({
        url: Routing.generate('inventory_branch_sales_item_view',{'item':item}),
        type: 'GET',
        success: function(response) {
            $('#branchItemStock').html(response);
        },
    })

});

$(document).on("click", "#pos", function() {

    var paymentAmount = $('#paymentAmount').val();
    if(paymentAmount == ''){
        $('#static').modal();
        return false;
    }
    var url = $(this).attr("rel");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            jsPostPrint(response);
            setTimeout(pageRedirect(), 3000);
        }
    })
});

$(document).on("click", "#onlineSalesPos", function() {

    var url = $(this).attr("rel");
    $.ajax({
        url: Routing.generate('inventory_salesonline_posprint'),
        type: 'POST',
        data : $('.form-horizontal').serialize(),
        success: function (response) {
            jsPostPrint(response);
            setTimeout(pageOnlineSalesRedirect(), 3000);
        }
    })
});

$(document).on("click", ".onlinePosPrintIndividual", function() {

    $.get( $(this).attr("data-url") , function (response) {
        jsPostPrint(response);
    })
});

function pageRedirect() {
    window.location.href = "/inventory/sales/new";
}

function pageOnlineSalesRedirect() {
    window.location.href = "/inventory/sales-online/new";
}

function jsPostPrint(data) {

    if(typeof EasyPOSPrinter == 'undefined') {
        alert("Printer library not found");
        return;
    }
    EasyPOSPrinter.raw(data);
    EasyPOSPrinter.cut();
    EasyPOSPrinter.print(function(r, x){
        console.log(r)
    });

}

