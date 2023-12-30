$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
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
                url: Routing.generate('dps_invoice_new'),
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                    formSubmit();
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});

function formSubmit() {

    $("#invoicePatientForm").validate({

        rules: {

            "dpsinvoice[customer][name]": {required: true},
            "dpsinvoice[customer][mobile]": {required: true},
            "dpsinvoice[customer][age]": {required: true},
            "dpsinvoice[customer][address]": {required: false},
            "dpsinvoice[customer][weight]": {required: false},
            "dpsinvoice[customer][bloodPressure]": {required: false},
            "dpsinvoice[customer][bloodGroup]": {required: false},
        },

        messages: {

            "dpsinvoice[customer][name]": "Enter patient name",
            "dpsinvoice[customer][mobile]": "Enter patient mobile no",
            "dpsinvoice[customer][age]": "Enter patient age",
        },
        tooltip_options: {
            "dpsinvoice[customer][name]": {placement: 'top', html: true},
            "dpsinvoice[customer][mobile]": {placement: 'top', html: true},
            "dpsinvoice[customer][age]": {placement: 'top', html: true},
        },

        submitHandler: function (form) {
            $.ajax({
                url         : $('form#invoicePatientForm').attr( 'action' ),
                type        : $('form#invoicePatientForm').attr( 'method' ),
                data        : new FormData($('form#invoicePatientForm')[0]),
                processData : false,
                contentType : false,
                beforeSend: function() {
                    $('.btn-ajax-loading').attr("disabled", true);
                },
                success: function(response){
                    $('.btn-ajax-loading').attr("disabled", false);
                    window.location.href = '/doctor-prescription/invoice/'+response+'/edit';
                }
            });
        }
    });
}

