/**
 * Created by rbs on 2/9/16.
 */

var PaymentSalary = function() {


    $(document).on('change', '#appstore_bundle_accountingBundle_paymentsalary_salarySetting', function() {

        var id = $('#appstore_bundle_accountingBundle_paymentsalary_salarySetting').val();
        if(id === ''){
            return false;
        }
        $.ajax({
            url: Routing.generate('account_salarysetting_salaryAmount'),
            type: 'POST',
            data:'id='+id,
            success: function(response) {
                   $('#salaryAmount').val(response);
                   $('#totalAmount').val(response);
                   $('#appstore_bundle_accountingBundle_paymentsalary_totalAmount').val(response);
            },
        })
    })

    $(document).on('change', ' #appstore_bundle_accountingBundle_paymentsalary_otherAmount', function() {

        var salary     = parseInt($('#salaryAmount').val()  != '' ? $('#salaryAmount').val() : 0 );
        var adjustment    = parseInt($('#appstore_bundle_accountingBundle_paymentsalary_adjustmentAmount').val() != '' ? $('#appstore_bundle_accountingBundle_paymentsalary_adjustmentAmount').val() : 0);
        var netAmount   = (salary - adjustment);
        $('#totalAmount').val(netAmount);
        $('#appstore_bundle_accountingBundle_paymentsalary_totalAmount').val(netAmount);

    });

    $(document).on("click", ".payment", function() {

        var paidAmount      = parseInt($('#appstore_bundle_accountingBundle_paymentsalary_paidAmount').val()  != '' ? $('#appstore_bundle_accountingBundle_paymentsalary_paidAmount').val() : 0 );
        var salary          = parseInt($('#salaryAmount').val()  != '' ? $('#salaryAmount').val() : 0 );
        var adjustment      = parseInt($('#appstore_bundle_accountingBundle_paymentsalary_adjustmentAmount').val() != '' ? $('#appstore_bundle_accountingBundle_paymentsalary_adjustmentAmount').val() : 0);
        var netAmount       = (salary - adjustment);
        if(netAmount < paidAmount){
            alert('This is wrong payment amount: '+paidAmount+' Tk.');
            return false;
        }
    });

    $('#payment').on("click", ".delete", function() {

        var url = $(this).attr("data-url");
        var id = $(this).attr("data-id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' == response ) {
                    $('#remove-' + id).hide();
                }
            },
        })
    });



}

