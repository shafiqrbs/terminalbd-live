/*
* @By Vi Quang Hoa
* @Mobile 0983.50.99.56
* @Email vyquanghoa@gmail.com
* @Website http://viquanghoa.com
* @Yahoo vyquanghoa
* @Skype vyquanghoa
* @Facebook https://www.facebook.com/vyquanghoa
* @Created on Oct 22, 2014, 2:45:40 PM
*/

(function ($) {
    $.fn.vSidebar = function (options) {
        var defaults = {
            'items': null,
            'position': 'right bottom',
            'horizontalMargin': 200, //horizontal magrin
            'verticalMargin': 0, //vertical margin
        };
        var options = $.extend(defaults, options);

        return this.each(function () {
            this.id = 'vsidebar-' + $.guid;
            $.guid++;

            var that = $(this), ul = $('<ul>');
            that.addClass('vsidebar').append(ul);

            if (options.items !== null) {
                $.each(options.items, function (k, v) {
                    ul.append(
                            $('<li>').append(
                            $('<a>').attr({'href': v.link,'id': v.id,'class': v.class}).append(
                            $('<span>').addClass('icon').append(
                            $('<img>').attr({'src': v.iconUrl})
                            ),
                            $('<span>').addClass('txt').html(v.label)
                            )
                            )
                            );
                });
            }
            that.appendTo('body');

            $('li', ul).on('mouseover mouseleave', function (e) {
                if (e.type === 'mouseover') {
                    $('.txt', this).stop(true, true).animate({'width': 'show'}, 200);
                } else {
                    $('.txt', this).delay(50).animate({'width': 'hide'}, 200);
                }
            });

        });
    };
})(jQuery);