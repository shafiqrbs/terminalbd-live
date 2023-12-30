$('.horizontal-form').submit(function(){
    $("button[type='submit']", this)
        .html("Please Wait...")
        .attr('disabled', 'disabled');
    return true;
});

$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true
});

$( ".datePicker" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-10:+0"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0"
});

$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
});

$(".number , .amount, .numeric").inputFilter(function(value) {
    return /^-?\d*[.,]?\d*$/.test(value); });


$('.amount').change(function(){
    this.value = parseFloat(this.value).toFixed(2);
});

function financial(val) {
    var number =  Number.parseFloat(val).toFixed(2);
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}



function CommonJs(){


    $( ".date-picker" ).datepicker({
        dateFormat: "yy-mm-dd"
    });
    // Getter

    var dateFormat = $( ".date-picker" ).datepicker( "option", "dateFormat" );

    // Setter
    $( ".date-picker" ).datepicker( "option", "dateFormat", "yy-mm-dd" );


    $(document).on('change', '.transactionMethod', function() {

        var transactionMethod = $(this).val();
        if(transactionMethod == 2 ){
            $('.bankHide').show();
            $('.mobileHide').hide();
        }else if(transactionMethod == 3 ){
            $('.bankHide').hide();
            $('.mobileHide').show();
        }else{
            $('.bankHide').hide();
            $('.mobileHide').hide();
        }

    });


    $(document).on("change", ".customer-ledger", function() {

        var customer = $(this).val();
        $.get( Routing.generate('domain_customer_ledger'),{ customer:customer} )
            .done(function( data ) {
                $('#outstanding').html(data);
        });

    });

    $(document).on("change", ".select2Customer", function() {
        var customer = $(this).val();
        $.get( Routing.generate('domain_customer_sales_ledger'),{ customer:customer} )
            .done(function( data ) {
                $('#outstanding').html(data);
            });

    });


    $(document).on("change", ".vendor-ledger-medicine", function() {

        var vendor = $(this).val();
        $.get( Routing.generate('account_single_vendor_ledger'),{ vendor:vendor,'type':'medicine'} )
            .done(function( data ) {
                $('#outstanding').html(data);
            });
    });

    $(document).on("change", ".vendor-ledger-inventory", function() {

        var vendor = $(this).val();
        $.get( Routing.generate('account_single_vendor_ledger'),{ vendor:vendor,'type':'inventory'} )
            .done(function( data ) {
                $('#outstanding').html(data);
            });
    });

    $(document).on("change", ".vendor-ledger-business", function() {

        var vendor = $(this).val();
        $.get( Routing.generate('account_single_vendor_ledger'),{ vendor:vendor,'type':'business'} )
            .done(function( data ) {
                $('#outstanding').html(data);
            });
    });


     $(document).on("change", ".vendor-ledger-business", function() {

        var vendor = $(this).val();
        $.get( Routing.generate('account_single_vendor_ledger'),{ vendor:vendor,'type':'business'} )
            .done(function( data ) {
                $('#outstanding').html(data);
            });
    });

    $('.removeZero').click(function() {
        $(this).attr('value', '');
    });

    $(document).on("change", ".accountHead", function(event) {
        account = $(this).val();
        $.get( Routing.generate('account_sub_account_select',{ id:account}))
            .done(function( data ) {
                $(event.target).closest('.clone').find('.subAccountHead').html(data);
        });
    });

    $(document).on("change", ".userProfit", function(e) {
        account = $(this).val();
        $.get( Routing.generate('account_profit_withdrawal_stakeholder',{ id:account}))
            .done(function( data ) {
                $('.profit').html(data);
                $('.profit').val(data);
        });
    });

    $(document).on('keyup', ".debit", function() {
        var sum = 0;
        $(".debit").each(function(){
            crVal = ($(this).val() === "" ) ? 0 : Math.abs($(this).val());
            sum += +parseFloat(Math.abs(crVal));
        });
        $(".totalDebit").html(sum);
        $(".totalDebit").val(sum);

        dabeitVal = $("#totalDebit").val();
        creditVal = $("#totalCredit").val();
        if(creditVal ===  dabeitVal){
            $("#submitBtn").attr("disabled", false);
        }else{
            $("#submitBtn").attr("disabled", true);
        }


    });

    $(document).on('keyup', ".credit", function() {
        var sum = 0;
        $(".credit").each(function(){
            crVal = ($(this).val() === "" ) ? 0 : Math.abs($(this).val());
            sum += +parseFloat(Math.abs(crVal));
        });
        $(".totalCredit").html(sum);
        $(".totalCredit").val(sum);
        dabeitVal = $("#totalDebit").val();
        creditVal = $("#totalCredit").val();
        if(creditVal ===  dabeitVal){
            console.log(creditVal);
            $("#submitBtn").attr("disabled", false);
        }else{
            $("#submitBtn").attr("disabled", true);
        }
    });



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


}

