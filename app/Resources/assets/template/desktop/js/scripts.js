/**
 * Created by rbs on 8/20/17.
 */
wow = new WOW(
    {
        boxClass:     'wow',
        animateClass: 'animated',
        offset:       100
    }
);
wow.init();

$(document).ready(function(){

        var stickyOffset = $('.sticky').offset().top;
        $(window).scroll(function(){
            var sticky = $('.sticky'),
                scroll = $(window).scrollTop();
            if (scroll > 1) sticky.addClass('fixed-top');
            else sticky.removeClass('fixed-top');
        });

        $('.social-tooltip').tooltip({
            selector: "[data-toggle=tooltip]",
            container: "body"
        });


        $('.select-category').select2({
            placeholder: "Filter by category",
            allowClear: true,
            color: "black"
        });

        $('.select-brand').select2({
            placeholder: "Filter by barnd",
            allowClear: true,
            color: "black"
        });

        $('.select-location').select2({
            placeholder: "Filter by location",
            allowClear: true,
            color: "black"
        });
        
        $(".numeric").numeric();
        $(".mobile").inputmask("mask", {"mask": "99999-999-999"}); //specifying fn & options
        $(".otp").inputmask("mask", {"mask": "9999"}); //specifying fn & options

        $('#searchEvent').click(function(){
            $('#nav-search').slideToggle('slow');
        });

        $(document).on("click", ".searchBtnSelector", function() {
            $('#search-area').slideToggle('slow');
        });

        $('#catlist').children('.news-list').each(function(index) {
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
        
        $('#list').click(function(event){event.preventDefault();$('#product .item').addClass('list-group-item');});
        $('#grid').click(function(event){event.preventDefault();$('#product .item').removeClass('list-group-item');$('#products .item').addClass('grid-group-item');});

        $('body').append('<div id="toTop" class="btn btn-primary color1"><span class="glyphicon glyphicon-chevron-up"></span></div>');
        $(window).scroll(function () {
            if ($(this).scrollTop() != 0) {
                $('#toTop').fadeIn();
            } else {
                $('#toTop').fadeOut();
            }
        });
        $('#toTop').click(function(){
            $("html, body").animate({ scrollTop: 0 }, 700);
            return false;
        });

        $('.custom-menu a[href^="#"], .intro-scroller .inner-link').on('click',function (e) {
        
            e.preventDefault();
            var target = this.hash;
            var $target = $(target);
        
            $('html, body').stop().animate({
                'scrollTop': $target.offset().top
            }, 900, 'swing', function () {
                window.location.hash = target;
            });
        });
        
        $('a.page-scroll').bind('click', function(event) {
            var $anchor = $(this);
            $('html, body').stop().animate({
                scrollTop: $($anchor.attr('href')).offset().top
            }, 1500, 'easeInOutExpo');
            event.preventDefault();
        });
        
        $(".nav a").on("click", function(){
            $(".nav").find(".active").removeClass("active");
            $(this).parent().addClass("active");
        });

        $("#price-range").slider({
            tooltip: 'always'
        });

        $( "#viewed" ).click(function() {
            $( "#viewed-item" ).slideToggle( "slow" );
        });


        $("#mega-menu-carousel").carousel({
            interval: 10000,
            wrap:true
        });

         $(".carousel").carousel({
            interval: 10000,
            wrap:true
        });

        $(".carousel").on("slid", function() {
            var to_slide;
            to_slide = $(".carousel-item.active").attr("data-slide-no");
            $(".myCarousel-target.active").removeClass("active");
            $(".carousel-indicators [data-slide-to=" + to_slide + "]").addClass("active");
        });
        $(".myCarousel-target").on("click", function() {
            $(this).preventDefault();
            $(".carousel").carousel(parseInt($(this).attr("data-slide-to")));
            $(".myCarousel-target.active").removeClass("active");
            $(this).addClass("active");
        });



        $('#topTabCarousel').carousel({
            interval:   4000
        });
    
        var clickEventTop = false;
        $('#topTabCarousel').on('click', '.nav a', function() {
            clickEventTop = true;
            $('.nav li').removeClass('active');
            $(this).parent().addClass('active');
        }).on('slid.bs.carousel', function(e) {
            if(!clickEventTop) {
                var count = $('.nav').children().length -1;
                var current = $('.nav li.active');
                current.removeClass('active').next().addClass('active');
                var id = parseInt(current.data('slide-to'));
                if(count == id) {
                    $('.nav li').first().addClass('active');
                }
            }
            clickEventTop = false;
        });


        $('#bottomTabCarousel').carousel({
            interval:   4000
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



});
