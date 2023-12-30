$('#main-carousel').carousel({
    wrap:true,
    interval:false
});

var owlMainPageSlider = $("#main-page-slider");
owlMainPageSlider.owlCarousel({
    slideSpeed : 10000,
    paginationSpeed : 10000,
    pagination:true,
    singleItem:true
});

// Custom Navigation Events
$("#main-page-next").click(function(){
    owlMainPageSlider.trigger('owl.next');
});
$("#main-page-prev").click(function(){
    owlMainPageSlider.trigger('owl.prev');
});

$('#topTabCarousel').carousel({
    interval:   10000
});

$('#bottomTabCarousel').carousel({
    interval:   10000
});

var clickEvent = false;
$('#bottomTabCarousel').on('click', '.nav a', function() {
    clickEvent = true;
    $('.nav li').removeClass('active');
    $(this).parent().addClass('active');
}).on('slid.bs.carousel', function(e) {
    if(!clickEvent) {
        var count = $('.nav').children().length -1;
        var current = $('.nav li.active');
        current.removeClass('active').next().addClass('active');
        var id = parseInt(current.data('slide-to'));
        if(count == id) {
            $('.nav li').first().addClass('active');
        }
    }
    clickEvent = false;
});

var clickEvent = false;
$('#topTabCarousel').on('click', '.nav a', function() {
    clickEvent = true;
    $('.nav li').removeClass('active');
    $(this).parent().addClass('active');
}).on('slid.bs.carousel', function(e) {
    if(!clickEvent) {
        var count = $('.nav').children().length -1;
        var current = $('.nav li.active');
        current.removeClass('active').next().addClass('active');
        var id = parseInt(current.data('slide-to'));
        if(count == id) {
            $('.nav li').first().addClass('active');
        }
    }
    clickEvent = false;
});

    
    $('#contactForm').on('submit', function(e){

        e.preventDefault();
        e.stopPropagation();

        // get values from FORM
        var name = $("#name").val();
        var email = $("#email").val();
        var message = $("#message").val();
        var goodToGo = false;
        var messgaeError = 'Request can not be send';
        var pattern = new RegExp(/^(('[\w-\s]+')|([\w-]+(?:\.[\w-]+)*)|('[\w-\s]+')([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);


        if (name && name.length > 0 && $.trim(name) !='' && message && message.length > 0 && $.trim(message) !='' && email && email.length > 0 && $.trim(email) !='') {
            if (pattern.test(email)) {
                goodToGo = true;
            } else {
                messgaeError = 'Please check your email address';
                goodToGo = false;
            }
        } else {
            messgaeError = 'You must fill all the form fields to proceed!';
            goodToGo = false;
        }
        if (goodToGo) {
            $.ajax({
                data: $('#contactForm').serialize(),
                beforeSend: function() {
                    $('#success').html('<div class="col-md-12 text-center"><img src="images/spinner1.gif" alt="spinner" /></div>');
                },
                success:function(response){
                    if (response==1) {
                        $('#success').html('<div class="col-md-12 text-center">Your email was sent successfully</div>');
                        window.location.reload();
                    } else {
                        $('#success').html('<div class="col-md-12 text-center">E-mail was not sent. Please try again!</div>');
                    }
                },
                error:function(e){
                    $('#success').html('<div class="col-md-12 text-center">We could not fetch the data from the server. Please try again!</div>');
                },
                complete: function(done){
                    console.log('Finished');
                },
                type: 'POST',
                url: 'js/send_email.php',
            });
            return true;
        } else {
            $('#success').html('<div class="col-md-12 text-center">'+ messgaeError +'</div>');
            return false;
        }
        return false;
    });


    $( "#viewed" ).click(function() {
        $( "#viewed-item" ).slideToggle( "slow" );
    });

    $('.login-preview').click(function () {
        $('#registerModal').modal('hide');
        $('#forgetModal').modal('hide');
        $('#loginModal').modal('toggle');

    });

    $('.register-btn').click(function () {

        $('#loginModal').modal('hide');
        $('#forgetModal').modal('hide');
        $('#registerModal').modal('toggle');

    });
    $('#forget-password').click(function () {

        $('#loginModal').modal('hide');
        $('#registerModal').modal('hide');
        $('#forgetModal').modal('toggle');
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

    $('.select2').on('change', function () {
        $(this).valid();
    });

    $("#loginForm").submit(function (e) {

        e.preventDefault();
        var $this = $(e.currentTarget),
            inputs = {};

        // Send all form's inputs
        $.each($this.find('input'), function (i, item) {
            var $item = $(item);
            inputs[$item.attr('name')] = $item.val();
        });

        // Send form into ajax
        $.ajax({
            url: $this.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: inputs,
            success: function (data) {
                if (data.has_error) {
                    $('#error').html(data.error);
                }else {
                    location.reload();
                }
            }
        });
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
            "Core_userbundle_user[globalOption][status]":{placement:'right',html:true}
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

    $("div.list-group > a").click(function(e) {
        e.preventDefault();
        $(this).siblings('a.active').removeClass("active");
        $(this).addClass("active");
        var index = $(this).index();
        $("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
        $("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
    });

    $('.modalOpen').click(function(){
        var id = $(this).attr('id');
        var inst = $("[data-remodal-id=modal"+id+"]").remodal();
        inst.open();
    });
