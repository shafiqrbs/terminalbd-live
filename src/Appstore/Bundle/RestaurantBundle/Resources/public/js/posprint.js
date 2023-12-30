$(document).on("click", "#kitchenBtn", function() {
    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( response ) {
                jsPostPrint(response);
                setTimeout(pageRedirect(),3000);
            });
        }
    });
});

$(document).on("click", ".paymentReceive", function() {
    var url = $(this).attr('data-url');
    var id = $(this).attr('data-id');
    var process = $(this).attr('data-value');
    $.get(url, function( response ) {
        if(process === 'pos-print'){
            jsPostPrint(response);
        }
        if(process === 'delivery-print'){
           // $('#print-area').html(response).kinziPrint();
            htmlPrint(response);
        }
    });
});

function htmlPrint(response) {
    w = window.open(window.location.href,"_blank");
    w.document.open();
    w.document.write(response);
    w.window.print();
}

function pageRedirect() {
    window.location.href = "/restaurant/invoice/new";
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

