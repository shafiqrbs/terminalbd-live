function financial(val) {
    return Number.parseFloat(val).toFixed(2);
}

function cartInfo(response,quantity) {
    obj = JSON.parse(response);
    var qnt = quantity === "" ? 1 : quantity;
    $('.cartSubmit').attr("disabled", true).html(qnt+' in Basket');
    setTimeout(function(){$('.cartSubmit').html('<i class="fa fa-shopping-cart"></i> ADD')}, 3000);
    $('.totalItem').html(obj['totalItems']);
    $('.cartQuantity').html(obj['totalQuantity']);
    $('.totalAmount').html(obj['cartTotal']);
    $('.cartTotal').html(obj['cartTotal']);
    $('.grandTotal').html(obj['grandTotal']);
    $('.vsidebar .cart-amount .txt').html(obj['cartResult']).show().fadeOut(3000);
}

$(document).on( "click", ".btn-number-stock", function(e){

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

$(document).on( "click", ".btn-number-cart", function(e){

    e.preventDefault();
    url         = $(this).attr('data-url');
    price       = $(this).attr('data-title');
    fieldId     = $(this).attr('data-id');
    fieldName   = $(this).attr('data-field');
    type        = $(this).attr('data-type');
    input = $('#quantity-'+$(this).attr('data-id'));
    currentVal = parseInt(input.val()) ? parseInt(input.val()) : 0;
    if (!isNaN(currentVal)) {
        if(type === 'minus') {
            if(currentVal > input.attr('min')) {
                existVal = (currentVal - 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal})
                    .done(function( response ) {
                        subTotal = (existVal * parseInt(price));
                        var netTotal = financial(subTotal);
                        $('#btn-total-'+fieldId).html(netTotal);
                        cartInfo(response,existVal)
                    });
            }
            if(parseInt(input.val()) === input.attr('max')) {
                $('#quantity-'+fieldId).attr('disabled', true);
            }else {
                $('#quantity-'+fieldId).attr('disabled', false);
            }

        } else if(type === 'plus') {

            if(currentVal < input.attr('max')) {
                existVal = (currentVal + 1);
                input.val(existVal).change();
                $.get( url,{ quantity:existVal})
                    .done(function(response){
                        obj = JSON.parse(response);
                        if(obj['process'] === 'success'){
                            subTotal = (existVal * parseInt(price));
                            var netTotal = financial(subTotal);
                            $('#btn-total-'+fieldId).html(netTotal);
                            cartInfo(response,existVal)
                        }else{
                            input.val(existVal-1).change();
                            alert('There is not enough product in stock at this moment')
                        }
                    });
            }
            if(parseInt(input.val()) === input.attr('min')) {
                $('#quantity-'+fieldId).attr('disabled', true);
            }else {
                $('#quantity-'+fieldId).attr('disabled', false);
            }

        }
    } else {
        input.val(0);
    }
});

$('#itemName').click(function() {
    $(this).attr('value', '').focus();
});


var searchRequest = null;
var minlength = 1;

$(document).on( "keyup", ".select2Stock", function(e){

    var that = this,
        value = $(this).val();
    if (value.length >= minlength ) {
        if (searchRequest != null)
            searchRequest.abort();
        searchRequest = $.ajax({
            type: "GET",
            url: "/product-stock-search",
            data: {
                'q' : value
            },
            dataType: 'json',
            success: function(response){
                var len = response.length;
                $("#searchResult").empty();
                for( var i = 0; i<len; i++){
                    var id = response[i]['id'];
                    var fname = response[i]['name'];
                    $("#searchResult").append("<li value='"+id+"'>"+fname+"</li>");
                }
                // binding click event to li
                $("#searchResult li").bind("click",function(){
                    setText(this);
                });

            }
        });
    }
});

function setText(element){

    var value = $(element).text();
    var stockId = $(element).val();
    $('#stockId').val(stockId);
    $("#itemName").val(value);
    $("#searchResult").empty();
    $.get( "/product-stock-details", { stockId:stockId } )
        .done(function( response ) {
            obj = JSON.parse(response);
            $('#salesPrice').html(obj['price']);
            $('#unit').html(obj['unit']);
            $('#cart-subitem').html(obj['subItems']);
            $('#quantity').focus();
        });
}

$(document).on( "change", ".changeSize", function( e ) {

    var subItem = $(this).val();
    var url = $(this).attr("data-url");
    $.ajax({
        url: url ,
        type: 'GET',
        data:'subItem='+subItem,
        success: function(response) {
            $('#subItemDetails').html(response);
        },

    })

});

$(document).on( "change", "#couponCode", function( e ) {
    url = $(this).attr('data-url');
    coupon = $(this).val();
    $.get( url,{ coupon:coupon})
        .done(function( response ) {
            obj = JSON.parse(response);
            $('#cartDiscount').html(obj['discount']);
            $('.grandTotal').html(obj['grandTotal']);
        });
});


$('.dropzone').inputFileZone({
    message: 'UPLOAD YOUR SHOPPING IMAGE',
    previewImages: false
});

var _validFileExtensions = [".jpg", ".jpeg", ".gif", ".png", ".pdf"];
function Validate(oForm) {
    var arrInputs = oForm.getElementsByTagName("input");
    for (var i = 0; i < arrInputs.length; i++) {
        var oInput = arrInputs[i];
        if (oInput.type == "file") {
            var sFileName = oInput.value;
            if (sFileName.length > 0) {
                var blnValid = false;
                for (var j = 0; j < _validFileExtensions.length; j++) {
                    var sCurExtension = _validFileExtensions[j];
                    if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                        blnValid = true;
                        break;
                    }
                }

                if (!blnValid) {
                    alert("Sorry, " + sFileName + " is invalid, allowed extensions are: " + _validFileExtensions.join(", "));
                    return false;
                }
            }
        }
    }
    return true;
}


