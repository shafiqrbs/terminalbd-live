
$( ".date-picker" ).datepicker({
    dateFormat: "dd-mm-yy"
});

var dateToday = new Date();
$( ".dateLeavePicker" ).datepicker({
    dateFormat: "dd-mm-yy",
    minDate: dateToday
});

$( ".dateCalendar" ).datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
});

    $('.datePickerMonth').datepicker(
    {
        dateFormat: "mm-yy",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        onClose: function(dateText, inst) {

            function isDonePressed(){
                return ($('#ui-datepicker-div').html().indexOf('ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all ui-state-hover') > -1);
            }

            if (isDonePressed()){
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1)).trigger('change');

                $('.date-picker').focusout()//Added to remove focus from datepicker input box on selecting date
            }
        },
        beforeShow : function(input, inst) {

            inst.dpDiv.addClass('month_year_datepicker')

            if ((datestr = $(this).val()).length > 0) {
                year = datestr.substring(datestr.length-4, datestr.length);
                month = datestr.substring(0, 2);
                $(this).datepicker('option', 'defaultDate', new Date(year, month-1, 1));
                $(this).datepicker('setDate', new Date(year, month-1, 1));
                $(".ui-datepicker-calendar").hide();
            }
        }
    })

$(document).on( "click", "#show", function(e){
    $('#hide').slideToggle(2000);
    $("i", this).toggleClass("fa fa-angle-double-up fa fa-angle-double-down");
});

$('form.form-horizontal').on('keyup', '#employee_profile_address', function (e) {
    var mrp = $('#employee_profile_address').val();
    $('#employee_profile_permanentAddress').val(mrp);
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

$(document).on("click", ".confirm , .approve , .delete ", function() {
    var url = $(this).attr('data-url');
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get(url, function( data ) {
                if(data === "success"){
                    location.reload();
                }

            });
        }
    });
});

var table = $('#attendance').DataTable( {
    scrollY:        "auto",
    scrollX:        true,
    scrollCollapse: true,
    paging:         false,
    bInfo : false,
    fixedColumns:   {
        leftColumns: 1,
        rightColumns: 1
    }
});

$(document).on("click", ".attendance", function() {

    var url = $(this).attr('data-url');
    var id = $(this).attr('data-id');
    var present = $('#'+id).val();
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event, el) {
            $.get( url,{present:present})
                .done(function(data){
                    $('this').prop('checked',true);
            });
        },
        onClose:function(el){
            $(this).prop( "checked", false ).removeAttr('checked');
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
    },
    allowClear: true,
    minimumInputLength: 1
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

var count = 0;
$('.addmore').click(function(){

    var $el = $(this);
    var $cloneBlock = $('#clone-block');
    var $clone = $cloneBlock.find('.clone:eq(0)').clone();
    $clone.find('[id]').each(function(){this.id+='someotherpart'});
    $clone.find(':text,textarea' ).val("");
    $clone.attr('id', "added"+(++count));
    $clone.find('.remove').removeClass('hidden');
    $cloneBlock.append($clone);
    $('.numeric').numeric();
});

$('#clone-block').on('click', '.remove', function(){
    $(this).closest('.clone').remove();
});

var count = 0;
$('.addDeduction').click(function(){

    var $el = $(this);
    var $cloneBlock = $('#clone-block-deduction');
    var $clone = $cloneBlock.find('.cloneDeduction:eq(0)').clone();
    $clone.find('[id]').each(function(){this.id+='someotherpart'});
    $clone.find(':text,textarea' ).val("");
    $clone.attr('id', "added"+(++count));
    $clone.find('.remove').removeClass('hidden');
    $cloneBlock.append($clone);
    $('.numeric').numeric();
});

$('#clone-block-deduction').on('click', '.remove', function(){
    $(this).closest('.clone').remove();
});

$('.trash').on("click", ".remove", function() {

    var url = $(this).attr('data-url');
    var id = $(this).attr("data-id");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            if ('success' === response) {
                $('#remove-'+id).hide();
            }
        },
    })
});

