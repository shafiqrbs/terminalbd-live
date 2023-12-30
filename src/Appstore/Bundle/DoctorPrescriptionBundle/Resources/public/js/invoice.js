$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
});

var bindDatePicker = function(element) {
    $(element).datetimepicker({
        showOn: "button",
        buttonImage: "/img/calendar_icon.png",
        buttonImageOnly: true,
        dateFormat: 'mm/dd/yy',
        timeFormat: 'hh:mm tt',
        stepMinute: 1,
        onClose: datePickerClose
    });
};

function datePickerReload() {
    $( ".date-picker" ).datepicker({
        dateFormat: "dd-mm-yy"
    });
}

$("[id^=startPicker]").each(function() {
    bindDatePicker(this);
});

$(document).on("click", ".sms-confirm", function() {
    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url);
        }
    });
});

$( "#name" ).autocomplete({

    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('domain_customer_auto_name_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 2,
    select: function( event, ui ) {}

});

$( ".autoProcedure" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('dms_invoice_procedure_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 1,
    select: function( event, ui ) {
    }
});

/*$( ".autoDiseases" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('dms_invoice_procedure_diseases_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 1,
    select: function( event, ui ) {
    }
});*/

function split( val ) {
    return val.split( /,\s*/ );
}
function extractLast( term ) {
    return split( term ).pop();
}

$( ".autoDiseases" )
// don't navigate away from the field on tab when selecting an item
    .on( "keydown", function( event ) {
        if ( event.keyCode === $.ui.keyCode.TAB &&
            $( this ).autocomplete( "instance" ).menu.active ) {
            event.preventDefault();
        }
    })
    .autocomplete({
        source: function( request, response ) {
            $.getJSON( Routing.generate('dms_invoice_procedure_diseases_search') , {
                term: extractLast( request.term )
            }, response );
        },
        search: function() {
            // custom minLength
            var term = extractLast( this.value );
            if ( term.length < 2 ) {
                return false;
            }
        },
        focus: function() {
            // prevent value inserted on focus
            return false;
        },
        select: function( event, ui ) {
            var terms = split( this.value );
            // remove the current input
            terms.pop();
            // add the selected item
            terms.push( ui.item.value );
            // add placeholder to get the comma-and-space at the end
            terms.push( "" );
            this.value = terms.join( ", " );
            return false;
        }
    });

$( ".investigation" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('dms_invoice_investigation_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 1,
    select: function( event, ui ) {
    }
});

$( ".autoMetaValue" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('dms_invoice_auto_particular_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 1,
    select: function( event, ui ) {
    }
});

$('.checkboxes').checkradios({
    checkbox: {
        iconClass:'fa fa-window-close'
    }

});
$(document).on("click", "#patientOverview", function() {
    var url = $(this).attr('data-url');
    $.ajax({
        url :url,
        beforeSend: function(){
            $('.loader-double').fadeIn(1000).addClass('is-active');
        },
        complete: function(){
            $('.loader-double').fadeIn(1000).removeClass('is-active');
        },
        success:  function (data) {
            $("#patientLoad").html(data);
        }
    });
});



$( "#mobile" ).autocomplete({

    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('domain_customer_auto_mobile_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 2,
    select: function( event, ui ) {}

});



$(document).on('change', '.transactionMethod', function() {

    var paymentMethod = $(this).val();

    if( paymentMethod == 2){
        $('#cartMethod').show();
        $('#bkashMethod').hide();
    }else if( paymentMethod == 3){
        $('#bkashMethod').show();
        $('#cartMethod').hide();
    }else{
        $('#cartMethod').hide();
        $('#bkashMethod').hide();
    }

});

$(document).on( "click", ".patientShow", function(e){
    $('#updatePatient').slideToggle(2000);
    $("span", this).toggleClass("fa fa-angle-double-up fa fa-angle-double-down");
});

$(document).on( "click", ".receivePayment", function(e){
    $("#showPayment").slideToggle(1000);
    $("span", this).toggleClass("fa-minus fa-money");
});

$(document).on( "change", "#invoiceParticular", function(e){

    var price = $(this).val();
    $('#appstore_bundle_dpsinvoice_payment').val(price);
});

$(document).on('click', '.addProcedure', function() {

    var dataTab    = $(this).attr('data-tab');
    var procedure =  $('#'+dataTab).find('#procedure').val();
    var diseases =  $('#'+dataTab).find('#diseases').val();
    if(procedure == ''){
        alert('You have to add procedure text');
        $('#'+dataTab).find('#procedure').focus();
        return false;
    }
    var url     = $(this).attr('data-url');
    var showDiv    = $(this).attr('data-id');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'procedure='+procedure+'&diseases='+diseases,
        success: function (response) {
            $('#'+dataTab).find('#procedure-'+showDiv).html(response);
            $('#'+dataTab).find('#procedure').val('');
            $('#'+dataTab).find('#diseases').val('');
        }
    });
});