jQuery.validator.addMethod("maxFileSize", function(value, element, param) {

    var isOptional = this.optional(element),
        file;
    if(isOptional) {
        return isOptional;
    }

    if ($(element).attr("type") === "file") {

        if (element.files && element.files.length) {

            file = element.files[0];
            return ( file.size && file.size <= param );
        }
    }
    return false;
}, "File size is too large.");


$("#cartForm").validate({

    rules: {
        "cashOnDelivery": {required: false},
        uploadFile: {
            required: false,
            extension:'jpe?g,png,gif,pdf',
            maxFileSize:5242880
        }
    },messages: {
        uploadFile:{
            extension:"Upload file must be jpeg,jpg,png,pdf",
            maxFileSize:"File size must be less than 5 MB.",
        }
    },
    submitHandler: function(form,e) {
        form.submit();
    }
});

$("#stockItemForm").validate({

    rules: {
        "itemName": {required: true},
        "itemQuantity": {required: true},
        "salesPrice": {required: false},
    },

    messages: {
        "itemName": "Search a item name",
        "itemQuantity": "Enter item quantity",
    },
    tooltip_options: {
        "itemName": {placement: 'top', html: true},
        "itemQuantity": {placement: 'top', html: true},
    },

    submitHandler: function (form) {

        $.ajax({
            url: $('form#stockItemForm').attr('action'),
            type: $('form#stockItemForm').attr('method'),
            data: new FormData($('form#stockItemForm')[0]),
            processData: false,
            contentType: false,
            success: function (response) {
                obj = JSON.parse(response);
                $('form#stockItemForm')[0].reset();
                $('.totalItem').html(obj['totalItems']);
                $('.cartQuantity').html(obj['totalQuantity']);
                $('.totalAmount').html(obj['cartTotal']);
                $('.cartTotal').html(obj['cartTotal']);
                $('.grandTotal').html(obj['grandTotal']);
                $('.vsidebar .cart-amount .txt').html(obj['cartResult']).show().fadeOut(3000);
                $("#stockCart").html(obj['cartItems']);
            }
        });
    }
});

$("#customerOtpLogin").validate({

    rules: {
        "mobile": {required: true}
    },

    messages: {
        "mobile":"Enter your mobile name"
    },

    tooltip_options: {
        "mobile": {placement:'top',html:true}
    },
    submitHandler: function(form) {

        $.ajax({

            url: $('form#customerOtpLogin').attr('action'),
            type: $('form#customerOtpLogin').attr('method'),
            data: new FormData($('form#customerOtpLogin')[0]),
            processData : false,
            contentType : false,
            success: function (data) {
                obj = JSON.parse(data);;
                if(obj['status'] === '301'){
                    alert(obj['message'])
                }else{
                    $('#customerOtpLogin').hide();
                    $('#customerOtp').show();
                    $('#otp').val(obj['otpCode']);
                    $('#otpCode').val(obj['otpCode']);
                    $('#resendMobile').val(obj['mobile']);
                }
            }
        });
    }

});

