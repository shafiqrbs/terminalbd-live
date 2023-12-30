/*
 Title: Simple and easy Cookie based jQuery Tabs Plugin
 Author: Amol Nirmala Waman
 URL: http://amolnw.wordpress.com/
 Relese: Version 1.0, Wednesday, 13 April 2011
 Location: http://cookie-based-jquery-tabs.googlecode.com/files/cookie-based-jquery-tabs-1.0.js
 */

$(function(){
    // added multi usable standard javascript nyHashTabs()
    function nyHashTabs(){
        var Cookie = $.cookie("nyacord"); // this will set the cookie 'nyacord'
        var activeTab = '';
        var navIndex = '';
        $('.tab_content').hide(); // hides all content

        // check if 'noacord' cookie is exist, if not then show the content of first anchor
        if(!Cookie){
            $(".tabs a:first").addClass("active").show();
            $(".tab_content:first").show();

            // check if 'Cookie' is not empty
        } else if (Cookie != "") {
            $('.tabs > a:eq('+ Cookie +')').addClass('active').next().show(); // setting cookie for first anchor link
            activeTab = $('.tabs > a:eq('+ Cookie +')').attr("href"); // getting content for first set cookie
            $(activeTab).fadeIn(0); // 0 is the fastest

            // if 'noacord' cookie does not exist then show the content of first anchor
        }

        $(".tabs > a").click(function() {
            $(".tabs a").removeClass("active"); // removes 'active' class from all anchors in '.tabs'
            $(this).addClass("active"); // current tab will be 'active'
            navIndex = $('.tabs > a').index(this); // check the index
            $.cookie("nyacord", navIndex); // set the index for cookie

            $('.tab_content').hide();
            activeTab = $(this).attr("href"); // the active tab + content
            $(activeTab).fadeIn(0);
            return false;
        });
    }

    $('#profileTabList').each(function(){
        return nyHashTabs(); // applies to this ID only
    });
}); // jquery ends