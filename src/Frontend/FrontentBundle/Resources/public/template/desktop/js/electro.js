// Departments menu Height
var $departments_menu_dropdown = $( '.departments-menu-dropdown' ),
    departments_menu_dropdown_height = $departments_menu_dropdown.height();

$departments_menu_dropdown.find( '.dropdown > .dropdown-menu' ).each( function() {
    $(this).find( '.menu-item-object-static_block' ).css( 'min-height', departments_menu_dropdown_height + 24 );
    $(this).css( 'min-height', departments_menu_dropdown_height + 28 );
});

$( '.vertical-menu, .departments-menu-dropdown' ).on( 'mouseleave', function() {
    var $this = $(this);
    $this.removeClass( 'animated-dropdown' );
});

$( '.vertical-menu .menu-item-has-children, .departments-menu-dropdown .menu-item-has-children' ).on({
    mouseenter: function() {
        var $this = $(this),
            $dropdown_menu = $this.find( '.dropdown-menu' ),
            $vertical_menu = $this.parents( '.vertical-menu' ),
            $departments_menu = $this.parents( '.departments-menu-dropdown' ),
            css_properties = {
                width:      540,
                opacity:    1
            },
            animation_duration = 300,
            has_changed_width = true,
            animated_class = '',
            $container = '';

        if ( $vertical_menu.length > 0 ) {
            $container = $vertical_menu;
        } else if ( $departments_menu.length > 0 ) {
            $container = $departments_menu;
        }

        if ( $this.hasClass( 'yamm-tfw' ) ) {
            css_properties.width = 540;

            if ( $departments_menu.length > 0 ) {
                css_properties.width = 600;
            }
        } else if ( $this.hasClass( 'yamm-fw' ) ) {
            css_properties.width = 900;
        } else if ( $this.hasClass( 'yamm-hw' ) ) {
            css_properties.width = 450;
        } else {
            css_properties.width = 277;
        }

        $dropdown_menu.css( {
            visibility: 'visible',
            display:    'block'
        } );

        if ( ! $container.hasClass( 'animated-dropdown' ) ) {
            $dropdown_menu.animate( css_properties, animation_duration, function() {
                $container.addClass( 'animated-dropdown' );
            });
        } else {
            $dropdown_menu.css( css_properties );
        }
    }, mouseleave: function() {
        $(this).find( '.dropdown-menu' ).css({
            visibility: 'hidden',
            opacity:    0,
            width:      0,
            display:    'none'
        });
    }
});