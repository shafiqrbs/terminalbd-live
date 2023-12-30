

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

$('form#medicine').on('keypress', '.input', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form").eq(0).find("input,select");
        var idx = inputs.index(this);
        if (idx == inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {

            case 'salesitem_quantity':
                $('#addParticular').focus();
                break;
        }
        return false;
    }
});

var form = $("#medicine").validate({

    rules: {

        "brand": {required: true},
        "generic": {required: false},
        "medicineForm": {required: true},
        "companyName": {required: false},
        "packSize": {required: false},
        "strength": {required: true}
    },

    tooltip_options: {
        "brand": {placement:'top',html:true},
        "generic": {placement:'top',html:true},
        "medicineForm": {placement:'top',html:true},
        "companyName": {placement:'top',html:true},
        "packSize": {placement:'top',html:true},
        "strength": {placement:'top',html:true}
    }
});



$( "#brand" ).autocomplete({

    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('medicine_search_brand_name_complete'),
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
        $.ajax( {
            url: Routing.generate('medicine_details'),
            data: {
                medicine: ui.item.id
            },
            success: function( response ) {
                obj = JSON.parse(response);
                $('#generic').val(obj['generic']);
                $('#medicineForm').val(obj['medicineForm']);
                $('#companyName').val(obj['company']);
            }
        });
    }
});

$( "#companyName" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('medicine_search_company_name_complete'),
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

$( "#generic" ).autocomplete({

    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('medicine_search_generic_name_complete'),
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

$( ".select2PackSize" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('medicine_search_pack_size_name_complete'),
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
            url: Routing.generate('medicine_search_strength_name_complete'),
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

$( ".select2MedicineForm" ).autocomplete({
    source: function( request, response ) {
        $.ajax( {
            url: Routing.generate('medicine_search_form_name_complete'),
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