$("#customerOtp").validate({

    rules : {
        otp : {
            required: true,
            minlength : 4
        }
    },

    messages: {
        "otp":"Enter 4 digit on time password"
    },
    tooltip_options: {
        "otp": {placement:'top',html:true}
    },
    submitHandler: function() {

        $.ajax({

            url: $('form#customerOtp').attr('action'),
            type: $('form#customerOtp').attr('method'),
            data: new FormData($('form#customerOtp')[0]),
            processData : false,
            contentType : false,
            success: function (data) {
                obj = JSON.parse(data);
                if(obj['status'] === 'success') {
                    location.reload();
                }else if(obj['status'] === 'new'){
                    $('#login-modal').hide();
                    $('#user-modal').slideToggle();
                }else{
                    return false;
                }
            }
        });
    }

});

$(document).on( "click", "#resendPin", function( e ) {

    var mobile = $('#resendMobile').val();
    var url = $(this).attr("data-url");
    $.ajax({
        url: url ,
        type: 'GET',
        data:'mobile='+mobile,
        success: function(response) {
            obj = JSON.parse(response);;
            if(obj['status'] === '301'){
                alert(obj['message'])
            }else{
                $('#customerOtpLogin').hide();
                $('#customerOtp').show();
                $('#otpCode').val(obj['otpCode']);
                $('#resendMobile').val(obj['mobile']);
            }
        }

    })

});

$("#registerFormCreate").validate({

    rules: {

        "registration_name": {required: true},
        "registration_mobile": {
            required: false
        },
        "registration_location": {
            required: true
        },
        "registration_address": {required: true}
    },

    messages: {

        "registration_name":"Enter your full name",
        "registration_location":"Enter your delivery location",
        "registration_address":"Enter your address"
    },

    tooltip_options: {
        "registration_name": {placement:'top',html:true},
        "registration_address": {placement:'top',html:true},
        "registration_location": {placement:'top',html:true}
    },
    submitHandler: function(form) {

        $.ajax({
            url         : $(form).attr( 'action' ),
            type        : $(form).attr( 'method' ),
            data        : new FormData(form),
            processData : false,
            contentType : false,
            success: function(response){
                location.reload();
            }

        });
    }
});

$("#registerFormUpdate").validate({

    rules: {

        "registration_name": {required: true},
        "registration_additional_phone": {
            required: false
        },
        "registration_location": {
            required: true
        },
        "registration_address": {required: true}
    },

    messages: {

        "registration_name":"Enter your full name",
        "registration_location":"Enter your delivery location",
        "registration_address":"Enter your address"
    },

    tooltip_options: {
        "registration_name": {placement:'top',html:true},
        "registration_address": {placement:'top',html:true},
        "registration_location": {placement:'top',html:true}
    },
    submitHandler: function(form) {

        $.ajax({
            url         : $(form).attr( 'action' ),
            type        : $(form).attr( 'method' ),
            data        : new FormData(form),
            processData : false,
            contentType : false,
            success: function(response){
                location.reload();
            }

        });
    }
});

$('.accordion').on('click', '.accordion-control', function(e) {
    e.preventDefault();
    $(this).next('.accordion-panel').not(':animated').slideToggle();
    if ($(this).hasClass('active')) {
        $(this).removeClass('active');
    } else {
        $(this).addClass('active');
    }
});

$(document).on( "click", ".label-warning", function() {
    $('#payment-transaction').slideToggle();
});

$(document).on('click',"#cart-print", function (event) {

    var url = $(this).attr('data-action');

    $.MessageBox({

        buttonDone      : "OK",
        buttonFail      : "Cancel",
        message         : "<b>PRINT CONFIRM</b>",
        input           : {
            mobile : {
                type    : "text",
                label   : "Mobile:",
                title   : "Mobile no"
            }
        },
        filterDone: function (data) {
            if (data['mobile'] === "") return "Please input mobile no";
            return $.ajax({
                url: url,
                type: "get",
                data: data
            }).done(function (data) {
                if (data === 'invalid') {
                    $.MessageBox("This item name already exist, Please try another item name");
                } else {
                    $.MessageBox("Invalid");
                }
            })
        }
    });
});

$(document).on( "click", ".hunger-remove-cart", function(e){
    var url = $(this).attr("data-url");
    var id = $(this).attr("id");
    $('#item-remove-'+id).hide();
    $.ajax({
        url:url ,
        type: 'GET',
        success: function(response){
            obj = JSON.parse(response);
            $('#cart-item-list-box').html(obj['cartItem']);
            $('.totalItem').html(obj['totalItem']);
            $('.totalAmount').html(obj['cartTotal']);
            $('.vsidebar .txt').html(obj['cartResult']);

        }
    });
    e.preventDefault();
});



