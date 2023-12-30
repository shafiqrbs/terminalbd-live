wow = new WOW(
    {
        boxClass:     'wow',
        animateClass: 'animated',
        offset:       100
    }
);
wow.init();

$(document).ready(function(){

        "use strict";

        // custom scrollbar

        $("html").niceScroll({styler:"fb",cursorcolor:"#2ad2c9", cursorwidth: '4', cursorborderradius: '10px', background: '#FFFFFF', spacebarenabled:false, cursorborder: '0',  zindex: '1000'});

        $(".scrollbar1").niceScroll({styler:"fb",cursorcolor:"#2ad2c9", cursorwidth: '4', cursorborderradius: '0',autohidemode: 'false', background: '#FFFFFF', spacebarenabled:false, cursorborder: '0'});

        $(".scrollbar1").getNiceScroll();
        if ($('body').hasClass('scrollbar1-collapsed')) {
            $(".scrollbar1").getNiceScroll().hide();
        }

        $('.treeview').each(function () {
            var tree = $(this);
            tree.treeview();
        });

        $("#mm-menu").mmenu();

        var stickyOffset = $('.sticky').offset().top;
        $(window).scroll(function(){
            var sticky = $('.sticky'),
                scroll = $(window).scrollTop();
            if (scroll >= stickyOffset) sticky.addClass('fixed');
            else sticky.removeClass('fixed');
        });

        $('.popup-top-anim').magnificPopup({
            type: 'inline',
            fixedContentPos: false,
            fixedBgPos: true,
            overflowY: 'auto',
            closeBtnInside: true,
            preloader: false,
            midClick: true,
            removalDelay: 300,
            mainClass: 'my-mfp-zoom-in'
        });

        $('.scrollToTop').click(function(){
            $('html, body').animate({scrollTop : 0},800);
            return false;
        });

        $(".numeric").numeric();
        $(".mobile").inputmask("mask", {"mask": "99999-999-999"}); //specifying fn & options


        $('.sms-content').keypress(function(e) {

            var tval = $('textarea').val(),
                tlength = tval.length,
                set = 140,
                remain = parseInt(set - tlength);
            $('#limit').text(remain);
            if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
                $('textarea').val((tval).substring(0, tlength - 1))
            }

        });

        $("#menu-bar a").click(function () {
            var id = $(this).attr("href").substring(1);
            $("html, body").animate({scrollTop: $("#" + id).offset().top}, 1000, function () {
                $("#menu-bar").slideReveal("hide");
            });
        });

        $('.social-tooltip').tooltip({
            selector: "[data-toggle=tooltip]",
            container: "body"
        });

        $('#searchEvent').click(function(){
            $('#nav-search').slideToggle('slow');
        });

        $('#catlist').children('.list-grid').each(function(index) {
            $(this).addClass(index % 2 ? 'odd' : 'even');
        });

        $(".dropdown").hover(
            function() {
                $('.dropdown-menu', this).not('.in .dropdown-menu').stop(true,true).slideDown("400");
                $(this).toggleClass('open');
            },
            function() {
                $('.dropdown-menu', this).not('.in .dropdown-menu').stop(true,true).slideUp("400");
                $(this).toggleClass('open');
            }
        );

        $('.modalOpen').click(function(){
            var id = $(this).attr('id');
            var inst = $("[data-remodal-id=modal"+id+"]").remodal();
            inst.open();
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
                icon : false
            });
        });

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

        /* Start This Registration form using for BHAWS Association */

        var total;
        function getRandom(){return Math.ceil(Math.random()* 20);}
        function createSum(){
            var randomNum1 = getRandom(),
                randomNum2 = getRandom();
            total =randomNum1 + randomNum2;
            var sum = randomNum1 + " + " + randomNum2 + "=";
            $("#question").html(sum);
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
                "registration_address": {required: false}
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

        /* End This Registration form using for BHAWS Association */

});






