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



/*
$(document).on("click", ".editable-submit", function() {
    setTimeout(pageReload, 3000);
});
function pageReload() {
    location.reload();
}
*/

document.onkeyup = function(e) {
    var key = e.which || e.charCode || e.keyCode
    if(e.ctrlKey && key === 83) {
        $('.select2StockMedicinePurchase').select2('open');
    } else if (e.ctrlKey && key === 80) {
        $('.regularPrintx').trigger('click');
    }
};


$(document).on( "click", "#show", function(e){
    $('#hide').slideToggle(2000);
    $("i", this).toggleClass("fa fa-angle-double-up fa fa-angle-double-down");
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

$(document).on( "click", ".androidProcess", function( e ) {
    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.ajax({
                url : url,
                beforeSend: function(){
                    $('.loader-double').fadeIn(1000).addClass('is-active');
                },
                success:  function (response) {
                  if(response === "success"){
                      location.reload();
                  }else{
                      alert('Data does not import successfully, Please try now');
                  }
                }
            });

        }
    });

});

$(document).on("click", "#vendorPaymentBtn", function() {
    var id      = $(this).attr("data-id");
    var url     = $(this).attr("data-url");
    var mode    = $("#mode").val();
    var amount  = $("#amount").val();
    var remark  = $("#remark").val();
    if(mode === "" && amount === ""){
        return false;
    }
    $("#vendorPaymentBtn").attr('disabled','disabled');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url,{'mode':mode,'amount':amount,'remark':remark}, function( data ) {
                if(data === 'success'){
                    location.reload();
                }
            });
        }
    });
});

$(document).on("click", ".delete", function() {
    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                $("#delete-"+id).hide();
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

$(".select2Medicine").select2({

    placeholder: "Search medicine name",
    ajax: {

        url: Routing.generate('medicine_select_search'),
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
        var id = $(item).val();
        $.ajax(Routing.generate('medicine_stock_name', { stock : id}), {
            dataType: "json"
        }).done(function (data) {
            console.log(data);
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1
});

$(".select2BrandName").select2({

    placeholder: "Search product brand name",
    ajax: {

        url: Routing.generate('medicine_stock_brand_search'),
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
        $.ajax(Routing.generate('medicine_stock_brand_name', { brand : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 2
});

$(document).on('keypress', '.autoComplete2Medicine', function (e) {

   // $("#medicineId").val(''); // save selected id to hidden input
});

$( ".autoComplete2Medicine" ).autocomplete({

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
    minLength: 2,
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

$(".select2StockMedicine").select2({

    placeholder: "Search stock medicine name",
    ajax: {
        url: Routing.generate('medicine_stock_search'),
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
        $.ajax(Routing.generate('medicine_stock_name', { stock : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1

});

$(".select2StockMedicinePurchase").select2({

    placeholder: "Search stock medicine name",
    ajax: {
        url: Routing.generate('medicine_stock_purchase_search'),
        dataType: 'json',
        delay: 100,
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
        $.ajax(Routing.generate('medicine_stock_name', { stock : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1

});

$(".select2Stockout").select2({

    placeholder: "Search stock medicine out name",
    ajax: {
        url: Routing.generate('medicine_stockhouse_search'),
        dataType: 'json',
        delay: 100,
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
        $.ajax(Routing.generate('medicine_stock_name', { stock : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1

});

$(".select2StockMedicineName").select2({

    placeholder: "Search stock medicine name",
    ajax: {
        url: Routing.generate('medicine_stock_name_search'),
        dataType: 'json',
        delay: 100,
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
        $.ajax(Routing.generate('medicine_stock_name', { stock : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1

});



$(".select2VendorCompany").select2({

    placeholder: "Search vendor name",
    ajax: {
        url: Routing.generate('medicine_vendor_company_search'),
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
        $.ajax(Routing.generate('medicine_vendor_name', { vendor : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1

});

$( ".select2Vendor" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('medicine_vendor_search'),
            data: {
                term: request.term
            },
            success: function( data ) {
                response(data);
            }
        });
    },
    minLength: 1,
    select: function( event, ui ) {
        $("#vendor").val(ui.item.id); // save selected id to hidden input

    }
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
        var user = $(element).val();
        $.ajax(Routing.generate('domain_user_name', { user : user}), {
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

$(document).on( "click", "#globalMedicine", function(e){
    $('#generic-hide').slideToggle(2000);
    $("i", this).toggleClass("fa fa-angle-double-up fa fa-angle-double-down");
    $('#medicineStock_name').focus();
});

$(document).on( "click", "#genericStockShow", function(e){
    $('#generic-stock-hide').slideToggle(2000);
    $("i", this).toggleClass("fa fa-angle-double-up fa fa-angle-double-down");
    $('#medicineStock_name').focus();
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