$(document).on('click', '.addInvestigation', function() {

    var dataTab    = $(this).attr('data-tab');
    var procedure =  $('#'+dataTab).find('#investigation').val();
    if(procedure == ''){
        alert('You have to add procedure text');
        $('#'+dataTab).find('#investigation').focus();
        return false;
    }

    var file =  $('#'+dataTab).find('#file').val();
    if(file == ''){
        alert('You have to add file');
        $('#'+dataTab).find('#file').focus();
        return false;
    }

    var url = $('form#invoiceForm').attr('action');
    var showDiv    = $(this).attr('data-id');
    var formData = new FormData($('form#invoiceForm')[0]);
    $.ajax({
        url:url ,
        type: 'POST',
        beforeSend: function() {
            $('.addInvestigation').show().addClass('btn-ajax-loading').fadeIn(3000);
            $('.btn-ajax-loading').attr("disabled", true);
        },
        processData: false,
        contentType: false,
        data:formData,
        success: function(response){
            $('#'+dataTab).find('#procedure-'+showDiv).html(response);
            $('#'+dataTab).find('#investigation').val('');
            $('#'+dataTab).find('#file').val('');
            $('.btn-ajax-loading').attr("disabled", false);
            $('.addInvestigation').removeClass('btn-ajax-loading');
        }
    });
});

$(document).on("click", ".particularDelete", function() {
    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    var dataTab    = $(this).attr('data-tab');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                $('#procedure-'+dataTab).find('tr#remove-'+id).remove();
            });
        }
    });
});

$(document).on('click', '#addPrescriptionParticular', function() {


    var medicine = $('#medicine').val();
    var medicineId = $('#medicineId').val();
    var generic = $('#generic').val();
    var medicineQuantity = $('#medicineQuantity').val();
    var medicineDose = $('#medicineDose').val();
    var medicineDoseTime = $('#medicineDoseTime').val();
    var medicineDuration = $('#medicineDuration').val();
    var medicineDurationType = $('#medicineDurationType').val();
    var url = $('#addPrescriptionParticular').attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'medicine='+medicine+'&medicineId='+medicineId+'&medicineQuantity='+medicineQuantity+'&medicineDose='+medicineDose+'&medicineDoseTime='+medicineDoseTime+'&medicineDuration='+medicineDuration+'&medicineDurationType='+medicineDurationType,
        success: function (response) {
            $('#invoiceMedicine').html(response);
            $('#medicine').val('');
            $('#generic').val('');
            $('#medicineId').val('');
        }
    })
});

$(document).on("click", ".deleteMedicine", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                $('#medicine-'+id).hide();
            });
        }
    });
});

$(document).on('click', '.prescription', function() {

    var url = $(this).attr('data-url');
    var dataTitle = $(this).attr('data-title');
    $('.dialogModal_header').html(dataTitle);
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: url,
                async: true,
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});



$(document).on('change', '#particular', function() {

    var url = $(this).val();
    if(url == ''){
        alert('You have to add particulars from drop down and this not service item');
        return false;
    }
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#particularId').val(obj['particularId']);
            $('#price').val(obj['price']);
            $('#instruction').html(obj['instruction']);
            $('#addParticular').attr("disabled", false);
        }
    })
});

$(document).on('change', '#appointmentDate', function() {

    var appointmentDate = $('#appointmentDate').val();
    if(appointmentDate == ''){
        return false;
    }
    var assignDoctor = $('#appstore_bundle_dpsinvoice_assignDoctor').val();
    $.get(Routing.generate('dms_invoice_appointment_schedule_time',{assignDoctor:assignDoctor,appointmentDate:appointmentDate}),
        function(data){
            $('#appointmentTime').html(data);
        }
    );
});

