function AccountingApproveProcess(){}

    $('.horizontal-form').submit(function(){
        $("button[type='submit']", this)
            .html("Please Wait...")
            .attr('disabled', 'disabled');
        return true;
    });

    $(".addCustomer").click(function(){
        $( ".customer" ).slideToggle( "slow" );
    }).toggle( function() {
        $(this).removeClass("blue").addClass("red").html('<i class="icon-remove"></i>');
    }, function() {
        $(this).removeClass("red").addClass("blue").html('<i class="icon-plus"></i> Add New');
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
    });

    $( ".dateCalendar" ).datepicker({
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
    });

    $(document).on("click", ".editable-submit", function() {
       // setTimeout(pageReload, 1000);
    });

    function pageReload() {
        location.reload();
    }

$(document).on("click", ".ledger-print", function(e) {
    var url = $(this).attr("data-action");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            htmlPrint(response);
           // $('#print-area').html(response).kinziPrint();
        },
    })
});

function htmlPrint(response) {
    w = window.open(window.location.href,"_blank");
    w.document.open();
    w.document.write(response);
    w.window.print();

}

$(document).on("click", ".delete", function(e) {

    var id = $(this).attr("data-id");
    var url = $(this).attr("data-url");
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    if ('success' === response ) {
                        location.reload();
                    }
                },
            })
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
                        location.reload();
                    }
                }
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

    $(".select2Vendor").select2({

        placeholder: "Search purchase vendor name",
        ajax: {
            url: Routing.generate('account_purchase_vendor_search'),
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
            $.ajax(Routing.generate('account_purchase_vendor_name', { vendor : id}), {
                dataType: "json"
            }).done(function (data) {
                  return  callback(data);
            });
        },
        allowClear: true,
        minimumInputLength: 1

    });

    $(".select2HmsVendor").select2({

        placeholder: "Search vendor name",
        ajax: {
            url: Routing.generate('hms_vendor_search'),
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
            $.ajax(Routing.generate('hms_vendor_name', { vendor : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });
        },
        allowClear: true,
        minimumInputLength: 1

    });

    $(".select2MedicineVendor").select2({

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

    $(".select2AccountVendor").select2({

    ajax: {

        url: Routing.generate('account_vendor_search'),
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
        var name = $(element).val();
        $.ajax(Routing.generate('account_vendor_name', { name : name}), {
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
        var id = $(element).val();
        $.ajax(Routing.generate('domain_customer_name', { customer : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1
});

    $(".select2CustomerName").select2({

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
        var id = $(element).val();
        $.ajax(Routing.generate('domain_customer_name', { customer : id}), {
            dataType: "json"
        }).done(function (data) {
            return  callback(data);
        });
    },
    allowClear: true,
    minimumInputLength: 1
});

    $(".select2Invoice").select2({

        ajax: {

            url: Routing.generate('inventory_sales_invoice_search'),
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
            $.ajax(Routing.generate('inventory_sales_invoice_name', { user : id}), {
                dataType: "json"
            }).done(function (data) {
                return  callback(data);
            });
        },
        allowClear: true,
        minimumInputLength: 1
    });

    var count = 0;

    $('.addmore').click(function(){

        var $el = $(this);
        var $cloneBlock = $('#clone-block');
        var $clone = $cloneBlock.find('.clone:eq(0)').clone();
        $clone.find('[id]').each(function(){this.id+='someotherpart'});
        $clone.find('.select2' ).val("").select();
        $clone.find(':text,textarea' ).val("");
        $clone.find('.debit' ).val(0);
        $clone.find('.credit' ).val(0);
        $clone.attr('id', "added"+(++count));
        $clone.find('.remove').removeClass('hidden');
        $cloneBlock.append($clone);
        $clone.children("select").select2();
        $('.numeric').numeric();
    });

    $('#clone-block').on('click', '.remove', function(){
        $(this).closest('.clone').remove();
    });

    $('.trash').on("click", ".remove", function() {

        var url = $(this).attr('data-url');
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if ('success' === response) {
                    location.reload();
                }
            },
        })
    });

    $('.amount').on('click', function(event) {
        $(this).val('');
    });

    $(document).on('keyup', ".amount", function() {
    var sum = 0;
    $(".amount").each(function(){
        sum += +parseFloat($(this).val());
    });
    $("#total").html(sum);
});




