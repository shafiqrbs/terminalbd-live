$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0"
});



/*
$(document).on("click", ".editable-submit", function() {
    setTimeout(pageReload, 3000);
});
function pageReload() {
    location.reload();
}
*/

$(document).on( "click", "#show", function(e){
    $('#hide').slideToggle(2000);
    $("i", this).toggleClass("fa fa-angle-double-up fa fa-angle-double-down");
});

$(document).on("click", ".confirmSubmit", function() {
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('form').submit();
        }
    });

});

$(document).on("click", ".invoiceConfirm", function() {
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $('#invoiveTransactionForm').submit();
        }
    });
});


$(document).on("click", ".confirm", function() {
    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                location.reload();
            });
        }
    });
});

$(document).on("click", ".tr-remove", function(event) {
    var id = $(this).attr("data-id");
    var url = $(this).attr("data-action");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(el) {
            $.get(url, function( data ) {
                if(data === 'invalid'){
                    location.reload();
                }else{
                    $('#remove-'+id).remove();
                    $(event.target).closest('tr').remove();
                }
            });
        }
    });
});


/*
$(document).on("click", ".delete", function() {
    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                location.reload();
            });
        }
    });
});


$(document).on("click", ".approve", function() {
    $(this).removeClass('approve');
    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                location.reload();
            });
        }
    });
});
*/


$(".select2Grn").select2({

    placeholder: "Search purchase grn",
    ajax: {
        url: Routing.generate('inventory_grn_search'),
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
    initSelection: function (element, callback) {
        var id = $(element).val();
        $.ajax(Routing.generate('inventory_grn_name', { grn : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1
});

$(".select2Product").select2({

    placeholder: "Search product name",
    ajax: {

        url: Routing.generate('inventory_product_search'),
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
    initSelection: function (element, callback) {
        var id = $(element).val();
        $.ajax(Routing.generate('inventory_product_name', { product : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1
});

$( ".select2Medicine" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('medicine_search'),
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
        $("#medicineId").val(ui.item.id); // save selected id to hidden input

    }
});

$( ".select2Generic" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('generic_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        } );
    },
    minLength: 2,
    select: function( event, ui ) {
        $("#medicineId").val(ui.item.id); // save selected id to hidden input

    }
});

$( ".select2MedicineForm" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('medicine_form_search'),
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
        $("#medicineId").val(ui.item.id); // save selected id to hidden input

    }
});

$( ".select2Strength" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('medicine_strength_search'),
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
        $("#medicineId").val(ui.item.id); // save selected id to hidden input

    }
});

$( ".select2PackSize" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('medicine_packsize_search'),
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
        $("#medicineId").val(ui.item.id); // save selected id to hidden input

    }
});

$( ".select2MedicineCompany" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('medicine_company_search'),
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
        $("#companyId").val(ui.item.id); // save selected id to hidden input

    }
});




$(".select2CustomerCode" ).autocomplete({

    source: function( request, response ) {
        $.ajax({
            url: Routing.generate('domain_customer_code_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        });
    },
    minLength: 8,
    select: function( event, ui){
        var customerId = ui.item.id;
        var invoice = $('#invoice').val();
        $.ajax({
            url: Routing.generate('hms_invoice_customer_details'),
            type: 'POST',
            data:'customer='+customerId +'&invoice='+invoice,
            success: function(response) {
                obj = JSON.parse(response);
                if(obj['status'] == 'valid'){
                    location.reload();
                }else{
                    alert("Exit patient information does not exist");
                }
            },
        })
    }

});

$( ".select2mobile" ).autocomplete({

    source: function( request, response ) {
        $.ajax({
            url: Routing.generate('domain_customer_auto_mobile_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response( data );
            }
        });
    },
    minLength: 11,
    select: function( event, ui){
        var customerId = ui.item.id;
        var invoice = $('#invoice').val();
        $.ajax({
            url: Routing.generate('hms_invoice_customer_details'),
            type: 'POST',
            data:'customer='+customerId+'&invoice='+invoice,
            success: function(response) {
                obj = JSON.parse(response);
                if(obj['status'] == 'valid'){
                    location.reload();
                }else{
                    alert("Exit patient information does not exist");
                }
            },
        })
    }

});


$(".select2Vendor").select2({

    placeholder: "Search vendor name",
    ajax: {
        url: Routing.generate('inventory_vendor_search'),
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
    formatResult: function (item) { return item.text}, // omitted for brevity, see the source of this page
    formatSelection: function (item) { return item.text }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var id = $(element).val();
        $.ajax(Routing.generate('inventory_vendor_name', { vendor : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1

});


$(".select2User").select2({

    ajax: {

        url: Routing.generate('domain_user_search'),
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
    initSelection: function (element, callback) {
        var id = $(element).val();
        $.ajax(Routing.generate('domain_user_name', { user : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1
});

$(".select2Customer").select2({

    ajax: {

        url: Routing.generate('domain_customer_search'),
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
    initSelection: function (element, callback) {
        var customer = $(element).val();
        $.ajax(Routing.generate('domain_customer_name', { customer : customer}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1
});

$(".select2Location").select2({

    ajax: {

        url: Routing.generate('domain_location_search'),
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
    initSelection: function (element, callback) {
        var location = $(element).val();
        $.ajax(Routing.generate('domain_location_name', { location : location}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1
});

$(document).on( "click", ".btn-number", function(e){

    e.preventDefault();
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $("input[name='"+fieldName+"']");
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {

            if(currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            }
            if(parseInt(input.val()) == input.attr('min')) {
                $(this).attr('disabled', true);
            }
        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $(this).attr('disabled', true);
            }
        }
    } else {
        input.val(0);
    }
});

$(document).on( "click", ".btn-number-day", function(e){

    e.preventDefault();
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $("input[name='"+fieldName+"']");
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {

            if(currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            }
            if(parseInt(input.val()) == input.attr('min')) {
                $(this).attr('disabled', true);
            }
        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $(this).attr('disabled', true);
            }
        }
    } else {
        input.val(0);
    }
});