$(document).on('click', '#addParticular', function() {

    var particularId = $('#particularId').val();
    if (particularId == '') {

        $('#particularId').addClass('input-error');
        $('#particularId').focus();
        alert('Please select treatment particular');
        return false;
    }

    var appointmentDate = $('#appointmentDate').val();
    if (appointmentDate == '') {

        $('#appointmentDate').addClass('input-error');
        $('#appointmentDate').focus();
        alert('Please select appointment date');
        return false;
    }

    var price = $('#price').val();
    var appointmentTime = $('#appointmentTime').val();

    var url = $('#addParticular').attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'particularId='+particularId+'&price='+price+'&appointmentDate='+appointmentDate+'&appointmentTime='+appointmentTime,
        success: function (response) {
            obj = JSON.parse(response);
            $('.estimateTotal').html(obj['estimateTotal']);
            $('.subTotal').html(obj['subTotal']);
            $('.netTotal').html(obj['netTotal']);
            $('.due').html(obj['due']);
            $('.payment').html(obj['payment']);
            $('.discount').html(obj['discount']);
            $('#invoiceParticulars').html(obj['invoiceParticulars']);
            $('.completeTreatment').html(obj['completeTreatment']);
            $("#particular").select2().select2("val","");
            $('#price').val('');
            $('#addParticular').attr("disabled", true);
            $('#addPatientParticular').attr("disabled", true);
            $(".editable").editable();

        }
    })
});

$(document).on("click", ".treatmentDelete", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( response ) {
                obj = JSON.parse(response);
                $('#remove-'+id).hide();
                $('.estimateTotal').html(obj['estimateTotal']);
                $('.subTotal').html(obj['subTotal']);
                $('.netTotal').html(obj['netTotal']);
                $('.due').html(obj['due']);
                $('.payment').html(obj['payment']);
                $('.discount').html(obj['discount']);
                $('.completeTreatment').html(obj['completeTreatment']);
            });
        }
    });
});

$(document).on('click', '#received2Btn', function() {

    var treatment = $('#treatment').val();
    if (treatment == '') {
        $('#treatment').focus();
        $('#treatment').addClass('input-error');
        alert('Please select treatment name');
        return false;
    }
    if ($('input#adjustment').is(':checked')) {
        var adjustment = 1;
    }else{
        var adjustment = 0;
    }
    var receive = $('#receive').val();
    var discount = $('#discount').val();
    var transactionMethod = $('#appstore_bundle_dpsinvoice_transactionMethod').val();
    var transactionId = $('#appstore_bundle_dpsinvoice_transactionId').val();
    var paymentMobile = $('#appstore_bundle_dpsinvoice_paymentMobile').val();
    var mobileBank = $('#appstore_bundle_dpsinvoice_accountMobileBank').val();
    var accountBank = $('#appstore_bundle_dpsinvoice_accountBank').val();
    var paymentCard = $('#appstore_bundle_dpsinvoice_paymentCard').val();
    var cardNo = $('#appstore_bundle_dpsinvoice_cardNo').val();
    var url = $(this).attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'treatment='+treatment+'&discount='+discount+'&receive='+receive+'&transactionMethod='+transactionMethod+'&transactionId='+transactionId+'&paymentMobile='+paymentMobile+'&mobileBank='+mobileBank+'&accountBank='+accountBank+'&paymentCard='+paymentCard+'&cardNo='+cardNo+'&adjustment='+adjustment,
        success: function (response) {
            obj = JSON.parse(response);
            if(obj['success'] == 'success'){
                $('.estimateTotal').html(obj['estimateTotal']);
                $('.subTotal').html(obj['subTotal']);
                $('.netTotal').html(obj['netTotal']);
                $('.due').html(obj['due']);
                $('.payment').html(obj['payment']);
                $('.discount').html(obj['discount']);
                $("#treatment").val($("#treatment option:first").val());
                $('#discount').val('');
                $('#receive').val('');
                $('#uniform-adjustment span').removeClass('checked');
                $('#adjustment').attr('checked', false);
                $('#invoiceParticulars').html(obj['invoiceParticulars']);
                $('.completeTreatment').html(obj['completeTreatment']);
                $('#treatment-approved-'+treatment).hide();
            }

        }
    })
});


$(document).on('click', '#addAccessories', function() {

    var accessories = $('#accessories').val();
    if (accessories == '') {
        $('#accessories').focus();
        $('#accessories').addClass('input-error');
        alert('Please select accessories name');
        return false;
    }
    var quantity = parseInt($('#quantity').val());
    var url = $(this).attr('data-url');
    $.ajax({
        url: url,
        type: 'POST',
        data: 'accessories='+accessories+'&quantity='+quantity,
        success: function (response) {
            $("#accessories").select2().select2("val","");
            $('#quantity').val('1');
            $('#invoiceAccessories').html(response);
            $(".editable").editable();

        }
    })
});

$(document).on("click", ".deleteAccessories", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                $('#accessories-'+id).hide();
            });
        }
    });
});

$(document).on("click", ".approveAccessories", function() {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                $('#approved-'+id).hide();
            });
        }
    });
});

