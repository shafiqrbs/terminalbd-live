(function($) {
	"use strict";

	/**
	 * debouncing function from John Hann
	 * http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
	 */
	var debounce = function (func, threshold, execAsap) {
		var timeout;

		return function debounced () {
			var obj = this, args = arguments;
			function delayed () {
				if (!execAsap)
					func.apply(obj, args);
				timeout = null;
			};

			if (timeout)
				clearTimeout(timeout);
			else if (execAsap)
				func.apply(obj, args);

			timeout = setTimeout(delayed, threshold || 100);
		};
	};

	/**
	 * Register smartresize plugin
	 */
	$.fn['smartresize'] = function(fn){
		return fn ? this.bind('resize', debounce(fn)) : this.trigger('smartresize');
	};


	/**
	 * Initialize masonry layout with blog style
	 * and mosaic style
	 * 
	 * @return  void
	 */
	var init_layout = function () {
		var
		blog_layout = $('.blog-masonry'),
		mosaic_layout = $('.masonry'),
		init_blog_layout = function () {
			blog_layout.masonry({
				itemSelector : '.item',
			});
		},
		init_mosaic_layout = function () {
			mosaic_layout.masonry({
				itemSelector: 'article.item',
				isResizeBound: false,
				isInitLayout: false
			});
		};

		// Initialize blog layout
		if (blog_layout.size() != 0) {
			$(window).on('load', init_blog_layout);
		}

		if (mosaic_layout.size() != 0) {
			var refresh_layout = function () {
				$('article.item', mosaic_layout).each(function () {
					$(this).height($(this).width());
					$('.tm-content-inner', this).height($(this).width());
				});

				mosaic_layout.masonry('layout');
			}

			$(window).on('load', init_mosaic_layout);
			$(window).on('load', refresh_layout);
			$(window).smartresize(refresh_layout);
		}
	};

	/**
	 * Initialize sticky navigator
	 * 
	 * @return  void
	 */
	var init_sticky_nav = function () {
		var
		nav = $('.tm-sticky-menu');

		if (nav.size() != 0) {
			var
			offset_top = nav.offset().top,
			nav_height = nav.height(),
			place_holder = $('<div />', {
				height: nav_height
			});

			// Inject place holder for the nav
			place_holder.hide()
			nav.after(place_holder);

			$(window).on('load scroll', function () {
				if ($(window).scrollTop() > offset_top) {
					nav.addClass('tm-fixed');
					place_holder.show();
				}
				else {
					place_holder.hide();
					nav.removeClass('tm-fixed');
				}
			});

			$(window).smartresize(function () {
				offset_top = nav.offset().top;
			});
		}
	};

	var init_portfolio = function () {
		var
		grid = $('#tm-portfolio .tm-content-isotope'),
		filters = $('#tm-portfolio .tm-filter a'),
		active = $('#tm-portfolio .tm-filter a.selected');

		if (grid.size() != 0) {
			$(window).on('load', function () {
				grid.isotope({
					filter: '*',
					animationOptions: {
						duration: 750,
						easing: 'linear',
						queue: false,
					}
				});

				filters.on('click', function (e) {
					e.preventDefault();

					// Trigger isotope to filter data
					grid.isotope({
						filter: $(this).attr('data-filter')
					});

					// Remove current active class
					active.removeClass('selected');

					// Assign active object to clicked element
					active = $(this);
					active.addClass('selected');
				});
			});
		}
	};

	var init_switch_buttons = function () {
		var
		buttons = $('a.button-switch');
		buttons.on('click',function(e) {
			buttons.removeClass('active');
			$(this).addClass('active');

			if ($(this).hasClass('grid')) {
				$('#tm-blog')
					.removeClass('tm-blog-style-list')
					.addClass('tm-blog-style-grid');
			}
			else if($(this).hasClass('list')) {
				$('#tm-blog')
					.removeClass('tm-blog-style-grid')
					.addClass('tm-blog-style-list');
			}
		});
	};

	var init_tabs = function () {
		$('.tm-tabs > ul a').on('click', function (e) {
			e.preventDefault();

			var
			elm = $(this),
			parent = elm.closest('.tm-tabs'),
			tab_page = $(elm.attr('href'), parent);

			parent.find('> ul a').removeClass('selected');
			parent.find('.tab-content').hide();

			elm.addClass('selected');
			tab_page.fadeIn();
		});

		$('.tm-tabs > ul li:first-child a').trigger('click');
	};

	var init_accordions = function () {
		var acc_wrapper = $('.tm-accordion');
		if (acc_wrapper.length > 0) 
		{
			$('.tm-accordion .accordion-container').hide();
			$.each(acc_wrapper, function(index, item){
				$(this).find($('.accordion-title')).first().addClass('active').next().show();
				
			});
			$('.accordion-title').on('click', function(e) {
				if( $(this).next().is(':hidden') ) {
					$(this).parent().find($('.active')).removeClass('active').next().slideUp(300);
					$(this).toggleClass('active').next().slideDown(300);
				}
				e.preventDefault();
			});
		}
	};

	var init_progress_bar = function () {
		$(".tm-progress-bar-inner").each(function() {
			$(this).data("origWidth", ($(this).width() / $(this).parent().width()) * 100)
			  .width(0)
			  .animate({ width: $(this).data("origWidth") + "%" }, 1200);
		});
	};

	var init_animate_scroll = function () {
		$(window).on('load scroll', function () {
			$('.setanimate').each(function() {
				this.getBoundingClientRect().top < $(window).height()
					? $(this).addClass('visible')
					: $(this).removeClass('visible');
			});
		});
	};

	var init_countdown = function () {
		var 
		simple_style = function (data) {
			$(this.el).html( + this.leadingZeros(data.years, 4) + " <span>years</span>" + this.leadingZeros(data.days, 3) + " <span>days</span>" + this.leadingZeros(data.hours, 2) + " <span>hrs</span>" + this.leadingZeros(data.min, 2) + " <span>min</span>" + this.leadingZeros(data.sec, 2) + " <span>sec</span>");
		},
		boxed_style = function (data) {
			$(this.el).html("<div>" + this.leadingZeros(data.years, 4) + " <span>years</span></div><div>" + this.leadingZeros(data.days, 3) + " <span>days</span></div><div>" + this.leadingZeros(data.hours, 2) + " <span>hrs</span></div><div>" + this.leadingZeros(data.min, 2) + " <span>min</span></div><div>" + this.leadingZeros(data.sec, 2) + " <span>sec</span></div>");
		};

		$('.tm-countdown').each(function () {
			$(this).countdown({
				date: $(this).attr('data-endtime'),
				render: $(this).hasClass('simple') ? simple_style : boxed_style
			});
		});
	};

	var init_contact_form = function () {
		if ($('.contact-form form').size() != 0) {
			$('.contact-form form').validate({
				submitHandler: function (frm) {
					var $form = $(frm);

					$.ajax($form.attr('action'), {
						type: 'POST',
						data: $form.serializeArray(),
						dataType: 'JSON',
						beforeSend: function () {
							$form.find(':input').attr('disabled', 'disabled');
							$form.find('.tm-alert').remove();
							$form.addClass('form-sending');
						},
						success: function (response) {
							$form.prepend(
								$('<div />', {
									'class': 'tm-alert ' + response.status,
									'text' : response.content
								}).append(
									$('<a class="close" href="javascript:void(0)"><i class="fa fa-times"></i></a>')
								)
							);
							$form.find(':input').val('');
						},
						complete: function (xhr, status, error_thrown) {
							$form.find(':input').removeAttr('disabled');
							$form.removeClass('form-sending');
						}
					});
				}
			});
		}
	};

	var init_newsletter = function () {
		$('form.newsletter').on('submit', function (e) {
			var form = $(this);
			e.preventDefault();


			$.ajax(form.attr('action'), {
				type: 'POST',
				data: form.serializeArray(),
				dataType: 'JSON',
				beforeSend: function () {
					form.find(':input').attr('disabled', 'disabled');
					form.find('.tm-alert').remove();
					form.addClass('form-sending');
				},
				success: function (response) {
					form.prepend(
						$('<div />', {
							'class': 'tm-alert ' + response.status,
							'text' : response.content
						}).append(
							$('<a class="close" href="javascript:void(0)"><i class="fa fa-times"></i></a>')
						)
					);
					form.find(':input').val('');
				},
				complete: function (xhr, status, error_thrown) {
					form.find(':input').removeAttr('disabled');
					form.removeClass('form-sending');
				}
			});
		});
	};

	// DOMReady event
	$(function () {
		init_layout();
		init_sticky_nav();
		init_portfolio();
		init_switch_buttons();
		init_tabs();
		init_accordions();
		init_progress_bar();
		init_animate_scroll();
		init_countdown();
		init_contact_form();
		init_newsletter();

		// Gotop button
		$('#tm-gotop').on('click', function(e) {
			e.preventDefault();
			$("html, body").animate({ scrollTop: 0 }, 1000);
		});
		$(window).on('load scroll', function() {
			$(window).scrollTop() > 0
				? $('#tm-gotop').addClass('tm-visible')
				: $('#tm-gotop').removeClass('tm-visible');
		});

		// Tiny NAV
		$('.tm-menu-simple').tinyNav({
			active: 'current-menu-item'
		});

		// Pretty photo
		$("a[data-rel^='prettyPhoto']").prettyPhoto({hook: 'data-rel'});

		// Flex slider
		$('.flexslider').flexslider({
			animation: 'fade',
			prevText: '<i class="fa fa-angle-left"></i>',
			nextText: '<i class="fa fa-angle-right"></i>',
		});

		// Flex slider with carousel style
		$('.gallery-carousel').flexslider({
			animation: "slide",
			animationLoop: false,
			itemWidth: 200,
			itemMargin: 10,
			minItems: 2,
			maxItems: 4,
			prevText: '<i class="fa fa-angle-left"></i>',
			nextText: '<i class="fa fa-angle-right"></i>',
		});

		// Flex slider with thumbnail navigator
		$('#tm-carousel').flexslider({
			animation: "slide",
			controlNav: false,
			animationLoop: false,
			slideshow: false,
			itemWidth: 75,
			itemMargin: 5,
			asNavFor: '#tm-slider',
			prevText: '<i class="fa fa-angle-left"></i>',
			nextText: '<i class="fa fa-angle-right"></i>',
		});
		
		$('#tm-slider').flexslider({
			animation: "slide",
			controlNav: false,
			animationLoop: false,
			slideshow: false,
			sync: "#tm-carousel",
			prevText: '<i class="fa fa-angle-left"></i>',
			nextText: '<i class="fa fa-angle-right"></i>',
		});

		// Dismiss button for message box
		$(".tm-alert a.close").live('click', function(){
			$(this).closest(".tm-alert").fadeOut();
		});

		// Goto top button
		$('.tm-to-top').click(function () {
			$('body,html').animate({
				scrollTop: 0 
			}, 800); 
			return false;
		});

		// Parallax background
		$('.tm-parallax[data-type="background"]').each(function(){
			var $bgobj = $(this); // assigning the object

			$(window).scroll(function() {						   
				var yPos = -($(window).scrollTop() / $bgobj.data('speed')); 
				var coords = '50%'+ (yPos + 0) + 'px';
				$bgobj.css({ backgroundPosition: coords });
			}); // window scroll Ends
		});

		// Modal
		$(function(){
			$('#loginform').submit(function(e){
			    return false;
			});
						  
			$('.tm-modal > a').leanModal({ top: 110, overlay: 0.8, closeButton: ".hidemodal" });
		});
	});

	// Woocommerce - Filter by price
	$(function() {
		$( ".price_slider" ).slider({
			range: true,
			min: 0,
			max: 100,
			values: [ 0, 75 ],
			slide: function( event, ui ) {
				$( ".price_label > input " ).val( "€" + ui.values[ 0 ] + " - €" + ui.values[ 1 ] );
				}
		});

		$( ".price_label > input " ).val( "€" + $( ".price_slider" ).slider( "values", 0 ) +
		" - €" + $( ".price_slider" ).slider( "values", 1 ) );
	});

	// Woocommerce - Spinner
	$(function() {
		$( "#spinner" ).spinner({
			spin: function( event, ui ) {
				if ( ui.value > 10 ) {
					$( this ).spinner( "value", -10 );
					return false;
				} else if ( ui.value < -10 ) {
					$( this ).spinner( "value", 10 );
					return false;
				}
			}
		});
	});
})(jQuery);