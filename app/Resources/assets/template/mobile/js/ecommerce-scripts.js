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

    $(document).on( "click", ".popup-top-anim", function(e){

        $('.popup-top-anim').magnificPopup({
            type: 'inline',
            fixedContentPos: false,
            fixedBgPos: true,
            overflowY: 'auto',
            closeBtnInside: true,
            preloader: true,
            midClick: true,
            removalDelay: 300,
            closeOnBgClick:false,
            enableEscapeKey:false,
            closeOnContentClick:false,
            mainClass: 'my-mfp-zoom-in',
            callbacks: {
                elementParse: function() {
                    $("#productFilter").hide();
                    $('#product-modal').modal('hide');
                }
            }
        });
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


});




                     
     
  