$(document).on('click', '.appointmentSchedule', function() {

    var url = $(this).attr('data-url');
    var dataTitle = $(this).attr('data-title');
    $('.dialogModal_header').html(dataTitle);
    $('.dialog_content').dialogModal({
        topOffset: 0,
        top: 0,
        type: '',
        onOkBut: function(event, el, current) {},
        onCancelBut: function(event, el, current) {},
        onLoad: function(el, current) {
            $.ajax({
                url: url,
                type: 'POST',
                success: function (response) {
                    el.find('.dialogModal_content').html(response);
                }
            });
        },
        onClose: function(el, current) {},
        onChange: function(el, current) {}
    });

});

$(document).on('click', '#searchAppointment', function() {

    var url = $('form#appointmentForm').attr('action');
    $.ajax({
        url: url,
        type: 'POST',
        data:new FormData($('#appointmentForm')[0]),
        processData: false,
        contentType: false,
        success: function (response) {
            $('#appointmentSchedule').html(response);
            setTimeout(datePickerReload(),1000);
            EditableInit();
        }
    })

});

var form = $("#invoiceForm").validate({

    rules: {

        "appstore_bundle_dpsinvoice[customer][name]": {required: true},
        "appstore_bundle_dpsinvoice[customer][mobile]": {required: true},
        "appstore_bundle_dpsinvoice[customer][age]": {required: true},
        "appstore_bundle_dpsinvoice[customer][address]": {required: false},
        "appstore_bundle_dpsinvoice[customer][weight]": {required: false},
        "appstore_bundle_dpsinvoice[customer][bloodPressure]": {required: false},
        "appstore_bundle_dpsinvoice[customer][bloodGroup]": {required: false},
    },

    messages: {

        "appstore_bundle_dpsinvoice[customer][name]":"Enter patient name",
        "appstore_bundle_dpsinvoice[customer][mobile]":"Enter patient mobile no",
        "appstore_bundle_dpsinvoice[customer][age]": "Enter patient age",
        "appstore_bundle_dpsinvoice[customer][hmsDiseasesProfile]": "Enter patient diseases profile",
    },
    tooltip_options: {
        "appstore_bundle_dpsinvoice[customer][name]": {placement:'top',html:true},
        "appstore_bundle_dpsinvoice[customer][mobile]": {placement:'top',html:true},
        "appstore_bundle_dpsinvoice[customer][age]": {placement:'top',html:true},
        "appstore_bundle_dpsinvoice[customer][hmsDiseasesProfile]": {placement:'top',html:true},
    },

    submitHandler: function(form) {


        $.ajax({
            url         : $('form#invoiceForm').attr( 'action' ),
            type        : $('form#invoiceForm').attr( 'method' ),
            data        : new FormData($('form#invoiceForm')[0]),
            processData : false,
            contentType : false,
            beforeSend: function(){
                $('.loader-double').fadeIn(1000).addClass('is-active');
            },
            complete: function(){
                $('.loader-double').fadeIn(1000).removeClass('is-active');
            },
            success: function(response){

            }
        });
    }
});

$('#appstore_bundle_dpsinvoice_customer_name').on('click', function(){
    form.element($(this));
});
$('#appstore_bundle_dpsinvoice_customer_mobile').on('click', function(){
    form.element($(this));
});
$('#appstore_bundle_dpsinvoice_customer_age').on('click', function(){
    form.element($(this));
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

$(document).on("change", ".inputs", function() {

    var formData = new FormData($('form#invoiceForm')[0]); // Create an arbitrary FormData instance
    var url = $('form#invoiceForm').attr('action'); // Create an arbitrary FormData instance
    $.ajax(url,{
        processData: false,
        contentType: false,
        type: 'POST',
        data: formData,
        success: function (response){}
    });
});

$(document).on("change", ".invoiceProcess", function() {

    var formData = new FormData($('form#invoiceForm')[0]); // Create an arbitrary FormData instance
    var url = $('form#invoiceForm').attr('action'); // Create an arbitrary FormData instance
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.ajax(url,{
                processData: false,
                contentType: false,
                type: 'POST',
                data: formData,
                success: function (response){}
            });
        }
    });

});


$('.particular-info').on('keypress', 'input', function (e) {
    if (e.which == 13) {
        e.preventDefault();
        switch (this.id) {

            case 'quantity':
                $('#price').focus();
                break;

            case 'price':
                $('#addParticular').trigger('click');
                $('#particular').focus();
                break;
        }
    }
});

/*
$('form.horizontal-form').on('keypress', 'input', function (e) {

    if (e.which == 13) {
        e.preventDefault();

        switch (this.id) {
            case 'appstore_bundle_hospitalbundle_invoice_discount':
                $('#appstore_bundle_hospitalbundle_invoice_payment').focus();
                break;

            case 'paymentAmount':
                $('#receiveBtn').focus();
                break;
        }
    }
});
*/
