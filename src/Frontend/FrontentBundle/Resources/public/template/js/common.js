
$('#main-carousel').carousel({
    wrap:true,
    interval:15000
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

$('#topTabCarousel').carousel({
    interval:   4000
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

    $(document).on( "click", ".emailSender", function(e){
        $.ajax({
            url         : $('#newsLetter').attr( 'action' ),
            type        : $('#newsLetter').attr( 'method' ),
            data        : $('#newsLetter').serialize(),
            success: function(response) {
                $('#newsLetter').find("input[type=text]").val("");
                $('#email-success').html('Your e-mail is sending successfuly');
            },

        });

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

    $("#commentform").submit(function (e) {

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


    function fixButtonHeights() {

        var heights = new Array();
        // Loop to get all element heights
        $('.height-box').each(function() {
            // Need to let sizes be whatever they want so no overflow on resize
            $(this).css('min-height', '0');
            $(this).css('max-height', 'none');
            $(this).css('height', 'auto');

            // Then add size (no units) to array
            heights.push($(this).height());
        });

        // Find max height of all elements
        var max = Math.max.apply( Math, heights );

        // Set all heights to max height
        $('.height-box').each(function() {
            $(this).css('height', max + 'px');
            // Note: IF box-sizing is border-box, would need to manually add border and padding to height (or tallest element will overflow by amount of vertical border + vertical padding)
        });
    }

    $(window).load(function() {
        // Fix heights on page load
        fixButtonHeights();

        // Fix heights on window resize
        $(window).resize(function() {
            // Needs to be a timeout function so it doesn't fire every ms of resize
            setTimeout(function() {
                fixButtonHeights();
            }, 120);
        });
    });



