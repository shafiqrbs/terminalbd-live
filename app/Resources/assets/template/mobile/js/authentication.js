$(document).on("click", ".logout", function() {
    var url = $(this).attr("data-action");
    $.get(url, function(data) {
        location.reload();
    });
});


$(document).on( "change", ".userMobile", function( e ) {

    var mobile = $(this).val();
    var url = $(this).attr("data-action");
    $.get(url,{ mobile:mobile} ).done(function(response) {
        $("#mobile-validate").html(response);
    }).always(function() {
        $('#mobile-confirm').notifyModal({
            duration : 10000,
            placement : 'center',
            overlay : true,
            type : 'notify',
            icon : true
        });
    });

});

function loginSigninButton() {
    setTimeout(function () {
        $("#logged-in").html('<a class="bar-icon" href="/authentiction"><i class="fa fa-home"></i></a><a  href="javascript:" data-action="/logout" class="logout bar-icon" > <i class="fa fa-sign-out"></i></a>');
        $("#login-cart").html('<button  type="submit" class="btn btn-success">Place Order <i class="fa fa-play"></i></button>');
        $.magnificPopup.proto.close.call(this);
    }, 3000)
}



$("#loginForm").validate({

    rules: {
        "_username": {required: true},
        "_password": {required: true},
    },

    messages: {

        "_username":"Enter your mobile name",
        "_password":"Enter valid password",

    },

    tooltip_options: {
        "_username": {placement:'top',html:true},
        "_password": {placement:'top',html:true}
    },
    submitHandler: function(form) {

        $.ajax({

            url         : $(form).attr( 'action' ),
            type        : $(form).attr( 'method' ),
            data        : new FormData(form),
            processData : false,
            contentType : false,
            dataType: 'json',
            success: function (data) {
                if (data.has_error) {
                    $('#loginMsg').addClass('alert-danger');
                    $('.alert-danger').html('There was an error with your Mobile no/Password combination. Please try again.');
                }else{
                    $('#loginMsg').removeClass('alert-danger');
                    $('#loginMsg').addClass('alert-success');
                    $('.alert-success').html('You have been successfully logged in...');
                    loginSigninButton();
                }
            }
        });
    }

});

$("#otpLoginForm").validate({

    rules: {
        "_username": {required: true},
        "_password": {required: true},
    },

    messages: {

        "_username":"Enter your mobile name",
        "_password":"Enter valid OTP",

    },

    tooltip_options: {
        "_username": {placement:'top',html:true},
        "_password": {placement:'top',html:true}
    },
    submitHandler: function(form) {

        $.ajax({

            url         : $(form).attr( 'action' ),
            type        : $(form).attr( 'method' ),
            data        : new FormData(form),
            processData : false,
            contentType : false,
            dataType: 'json',
            success: function (data) {
                if (data.has_error) {
                    $('#loginMsg').addClass('alert-danger');
                    $('.alert-danger').html('There was an error with your Mobile no/Password combination. Please try again.');
                }else{
                    $('#loginMsg').removeClass('alert-danger');
                    $('#loginMsg').addClass('alert-success');
                    $('.alert-success').html('You have been successfully logged in...');
                    loginSigninButton();
                }
            }
        });
    }

});

$("#sendSms").validate({
    ignore: ".ignore",
    rules: {
        mobile: {required: true},
        content: {required: true}
    },
    messages: {
        mobile: "Enter valid mobile no",
        content: "Enter your comment"
    },
    tooltip_options: {
        name: {trigger:'focus',placement:'top',html:true},
        mobile: {placement:'top',html:true}
    },
    submitHandler: function(form) {

        $.ajax({

            url         : $(form).attr( 'action' ),
            type        : $(form).attr( 'method' ),
            data        : new FormData(form),
            processData : false,
            contentType : false,
            success: function(response) {
                $("form").trigger("reset");
            },
            complete: function(){

            }
        });
    }

});

