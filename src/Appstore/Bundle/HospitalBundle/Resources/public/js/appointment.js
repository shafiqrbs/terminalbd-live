$(document).on('click', '.addAppointment', function() {

    $('.dialogModal_header').html('Patient Information');
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: Routing.generate('hms_doctor_visit_new'),
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                    appointmentFormSubmit();
                    $('.referredDoctor,.assignDoctor,.diseasesProfile,.specialization').select2();
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});

$(document).on('change', '.assignDoctor, .visitType', function() {
    var id = $("#appointment_invoice_assignDoctor").val();
    var visitType = $("#appointment_invoice_visitType").val();
    $.get(Routing.generate('hms_doctor_visit_amount',{id:id,'visitType':visitType}), function( data ){
        $('#appointment_invoice_payment').val(data);
        $('#visit').html(data);
    });
});


$('form#appointmentPatientForm').on('keypress', 'input,select,textarea', function (e) {

    if (e.which == 13) {

        e.preventDefault();
        switch (this.id) {

            case 'appointment_invoice_customer_name':
                $('#appointment_invoice_customer_mobile').focus();
                break;

            case 'appointment_invoice_customer_mobile':
                $('#appointment_invoice_customer_age').focus();
                break;

            case 'appointment_invoice_customer_age':
                $('#appointment_invoice_customer_height').focus();
                break;

            case 'appointment_invoice_customer_height':
                $('#appointment_invoice_customer_weight').focus();
                break;

            case 'appointment_invoice_customer_weight':
                $('#appointment_invoice_customer_bloodPressure').focus();
                break;

            case 'appointment_invoice_customer_bloodPressure':
                $('#appointment_invoice_customer_address').focus();
                break;

            case 'appointment_invoice_assignDoctor':
                $('#appointment_invoice_payment').focus();
                break;


            case 'appointment_invoice_customer_payment':
                $('#saveDiagnosticButton').trigger('click');
                break;
        }
    }
});
var form = $("#appointmentPatientForm").validate({

    rules: {

        "appointment_invoice_assignDoctor": {required: true},
        "appointment_invoice[customer][name]": {required: true},
        "appointment_invoice[customer][mobile]": {required: true, digits: true},
        "appointment_invoice[customer][age]": {required: true, digits: true},
        "appointment_invoice[customer][weight]": {required: false, digits: true},
        "appointment_invoice[customer][height]": {required: false, digits: true},
        "appointment_invoice[customer][bloodPressure]": {required: false},
        "appointment_invoice[department]": {required: false},
        "appointment_invoice[diseasesProfile]": {required: false},
        "appointment_invoice[payment]": {required: true, digits: true},
        "appointment_invoice[comment]": {required: false},
        "#appointment_invoice_followUpId": {required: false},
        "#appointment_invoice_referredDoctor": {required: false},
    },

    messages: {
        "appointment_invoice_assignDoctor": "Select Doctor",
        "appointment_invoice[customer][name]": "Enter patient name",
        "appointment_invoice[customer][mobile]": "Enter patient mobile no",
        "appointment_invoice[customer][age]": "Enter patient age",
        "appointment_invoice[payment]": "Enter payment amount, if payment are due input zero",
    },
    tooltip_options: {
        "appointment_invoice_assignDoctor": {placement: 'top', html: true},
        "appointment_invoice[customer][name]": {placement: 'top', html: true},
        "appointment_invoice[customer][mobile]": {placement: 'top', html: true},
        "appointment_invoice[customer][age]": {placement: 'top', html: true},
        "appointment_invoice[payment]": {placement: 'top', html: true},
        "appointment_invoice[department]": {placement: 'top', html: true},
    },
    submitHandler: function (form) {
        console.log(new FormData($('form#appointmentPatientForm')[0]));
        $.ajax({
            url         : $('form#appointmentPatientForm').attr( 'action' ),
            type        : $('form#appointmentPatientForm').attr( 'method' ),
            data        : new FormData($('form#appointmentPatientForm')[0]),
            processData : false,
            contentType : false,
            beforeSend: function() {
                $('.saveDiagnosticButton').attr('disabled', true);
            },
            success: function(response){

                $('form#appointmentPatientForm')[0].reset();
                $('.saveDiagnosticButton').attr("disabled", false);
                $('.subTotal, .initialGrandTotal, .due, .discountAmount, .initialDiscount').html('');
                $('#appointment_invoice_discount').val(0);
                $('#invoiceParticulars').hide();
                $("#appointment_invoice_assignDoctor").select2().select2("val","");
                $("#referredId").select2().select2("val","");
                window.open('/hms/invoice/'+response+'/appointment-print', '_blank');
            }
        });

    }
});

