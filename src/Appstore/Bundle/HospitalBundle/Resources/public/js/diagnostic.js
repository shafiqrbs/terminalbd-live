$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
});

$(".select2Invoice").select2({

    ajax: {
        url: Routing.generate('hms_patient_invoice_search'),
        dataType: 'json',
        delay: 250,
        data: function (params, page) {
            return {
                q: params,
                page_limit: 100
            };
        },
        results: function (data, page) {
            return {
                results: data
            };
        },
        cache: true
    },
    escapeMarkup: function (m) {
        return m;
    },
    formatResult: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    formatSelection: function (item) {
        return item.text
    }, // omitted for brevity, see the source of this page
    allowClear: true,
    minimumInputLength: 1
});


$(document).on('change', '#select2Invoice, #barcode2Invoice', function() {

    var invoice = $(this).val();
    if(invoice === "" || invoice === "NaN"){
        return false;
    }
    $('.dialogModal_header').html('Patient Information');
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: Routing.generate('hms_patient_invoice_details',{'invoice':invoice}),
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                    EditableWithOutReloadInit();
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});

$(document).on('click', '.addPatient', function() {

    $('.dialogModal_header').html('Patient Information');
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: Routing.generate('hms_invoice_temporary_new'),
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                    formSubmit();
                    $('#particular').select2();
                    $('.marketingExecutive').select2();
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});

$(document).on("click", ".saveButton", function() {

    var formData = new FormData($('form#invoiceForm')[0]); // Create an arbitrary FormData instance
    var url = $('form#invoiceForm').attr('action'); // Create an arbitrary FormData instance
    $.ajax({
        url:url ,
        type: 'POST',
        processData: false,
        contentType: false,
        data:formData,
        success: function(response){

        }
    });

});