$("#contactUs").validate({

    rules: {
        name: {required: true},
        mobile: {required: true,maxlength:13,minlength:13},
        message: {required: true,maxlength:512},
        email: {email: true},
        hiddenRecaptcha: {
            required: function() {
                if(grecaptcha.getResponse() == '') {
                    return true;
                } else {
                    return false;
                }
            }
        }
    },
    messages: {
        name: "Enter name or company",
        mobile: "Enter valid mobile no",
        email: "Please enter a valid email address.",
        message: "Please enter comments",
        hiddenRecaptcha: {
            checkCaptcha: "Your Captcha response was incorrect. Please try again."
        }
    },

    tooltip_options: {
        name: {trigger:'focus',placement:'top',html:true},
        mobile: {trigger:'focus',placement:'top',html:true},
        email: {trigger:'focus',placement:'top',html:true},
        message: {trigger:'focus',placement:'top',html:true},
        hiddenRecaptcha: {trigger:'focus',placement:'top',html:true}
    },

    submitHandler: function(form) {

        if(grecaptcha.getResponse() == "") {
            alert("Your Captcha response was incorrect. Please try again.");
            return false;
        }
        $.ajax({

            url         : $(form).attr( 'action' ),
            type        : $(form).attr( 'method' ),
            data        : new FormData(form),
            processData : false,
            contentType : false,
            success: function(response) {
                $("form").trigger("reset");
                grecaptcha.reset();
            },
            complete: function(){
                $('#contactMsg').show();
            }
        });
    }

});

$("#sendEmail").validate({

    rules: {
        name: {required: true},
        mobile: {required: true,maxlength:13,minlength:13},
        content: {required: true,maxlength:512},
        email: {email: true},
        hiddenRecaptcha: {
            required: function() {
                if(grecaptcha.getResponse() == '') {
                    return true;
                } else {
                    return false;
                }
            }
        }
    },
    messages: {
        name: "Enter name or company",
        mobile: "Enter valid mobile no",
        email: "Please enter a valid email address.",
        content: "Please enter comments",
        hiddenRecaptcha: {
            checkCaptcha: "Your Captcha response was incorrect. Please try again."
        }
    },

    tooltip_options: {
        name: {trigger:'focus',placement:'top',html:true},
        mobile: {trigger:'focus',placement:'top',html:true},
        email: {trigger:'focus',placement:'top',html:true},
        content: {trigger:'focus',placement:'top',html:true},
        hiddenRecaptcha: {trigger:'focus',placement:'top',html:true}
    },

    submitHandler: function(form) {

        if(grecaptcha.getResponse() == "") {
            alert("Your Captcha response was incorrect. Please try again.");
            return false;
        }
        $.ajax({

            url         : $(form).attr( 'action' ),
            type        : $(form).attr( 'method' ),
            data        : new FormData(form),
            processData : false,
            contentType : false,
            success: function(response) {
                $("form").trigger("reset");
                grecaptcha.reset();
            },
            complete: function(){
                $('#contactMsg').show();
            }
        });
    }

});

$("#signup").validate({

    rules: {

        "Core_userbundle_user[globalOption][name]": {required: true},
        "Core_userbundle_user[profile][mobile]": {
            required: true,
            remote:'/checking-username'
        },
        "Core_userbundle_user[globalOption][syndicate]": {required: true},
        "Core_userbundle_user[globalOption][location]": {required: true},
        "Core_userbundle_user[globalOption][status]": {required: true}
    },

    messages: {

        "Core_userbundle_user[globalOption][name]":"Enter your organization name",
        "Core_userbundle_user[profile][mobile]":{
            required: "Enter valid mobile no",
            remote: "This mobile no is already registered. Please try to another no."
        },
        "Core_userbundle_user[profile][syndicate]": "Enter your professional",
        "Core_userbundle_user[profile][location]": "Enter your location",
        "Core_userbundle_user[globalOption][status]": "Please read terms & condition and agree"
    },

    tooltip_options: {

        "Core_userbundle_user[globalOption][name]": {placement:'top',html:true},
        "Core_userbundle_user[profile][mobile]": {placement:'top',html:true},
        "Core_userbundle_user[globalOption][syndicate]": {placement:'top',html:true},
        "Core_userbundle_user[globalOption][location]": {placement:'top',html:true},
        "Core_userbundle_user[globalOption][status]":{placement:'right',html:true},
    },
    submitHandler: function(form) {

        $.ajax({

            url         : $(form).attr( 'action' ),
            type        : $(form).attr( 'method' ),
            data        : new FormData(form),
            processData : false,
            contentType : false,
            success: function(response) {
                if(response == 'valid'){
                    $('#registerModal').modal('hide');
                    $('#forgetModal').modal('hide');
                    $('#loginModal').modal('toggle');
                }
            },
            complete: function(){

            }
        });
    }
});

