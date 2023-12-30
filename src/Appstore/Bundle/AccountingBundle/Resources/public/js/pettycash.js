function AccountingPettyCashPage(){

    $(document).on("click", ".payment", function() {

        var id = $(this).attr("id");
        $('.cashPayment').show();
        $('#parent').val(id);
        var parentAmount =  $(this).attr("data-parent");
        $('#parentAmount').val(parentAmount);
        var receivedAmount =  $(this).attr("data-title");
        $('#receivedAmount').val(receivedAmount);

    })

    $(document).on("click", ".delete", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response ) {
                    $('#remove-' + id).remove();
                }
            },
        })

    })

    $(document).on("click", ".approve", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response ) {
                    location.reload();
                    //$('#action-' + id).append('<a title="Approve" href="javascript:" class="btn green mini" ><i class="icon-check"></i>&nbsp;Approved</a>');
                    //$('.approved-' + id).remove();
                }
            },
        })

    })

    $('#payment-button').click(function (e) {

        var number_regex = /^[0-9]+$/;
        var parent =  $('#parent').val();
        var accountHead =  $('#accountHead').val();
        var remark =  $('#remark').val();
        var payment =  $('#amount').val();
        var transactionMethod =  $('#transactionMethod').val();
        var accountBank =  $('#accountBank').val();
        var accountBkash =  $('#accountBkash').val();
        var amount = (payment != '') ? parseInt(payment) : 0;
        var parentAmount =  $('#parentAmount').val();
        var receivedAmount =  $('#receivedAmount').val();
        var receivedAmount = (receivedAmount != '') ? parseInt(receivedAmount) : 0;
        var currentReceive = (receivedAmount + amount);
        if( amount.length == 0) {

            $('#message').text("add receive amount"); //this segment displays the validation rule for selection
            $("#amount").focus();
            return false;

        }else if( parentAmount < currentReceive ){

            $('#message').text("Total receive amount more then payment amount dose not exist."); //this segment displays the validation rule for selection
            $("#amount").focus();
            return false;

        }else{

            $.ajax({
                url: Routing.generate('account_pettycash_payment_return'),
                type: 'POST',
                data:'parent='+parent+'&transactionMethod='+transactionMethod+'&remark='+remark+'&amount='+ amount+'&accountBank='+ accountBank+'&accountBkash='+ accountBkash,
                success: function (response) {
                    if (response == 'success'){
                        location.reload();
                    }
                },

            })
        }
        e.preventDefault();

    });



}