function appointmentFormSubmit() {

    $('#appointment_invoice_customer_name').focus().keypress(function () {
        $('#appointment_invoice_customer_name').css('textTransform', 'capitalize');
    });

    $('form#appointmentPatientForm').on('keypress', 'input,select,textarea', function (e) {

        if (e.which == 13) {

            e.preventDefault();
            switch (this.id) {

                case 'appointment_invoice_customer_name':
                    $('#appointment_invoice_customer_mobile').focus();
                    break;

                case 'appointment_invoice_customer_mobile':
                    $('#appointment_invoice_customer_age').focus();
                    break;

                case 'appointment_invoice_customer_age':
                    $('#appointment_invoice_customer_height').focus();
                    break;

                case 'appointment_invoice_customer_height':
                    $('#appointment_invoice_customer_weight').focus();
                    break;

                case 'appointment_invoice_customer_weight':
                    $('#appointment_invoice_customer_bloodPressure').focus();
                    break;

                case 'appointment_invoice_customer_bloodPressure':
                    $("#appointment_invoice_assignDoctor").select2().select2("val","").select2('open');
                    break;

                case 'appointment_invoice_assignDoctor':
                    $('#appointment_invoice_payment').focus();
                    break;


                case 'appointment_invoice_customer_payment':
                    $('#saveDiagnosticButton').trigger('click');
                    break;
            }
        }
    });
    var form = $("#appointmentPatientForm").validate({

        rules: {

            "appointment_invoice[customer][name]": {required: true},
            "appointment_invoice[customer][mobile]": {required: true, digits: true},
            "appointment_invoice[customer][age]": {required: true, digits: true},
            "appointment_invoice[customer][weight]": {required: false, digits: true},
            "appointment_invoice[customer][height]": {required: false, digits: true},
            "appointment_invoice[customer][bloodPressure]": {required: false},
            "appointment_invoice[customer][address]": {required: false},
            "appointment_invoice[assignDoctor]": {required: true},
            "appointment_invoice[department]": {required: false},
            "appointment_invoice[payment]": {required: false, digits: true},
            "appointment_invoice[comment]": {required: false},
            "appointment_invoice[smsAlert]": {required: false},
            "appointment_invoice[isConfirm]": {required: false},
            "appointment_invoice[appointmentDate]": {required: false},
        },

        messages: {
            "appointment_invoice[assignDoctor]": "Select assign doctor",
            "appointment_invoice[department]": "Select Department",
            "appointment_invoice[customer][name]": "Enter patient name",
            "appointment_invoice[customer][mobile]": "Enter patient mobile no",
            "appointment_invoice[customer][age]": "Enter patient age",
            "appointment_invoice[payment]": "Enter payment amount, if payment are due input zero",
        },
        tooltip_options: {
            "appointment_invoice[customer][name]": {placement: 'top', html: true},
            "appointment_invoice[customer][mobile]": {placement: 'top', html: true},
            "appointment_invoice[customer][age]": {placement: 'top', html: true},
            "appointment_invoice[payment]": {placement: 'top', html: true},
            "appointment_invoice[assignDoctor]": {placement: 'top', html: true},
            "appointment_invoice[department]": {placement: 'top', html: true},
        },
        submitHandler: function (form) {
            $.ajax({
                url         : $('form#appointmentPatientForm').attr( 'action' ),
                type        : $('form#appointmentPatientForm').attr( 'method' ),
                data        : new FormData($('form#appointmentPatientForm')[0]),
                processData : false,
                contentType : false,
                beforeSend: function() {
                    $('#saveDiagnosticButton').html("Please Wait...").attr('disabled', 'disabled');
                },
                success: function(response){
                    obj = JSON.parse(response);
                    if(obj['isConfirm'] == 1){
                        $('#print-link').html(obj['prescriptionLink']);
                    }else{
                        $('#print-link').html('');
                    }
                    $('#patientId').val(obj['patientId']);
                    $('form#appointmentPatientForm')[0].reset();
                    $('#saveDiagnosticButton').html("<i class='icon-save'></i> Save").attr("disabled", false);
                    $('.subTotal, .initialGrandTotal, .due, .discountAmount, .initialDiscount').html('');
                    $('#appointment_invoice_discount').val(0);
                    $('#invoiceParticulars').hide();
                    $("#appointment_invoice_assignDoctor").select2().select2("val","");
                    $("#referredId").select2().select2("val","");
                    if(obj['print'] === "print") {
                        window.open(obj['printLink'], '_blank');
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

    $(document).on('change', '#patient', function() {

        var id = $(this).val();
        $.ajax({
            url: Routing.generate('domain_patient_information',{'id':id}),
            type: 'GET',
            success: function (response) {
                console.log(response);
                obj = JSON.parse(response);
                $('#patientId').val(obj['patientId']);
                $('#appointment_invoice_customer_name').val(obj['name']);
                $('#appointment_invoice_customer_mobile').val(obj['mobile']);
                $('#appointment_invoice_customer_age').val(obj['age']);
                $('#appointment_invoice_customer_ageType').val(obj['ageType']);
                $('#appointment_invoice_customer_gender').val(obj['gender']);
                $('#appointment_invoice_customer_height').val(obj['height']);
                $('#appointment_invoice_customer_weight').val(obj['weight']);
                $('#appointment_invoice_customer_bloodPressure').val(obj['bloodPressure']);
                $('#appointment_invoice_customer_address').val(obj['address']);
            }
        })

    });

}