function formSubmit() {

    $('#appstore_bundle_hospitalbundle_invoice_customer_name').focus().keypress(function () {
        $('#appstore_bundle_hospitalbundle_invoice_customer_name').css('textTransform', 'capitalize');
    });

    $('form#invoicePatientForm').on('keypress', 'input,select,textarea', function (e) {

        if (e.which == 13) {
            e.preventDefault();
            switch (this.id) {

                case 'appstore_bundle_hospitalbundle_invoice_customer_name':
                    $('#appstore_bundle_hospitalbundle_invoice_customer_mobile').focus();
                    break;

                case 'appstore_bundle_hospitalbundle_invoice_customer_mobile':
                    $('#appstore_bundle_hospitalbundle_invoice_customer_age').focus();
                    break;

                case 'appstore_bundle_hospitalbundle_invoice_customer_age':
                    $('#appstore_bundle_hospitalbundle_invoice_customer_address').focus();
                    break;

                case 'appstore_bundle_hospitalbundle_invoice_customer_address':
                    $("#particular").select2().select2("val","").select2('open');
                    break;

                case 'quantity':
                    $('#temporaryParticular').trigger('click');
                    break;

                case 'appstore_bundle_hospitalbundle_invoice_discountCalculation':
                    $('#appstore_bundle_hospitalbundle_invoice_payment').focus();
                    break;


                case 'appstore_bundle_hospitalbundle_invoice_payment':
                    $("#appstore_bundle_hospitalbundle_invoice_assignDoctor").select2().select2("val","").select2('open');
                    break;


                case 'appstore_bundle_hospitalbundle_invoice_assignDoctor':
                    $("#referredId").select2().select2("val","").select2('open');
                    break;


                case 'referredId':
                    $('#saveDiagnosticButton').trigger('click');
                    break;
            }
        }
    });

    $("form#invoicePatientForm").on('click', '.discount-indicator', function() {
        $( "#discount-box" ).slideToggle( "slow" );
    });

    $("form#invoicePatientForm").on('click', '.addCustomer', function() {
        $( ".customer" ).slideToggle( "slow" );
    });

    $("form#invoicePatientForm").on('click', '.addDoctor', function() {
        $( "#consultant" ).slideToggle( "slow" );
    });

    $("form#invoicePatientForm").on('click', '.referredId', function() {
        $( "#referredDoctor" ).slideToggle( "slow" );
    });

    $(document).on('change', '#particular', function() {
        var url = $(this).val();
        if(url === ''){
            alert('You have to add particulars from drop down and this not service item');
            return false;
        }
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                $('#particularId').val(obj['particularId']);
                $('#quantity').val(obj['quantity']).focus();
                $('#price').val(obj['price']);
                $('#instruction').html(obj['instruction']);
                $('#addParticular').attr("disabled", false);
            }
        })
    });

    $(document).on('change', '#appstore_bundle_hospitalbundle_invoice_particulars', function() {
        var id = $(this).val();
        if(id === ''){
            alert('You have to add particulars from drop down and this not service item');
            return false;
        }
        $.ajax({
            url: Routing.generate('hms_invoice_temporary_particular_search_add',{'id':id}),
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                $('#invoiceParticulars').show().html(obj['invoiceParticulars']);
                $('.subTotal').html(obj['subTotal']);
                $('.initialGrandTotal').html(obj['initialGrandTotal']);
                $('#initialDue').val(obj['initialGrandTotal']);
                $('.initialDiscount').html('');
                $('#appstore_bundle_hospitalbundle_invoice_discountCalculation').val('').attr( "placeholder", 'Discount' );
                $('#appstore_bundle_hospitalbundle_invoice_discount').val(0);
                $("#appstore_bundle_hospitalbundle_invoice_particulars").select2("val", "").select2('open');
                if(obj['initialGrandTotal'] > 0 ){
                    $('#saveDiagnosticButton').attr("disabled", false);
                }else{
                    $('#saveDiagnosticButton').attr("disabled", true);
                }
            }
        })
    });



    $(document).on('click', '#temporaryParticular', function() {

        var particularId = $('#particularId').val();
        var quantity = parseInt($('#quantity').val());
        var price = parseInt($('#price').val());
        var url = $('#temporaryParticular').attr('data-url');
        if(particularId === ''){
            $("#particular").select2('open');
            return false;
        }
        $.ajax({
            url: url,
            type: 'POST',
            data: 'particularId='+particularId+'&quantity='+quantity+'&price='+price,
            success: function (response) {
                obj = JSON.parse(response);
                $('#invoiceParticulars').show().html(obj['invoiceParticulars']);
                $('.subTotal').html(obj['subTotal']);
                $('.initialGrandTotal').html(obj['initialGrandTotal']);
                $('#initialDue').val(obj['initialGrandTotal']);
                $('.initialDiscount').html('');
                $('#appstore_bundle_hospitalbundle_invoice_discountCalculation').val('').attr( "placeholder", 'Discount' );
                $('#appstore_bundle_hospitalbundle_invoice_discount').val(0);
                $('#instruction').html('');
                $("#particular").select2().select2("val","").select2('open');
                $('#price').val('');
                $('#quantity').val('1');
                $('#particularId').val('');
                $('#addParticular').attr("disabled", true);
                if(obj['initialGrandTotal'] > 0 ){
                    $('#saveDiagnosticButton').attr("disabled", false);
                }else{
                    $('#saveDiagnosticButton').attr("disabled", true);
                }
            }
        })
    });

    $(document).on('change', '#discountType , #appstore_bundle_hospitalbundle_invoice_discountCalculation', function() {

        var discountType = $('#discountType').val();
        var discount = parseInt($('#appstore_bundle_hospitalbundle_invoice_discountCalculation').val());
        if(discount === "NaN"){
            return false;
        }
        $.ajax({
            url: Routing.generate('hms_invoice_temporary_discount_update'),
            type: 'POST',
            data:'discount=' + discount+'&discountType='+discountType,
            success: function(response) {
                obj = JSON.parse(response);
                $('.subTotal').html(obj['subTotal']);
                $('.initialGrandTotal').html(obj['initialGrandTotal']);
                $('.initialDiscount').html(obj['initialDiscount']);
                $('#appstore_bundle_hospitalbundle_invoice_discount').val(obj['initialDiscount']);
                $('#initialDue').val(obj['initialGrandTotal']);

            }

        })
    });

    $(document).on("click", ".initialParticularDelete , .particularDelete", function() {

        var id = $(this).attr("data-id");
        var url = $(this).attr("data-url");
        $('#confirm-content').confirmModal({
            topOffset: 0,
            top: '25%',
            onOkBut: function(event, el) {
                $.get(url, function( data ) {
                    obj = JSON.parse(data);
                    $('.subTotal').html(obj['subTotal']);
                    $('.initialGrandTotal').html(obj['initialGrandTotal']);
                    $('#initialDue').val(obj['initialGrandTotal']);
                    $('.initialDiscount').html('');
                    $('#appstore_bundle_hospitalbundle_invoice_discountCalculation').val('').attr( "placeholder", 'Discount' );
                    $('#appstore_bundle_hospitalbundle_invoice_discount').val(0);
                    $('#remove-'+id).hide();
                    if(obj['initialGrandTotal'] > 0 ){
                        $('#saveDiagnosticButton').attr("disabled", false);
                    }else{
                        $('#saveDiagnosticButton').attr("disabled", true);
                    }
                });
            }
        });
    });

    $(document).on('keyup', '.payment', function() {

        var payment  = parseInt($('#appstore_bundle_hospitalbundle_invoice_payment').val()  != '' ? $('#appstore_bundle_hospitalbundle_invoice_payment').val() : 0 );
        var due  = parseInt($('#initialDue').val()  != '' ? $('#initialDue').val() : 0 );
        var dueAmount = (due - payment);
        if(dueAmount > 0){
            $('#balance').html('Due Tk.');
            $('#dueable').html(dueAmount);
        }else{
            var balance =  payment - due ;
            $('#balance').html('Return Tk.');
            $('#dueable').html(balance);
        }

    });

    var form = $("#invoicePatientForm").validate({

        rules: {

            "appstore_bundle_hospitalbundle_invoice[customer][name]": {required: true},
            "appstore_bundle_hospitalbundle_invoice[customer][mobile]": {required: true, digits: true},
            "appstore_bundle_hospitalbundle_invoice[customer][age]": {required: true, digits: true},
            "appstore_bundle_hospitalbundle_invoice[discountCalculation]": {required: false, digits: true},
            "appstore_bundle_hospitalbundle_invoice[payment]": {required: true, digits: true},
            "appstore_bundle_hospitalbundle_invoice[customer][address]": {required: false},
            "appstore_bundle_hospitalbundle_invoice[customer][location]": {required: false},
            "appstore_bundle_hospitalbundle_invoice[referredDoctor][name]": {required: false},
            "appstore_bundle_hospitalbundle_invoice[referredDoctor][mobile]": {required: false},
            "appstore_bundle_hospitalbundle_invoice[comment]": {required: false},
            "customerId": {required: false},
        },

        messages: {
            "appstore_bundle_hospitalbundle_invoice[customer][name]": "Enter patient name",
            "appstore_bundle_hospitalbundle_invoice[customer][mobile]": "Enter patient mobile no",
            "appstore_bundle_hospitalbundle_invoice[customer][age]": "Enter patient age",
            "appstore_bundle_hospitalbundle_invoice[payment]": "Enter payment amount, if payment are due input zero",
        },
        tooltip_options: {
            "appstore_bundle_hospitalbundle_invoice[customer][name]": {placement: 'top', html: true},
            "appstore_bundle_hospitalbundle_invoice[customer][mobile]": {placement: 'top', html: true},
            "appstore_bundle_hospitalbundle_invoice[customer][age]": {placement: 'top', html: true},
            "appstore_bundle_hospitalbundle_invoice[payment]": {placement: 'top', html: true},
        },
        submitHandler: function (form) {
            $.ajax({
                url         : $('form#invoicePatientForm').attr( 'action' ),
                type        : $('form#invoicePatientForm').attr( 'method' ),
                data        : new FormData($('form#invoicePatientForm')[0]),
                processData : false,
                contentType : false,
                beforeSend: function() {
                    $('#saveDiagnosticButton').html("Please Wait...").attr('disabled', 'disabled');
                },
                success: function(response){
                    obj = JSON.parse(response);
                    $('#consultant,#referredDoctor,#discount-box').hide();
                    $(".consultant , .referred").select2("val", "");
                    $('form#invoicePatientForm')[0].reset();
                    $('#saveDiagnosticButton').html("<i class='icon-save'></i> Save").attr('disabled', 'disabled');
                    $('.subTotal, .initialGrandTotal, .due, .discountAmount, .initialDiscount').html('');
                    $('#appstore_bundle_hospitalbundle_invoice_discount').val(0);
                    $('#invoiceParticulars').hide();
                    if(obj['process'] === "In-progress"){
                        window.open('/hms/invoice/'+obj['entity']+'/print', '_blank');
                    }
                }
            });

        }
    });

    $(".invoiceSearch").select2({

        ajax: {
            url: Routing.generate('domain_patient_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    q: params,
                    page_limit: 100
                };
            },
            results: function (data, page) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (m) {
            return m;
        },
        formatResult: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        formatSelection: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        allowClear: true,
        minimumInputLength: 1
    });

    $(".referred").select2({
        ajax: {
            url: Routing.generate('hms_patient_select2_referred_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    q: params,
                    page_limit: 100
                };
            },
            results: function (data, page) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (m) {
            return m;
        },
        formatResult: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        formatSelection: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        allowClear: true,
        minimumInputLength: 1
    });

    $(".requested2User").select2({
        ajax: {
            url: Routing.generate('domain_user_profilename_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    q: params,
                    page_limit: 100
                };
            },
            results: function (data, page) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (m) {
            return m;
        },
        formatResult: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        formatSelection: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        allowClear: true,
        minimumInputLength: 1
    });

    $(".consultant").select2({

        ajax: {
            url: Routing.generate('hms_patient_select2_doctor_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    q: params,
                    page_limit: 100
                };
            },
            results: function (data, page) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (m) {
            return m;
        },
        formatResult: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        formatSelection: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        allowClear: true,
        minimumInputLength: 1
    });

    $(".particulars").select2({
        ajax: {
            url: Routing.generate('hms_patient_select2_particular_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    q: params,
                    page_limit: 100
                };
            },
            results: function (data, page) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (m) {
            return m;
        },
        formatResult: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        formatSelection: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        allowClear: true,
        minimumInputLength: 1
    });



    $(document).on('change', '#patient', function() {

        var id = $(this).val();
        $.ajax({
            url: Routing.generate('domain_patient_information',{'id':id}),
            type: 'GET',
            success: function (response) {
                console.log(response);
                obj = JSON.parse(response);
                $('#customerId').val(obj['patientId']);
                $('#appstore_bundle_hospitalbundle_invoice_customer_name').val(obj['name']);
                $('#appstore_bundle_hospitalbundle_invoice_customer_mobile').val(obj['mobile']);
                $('#appstore_bundle_hospitalbundle_invoice_customer_age').val(obj['age']);
                $('#appstore_bundle_hospitalbundle_invoice_customer_ageType').val(obj['ageType']);
                $('#appstore_bundle_hospitalbundle_invoice_customer_gender').val(obj['gender']);
                $('#appstore_bundle_hospitalbundle_invoice_customer_address').val(obj['address']);
            }
        })

    });

    $(".select2Admission").select2({

        ajax: {
            url: Routing.generate('hms_patient_admission_invoice_search'),
            dataType: 'json',
            delay: 250,
            data: function (params, page) {
                return {
                    q: params,
                    page_limit: 100
                };
            },
            results: function (data, page) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (m) {
            return m;
        },
        formatResult: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        formatSelection: function (item) {
            return item.text
        }, // omitted for brevity, see the source of this page
        allowClear: true,
        minimumInputLength: 1
    });

    $(document).on('change', '#admissionId', function() {

        var invoice = $(this).val();
        $.ajax({
            url: Routing.generate('hms_patient_info',{'invoice':invoice}),
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                $('#customerId').val(obj['patientId']);
                $('#appstore_bundle_hospitalbundle_invoice_customer_name').val(obj['name']);
                $('#appstore_bundle_hospitalbundle_invoice_customer_mobile').val(obj['mobile']);
                $('#appstore_bundle_hospitalbundle_invoice_customer_age').val(obj['age']);
                $('#appstore_bundle_hospitalbundle_invoice_customer_ageType').val(obj['ageType']);
                $('#appstore_bundle_hospitalbundle_invoice_customer_gender').val(obj['gender']);
                $('#appstore_bundle_hospitalbundle_invoice_customer_address').val(obj['address']);
            }
        })

    });

}



