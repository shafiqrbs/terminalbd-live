$.validator.addMethod("uidValid", function(uid, element) {
    return (this.optional(element) || uid.match(/^[a-z][a-z0-9]*$/i));
}, "Please specify a valid user id");

var FormValidation = function () {

    var handleValidation = function () {

        var form1 = $('#supplier-client-form');
        var error1 = $('.alert-danger', form1);
        var success1 = $('.alert-success', form1);

        form1.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                'bcl_supplierclientbundle_supplierclient[firstName]': {
                    uidValid:true,
                    minlength: 2,
                    required: true
                },
                'bcl_supplierclientbundle_supplierclient[companyName]': {
                    minlength: 2,
                    required: true
                },
                'bcl_supplierclientbundle_supplierclient[email]': {
                    required: true,
                    email: true
                },
                'bcl_supplierclientbundle_supplierclient[phoneNumber]': {
                    required: true,
                    number: true
                },
                'bcl_supplierclientbundle_supplierclient[address]': {
                    minlength: 2,
                    required: true
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit
                success1.hide();
                error1.show();
                App.scrollTo(error1, -200);
            },

            errorPlacement: function (error, element) { // render error placement for each input type
                var icon = $(element).parent('.input-icon').children('i');
                icon.removeClass('fa-check').addClass("fa-warning");
                icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by hightlight

            },

            success: function (label, element) {
                var icon = $(element).parent('.input-icon').children('i');
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");
            },

            submitHandler: function (form) {
                form.submit();
                //success1.show();
                error1.hide();
            }
        });

    }

    var form2 = $('#user-form');
    var error2 = $('.alert-danger', form2);
    var success2 = $('.alert-success', form2);

    form2.validate({
        errorElement: 'span', //default input error message container
        errorClass: 'help-block', // default input error message class
        focusInvalid: false, // do not focus the last invalid input
        ignore: "",
        rules: {
            'bcl_userbundle_user[firstName]': {
                uidValid:true,
                minlength: 2,
                required: true
            },
            'bcl_userbundle_user[username]': {
                minlength: 2,
                required: true,
                uidValid:true
            },
            'bcl_userbundle_user[email]': {
                required: true,
                email: true
            },
            'bcl_userbundle_user[phoneNumber]': {
                required: true,
                number: true
            },
            'bcl_userbundle_user[designation]': {
                minlength: 2,
                required: true
            },
            'bcl_userbundle_user[passwords][Password]': {
                minlength: 5,
                required: true
            },
            'bcl_userbundle_user[passwords][Confirm_Password]': {
                required: true,
                minlength: 5,
                equalTo: '#bcl_userbundle_user_passwords_Password'
            }
        },
        messages: {
            'bcl_userbundle_user[passwords][Confirm_Password]': {
                required: 'Repeat your password',
                equalTo: 'Enter the same password as above'
            }
        },

        invalidHandler: function (event, validator) { //display error alert on form submit
            success2.hide();
            error2.show();
            App.scrollTo(error2, -200);
        },

        errorPlacement: function (error, element) { // render error placement for each input type
            var icon = $(element).parent('.input-icon').children('i');
            icon.removeClass('fa-check').addClass("fa-warning");
            icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
        },

        highlight: function (element) { // hightlight error inputs
            $(element)
                .closest('.form-group').addClass('has-error'); // set error class to the control group
        },

        unhighlight: function (element) { // revert the change done by hightlight

        },

        success: function (label, element) {
            var icon = $(element).parent('.input-icon').children('i');
            $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
            icon.removeClass("fa-warning").addClass("fa-check");
        },

        submitHandler: function (form) {
            form.submit();
            //success2.show();
            error2.hide();
        }
    });

//    var form3 = $('#product-form');
//    var error3 = $('.alert-danger', form3);
//    var success3 = $('.alert-success', form3);
//
//    form3.validate({
//        errorElement: 'span', //default input error message container
//        errorClass: 'help-block', // default input error message class
//        focusInvalid: false, // do not focus the last invalid input
//        ignore: "",
//        rules: {
//            'bcl_productbundle_product[productName]': {
//                minlength: 2,
//                required: true
//            },
//            'bcl_productbundle_product[description]': {
//                minlength: 2,
//                required: true
//            },
//            'bcl_productbundle_product[isCategory]': {
//                required: true
//            }
//        },
//
//        invalidHandler: function (event, validator) { //display error alert on form submit
//            success3.hide();
//            error3.show();
//            App.scrollTo(error3, -200);
//        },
//
//        errorPlacement: function (error, element) { // render error placement for each input type
//            var icon = $(element).parent('.input-icon').children('i');
//            icon.removeClass('fa-check').addClass("fa-warning");
//            icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
//        },
//
//        highlight: function (element) { // hightlight error inputs
//            $(element)
//                .closest('.form-group').addClass('has-error'); // set error class to the control group
//        },
//
//        unhighlight: function (element) { // revert the change done by hightlight
//
//        },
//
//        success: function (label, element) {
//            var icon = $(element).parent('.input-icon').children('i');
//            $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
//            icon.removeClass("fa-warning").addClass("fa-check");
//        },
//
//        submitHandler: function (form) {
//            form.submit();
//            //success3.show();
//            error3.hide();
//        }
//    });

    var form4 = $('#file-form');
    var error4 = $('.alert-danger', form4);
    var success4 = $('.alert-success', form4);

    form4.validate({
        errorElement: 'span', //default input error message container
        errorClass: 'help-block', // default input error message class
        focusInvalid: false, // do not focus the last invalid input
        ignore: "",
        rules: {
            'bcl_filebundle_file[title]': {
                minlength: 2,
                required: true
            },
            'bcl_filebundle_file[description]': {
                minlength: 2,
                required: true
            },
            'bcl_filebundle_file[price]': {
                required: true
            },
            'bcl_filebundle_file[commission]': {
                required: true
            },
            'bcl_filebundle_file[quantity]': {
                required: true
            },
            'bcl_filebundle_file[lineTotal]': {
                required: true
            }
        },

        invalidHandler: function (event, validator) { //display error alert on form submit
            success4.hide();
            error4.show();
            App.scrollTo(error4, -200);
        },

        errorPlacement: function (error, element) { // render error placement for each input type
            var icon = $(element).parent('.input-icon').children('i');
            icon.removeClass('fa-check').addClass("fa-warning");
            icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
        },

        highlight: function (element) { // hightlight error inputs
            $(element)
                .closest('.form-group').addClass('has-error'); // set error class to the control group
        },

        unhighlight: function (element) { // revert the change done by hightlight

        },

        success: function (label, element) {
            var icon = $(element).parent('.input-icon').children('i');
            $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
            icon.removeClass("fa-warning").addClass("fa-check");
        },

        submitHandler: function (form) {
            form.submit();
            //success2.show();
            error2.hide();
        }
    });

    var handleWysihtml5 = function () {
        if (!jQuery().wysihtml5) {

            return;
        }

        if ($('.wysihtml5').size() > 0) {
            $('.wysihtml5').wysihtml5({
                "stylesheets": ["assets/plugins/bootstrap-wysihtml5/wysiwyg-color.css"]
            });
        }
    }

    return {
        init: function () {
            //handleWysihtml5();
            handleValidation();
        }
    };

}();