$("#registrationForm").validate({

    rules: {

        "registration_name": {required: true},
        "registration_email": {
            required: false,
            remote:'/checking-member-email'
        },
        "registration_mobile": {
            required: true,
            remote:'/checking-member'
        },
        "registration_facebookId": {required: false},
        "registration_address": {required: true}
    },

    messages: {

        "registration_name":"Enter your full name",
        "registration_mobile":{
            required: "Enter valid mobile no",
            remote: "This mobile no is already registered. Please try to another no."
        },
        "registration_email":{
            remote: "This email is already registered. Please try to another email."
        },
        "registration_address":"Enter your address",
    },

    tooltip_options: {
        "registration_name": {placement:'top',html:true},
        "registration_mobile": {placement:'top',html:true},
    },
    submitHandler: function(form) {

        $.ajax({

            url         : $(form).attr( 'action' ),
            type        : $(form).attr( 'method' ),
            data        : new FormData(form),
            processData : false,
            contentType : false,
            success: function(response){
                window.open(response+"#modal","_self");
            },
            complete: function(response){

            }
        });
    }
});

$("#registerForm").validate({

    rules: {

        "registration_name": {required: true},
        "registration_mobile": {
            required: true,
            remote:'/checking-member'
        },
        "registration_email": {
            required: false,
            remote:'/checking-member-email'
        },
        "registration_address": {required: true}
    },

    messages: {

        "registration_name":"Enter your full name",
        "registration_mobile":{
            required: "Enter valid mobile no",
            remote: "This mobile no is already registered. Please try to another no."
        },
        "registration_email":{
            remote: "This email is already registered. Please try to another email."
        },
        "registration_address":"Enter your address",
    },

    tooltip_options: {
        "registration_name": {placement:'top',html:true},
        "registration_mobile": {placement:'top',html:true},
        "registration_email": {placement:'top',html:true},
    },
    submitHandler: function(form) {

        $.ajax({
            url         : $(form).attr( 'action' ),
            type        : $(form).attr( 'method' ),
            data        : new FormData(form),
            processData : false,
            contentType : false,
            dataType: 'json',
            success: function(response){
                if(response === 'success'){
                    $("#register-success").addClass('alert-success').show();
                    loginSigninButton();
                }else if(response === 'invalid'){
                    $("form").trigger("reset");
                }
            },

        });

    }
});



$.validator.setDefaults({

    errorElement: "span",
    errorClass: "help-block",
    //	validClass: 'stay',
    highlight: function (element, errorClass, validClass) {
        $(element).addClass(errorClass); //.removeClass(errorClass);
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass(errorClass); //.addClass(validClass);
        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
    },
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else if (element.hasClass('select2')) {
            error.insertAfter(element.next('span'));
        } else {
            error.insertAfter(element);
        }
    }
});

/* This code use for simple captcha*/

var total;
function getRandom(){return Math.ceil(Math.random()* 20);}
function createSum(){
    var randomNum1 = getRandom(),
        randomNum2 = getRandom();
    total =randomNum1 + randomNum2;
    $( "#question" ).text( randomNum1 + " + " + randomNum2 + "=" );
    $("#ans").val('');
    checkInput();
}

function checkInput(){
    var input = $("#ans").val(),
        slideSpeed = 200,
        hasInput = !!input,
        valid = hasInput && input == total;
    $('#verify').toggle(!hasInput);
    $('#registrationSubmitForm').prop('disabled', !valid);
    $('#success').toggle(valid);
    $('#fail').toggle(hasInput && !valid);
}

createSum();
// On "reset button" click, generate new random sum
//   $('#registrationSubmitForm').click(createSum);
// On user input, check value
$( "#ans" ).keyup(checkInput);


