    $('.checkbox :checkbox').rcSwitcher({

        inputs:false,
        onText: 'Yes',
        offText: 'No',
        theme: 'modern'

    });

    $('#setting').click(function(){
        $('.main-setting').toggle();
    })

    $('#settingMainClose').click(function(){
        $('.main-setting').fadeOut(3000);
    })


    $('.tm-accordion').on('click', '.accordion-title', function(){
        $(".tm-accordion-setting" ).hide();
        $(this).find( ".tm-accordion-setting" ).show();
    });


    $('.tm-accordion').on('click', 'a.setting-tab,dt.setting-tab', function(){

        var container = $(this).closest('.accordion-title').next();

        $('.setting-tab').removeClass('active');
        $(this).addClass('active');
        $('.setting-hidden').hide();
        container.find("."+$(this).data('ref')).show();
    });


function builderJs(){

    $("#facebookPageUrl").keydown(function(e) {

        var oldvalue=$(this).val();
        var field=this;
        setTimeout(function () {
            if(field.value.indexOf('https://facebook.com/') !== 0) {
                $(field).val(oldvalue);
            }
        }, 1);

    });

    $("#googlePlus").keydown(function(e) {

        var oldvalue=$(this).val();
        var field=this;
        setTimeout(function () {
            if(field.value.indexOf('https://plus.google.com/') !== 0) {
                $(field).val(oldvalue);
            }
        }, 1);

    });

    $("#twitterUrl").keydown(function(e) {

        var oldvalue=$(this).val();
        var field=this;
        setTimeout(function () {
            if(field.value.indexOf('https://twitter.com/') !== 0) {
                $(field).val(oldvalue);
            }
        }, 1);

    });


    jQuery.validator.addMethod("valDomain",function(name){

        var arr = new Array(
            '.com','.net','.org','.biz','.coop','.info','.museum','.name',
            '.pro','.edu','.gov','.int','.mil','.ac','.ad','.ae','.af','.ag',
            '.ai','.al','.am','.an','.ao','.aq','.ar','.as','.at','.au','.aw',
            '.az','.ba','.bb','.bd','.be','.bf','.bg','.bh','.bi','.bj','.bm',
            '.bn','.bo','.br','.bs','.bt','.bv','.bw','.by','.bz','.ca','.cc',
            '.cd','.cf','.cg','.ch','.ci','.ck','.cl','.cm','.cn','.co','.cr',
            '.cu','.cv','.cx','.cy','.cz','.de','.dj','.dk','.dm','.do','.dz',
            '.ec','.ee','.eg','.eh','.er','.es','.et','.fi','.fj','.fk','.fm',
            '.fo','.fr','.ga','.gd','.ge','.gf','.gg','.gh','.gi','.gl','.gm',
            '.gn','.gp','.gq','.gr','.gs','.gt','.gu','.gv','.gy','.hk','.hm',
            '.hn','.hr','.ht','.hu','.id','.ie','.il','.im','.in','.io','.iq',
            '.ir','.is','.it','.je','.jm','.jo','.jp','.ke','.kg','.kh','.ki',
            '.km','.kn','.kp','.kr','.kw','.ky','.kz','.la','.lb','.lc','.li',
            '.lk','.lr','.ls','.lt','.lu','.lv','.ly','.ma','.mc','.md','.mg',
            '.mh','.mk','.ml','.mm','.mn','.mo','.mp','.mq','.mr','.ms','.mt',
            '.mu','.mv','.mw','.mx','.my','.mz','.na','.nc','.ne','.nf','.ng',
            '.ni','.nl','.no','.np','.nr','.nu','.nz','.om','.pa','.pe','.pf',
            '.pg','.ph','.pk','.pl','.pm','.pn','.pr','.ps','.pt','.pw','.py',
            '.qa','.re','.ro','.rw','.ru','.sa','.sb','.sc','.sd','.se','.sg',
            '.sh','.si','.sj','.sk','.sl','.sm','.sn','.so','.sr','.st','.sv',
            '.sy','.sz','.tc','.td','.tf','.tg','.th','.tj','.tk','.tm','.tn',
            '.to','.tp','.tr','.tt','.tv','.tw','.tz','.ua','.ug','.uk','.um',
            '.us','.uy','.uz','.va','.vc','.ve','.vg','.vi','.vn','.vu','.ws',
            '.wf','.ye','.yt','.yu','.za','.zm','.zw');

        var mai = name;
        var val = true;

        var dot = mai.lastIndexOf(".");
        var dname = mai.substring(0,dot);
        var ext = mai.substring(dot,mai.length);
        //alert(ext);

        if(dot>2 && dot<57)
        {
            for(var i=0; i<arr.length; i++)
            {
                if(ext == arr[i])
                {
                    val = true;
                    break;
                }
                else
                {
                    val = false;
                }
            }
            if(val == false)
            {
                return false;
            }
            else
            {
                for(var j=0; j<dname.length; j++)
                {
                    var dh = dname.charAt(j);
                    var hh = dh.charCodeAt(0);
                    if((hh > 47 && hh<59) || (hh > 64 && hh<91) || (hh > 96 && hh<123) || hh==45 || hh==46)
                    {
                        if((j==0 || j==dname.length-1) && hh == 45)
                        {
                            return false;
                        }
                    }
                    else	{
                        return false;
                    }
                }
            }
        }
        else
        {
            return false;
        }
        return true;
        <!-- Script by hscripts.com -->

    }, 'Invalid domain name.');

    jQuery.validator.addMethod("valSubDomain",function(subdomain){

        if(!/^[a-z]{3,}$/.test(subdomain))
        {
            return false;
        }
    }, 'Invalid sub-domain name, please only name without .com, .info, .org etc');



    var validator =  $(".domainFrom").validate({

        rules: {

            "domain": {
                required: true,
                valDomain:true,
                remote: Routing.generate('bindu_signup_check_doamin')
            },

            "subDomain": {
                required: true,
                pattern: '^([a-z]{3,})$',
                remote: Routing.generate('bindu_signup_check_subdoamin')
            }

        },

        messages: {

            "domain":{
                required: "Enter valid domain name",
                remote: "This domain name is already registered."
            },

            "subDomain":{
                required: "Please enter your choosable sub domain name.",
                remote: jQuery.validator.format("{0} is already taken for another user.")
            },
        },
        submitHandler: function(form) {

            $.ajax({

                url: Routing.generate('bindu_build_option'),
                type: "POST",
                data: new FormData(form),
                processData: false,
                contentType: false,
                success: function(response) {
                    $('.ajax-loading').fadeOut(3000);
                    $('.main-setting').fadeOut(3500);
                },
                complete: function(){
                    location.reload();
                }
            });
        }

    });

    var validator =  $(".callEmail").validate({

        rules: {

            "mobile": {
                required: true,
                remote: Routing.generate('bindu_option_check_mobile')
            },

            "email": {
                required: true,
                email: true,
                remote: Routing.generate('bindu_option_check_email')
            }

        },

        messages: {

            "mobile":{
                required: "Enter valid mobile no",
                remote: jQuery.validator.format("{0} mobile no is already taken for another user.")
            },

            "email":{
                required: "Please valid email address",
                remote: jQuery.validator.format("{0} is already taken for another user.")
            },
        },
        submitHandler: function(form) {

            $.ajax({

                url: Routing.generate('bindu_build_option'),
                type: "POST",
                data: new FormData(form),
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('.ajax-loading').show().addClass('loading').fadeIn(3000);
                },
                success: function(response) {
                    location.reload();
                }
            });
        }

    });

    $('.timepicker').ptTimeSelect();

    $('.finish').submit( function( e ) {

        $.ajax({

            url: Routing.generate('bindu_build_finish'),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.ajax-loading').show().addClass('loading').fadeIn(3000);
            },
            success: function(response) {
                $('.ajax-loading').fadeOut(3000);
            },
            complete: function(){
                document.location.reload();
            }
        })
        e.preventDefault();

    });

    $('.theme-submitted').submit( function( e ) {

        $.ajax({

            url: Routing.generate('bindu_build_template'),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.ajax-loading').show().addClass('loading').fadeIn(3000);
            },
            success: function(response) {
                $('.ajax-loading').fadeOut(3000);
            }
        });

        e.preventDefault();

    });

    $('.home-submitted').submit( function( e ) {

        $.ajax({

            url: Routing.generate('bindu_build_homepage'),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.ajax-loading').show().addClass('loading').fadeIn(3000);
            },
            success: function(response) {
                document.getElementById('frame').contentDocument.location.reload(true);
                $('.ajax-loading').fadeOut(3000);
            }
        });

        e.preventDefault();

    });

    $('.customize-submitted').submit(function(e){

        $.ajax({

            url: Routing.generate('bindu_build_customize'),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.ajax-loading').show().addClass('loading').fadeIn(3000);
            },
            success: function(response) {
                document.getElementById('frame').contentDocument.location.reload(true);
                $('.ajax-loading').fadeOut(3000);
            }
        });
        e.preventDefault();

    });

    $('.footer-submitted').submit(function(e){

        $.ajax({

            url: Routing.generate('bindu_build_footer'),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.ajax-loading').show().addClass('loading').fadeIn(3000);
            },
            success: function(response) {
                document.getElementById('frame').contentDocument.location.reload(true);
                $('.ajax-loading').fadeOut(3000);
            }
        });
        e.preventDefault();


    });

    $('.about-us').submit(function(e){

        $.ajax({

            url: Routing.generate('bindu_build_about'),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            beforeSend: function(){
                $('.ajax-loading').show().addClass('loading').fadeIn(3000);
            },
            success: function(response){
                document.getElementById('frame').contentDocument.location.reload(true);
                $('.ajax-loading').fadeOut(3000);
            }
        });
        e.preventDefault();


    });

    $('.sitesetting').submit(function(e){

        $.ajax({

            url: Routing.generate('bindu_build_sitesetting'),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            beforeSend: function(){
                $('.ajax-loading').show().addClass('loading').fadeIn(3000);
            },
            success: function(response){
                document.getElementById('frame').contentDocument.location.reload(true);
                $('.ajax-loading').fadeOut(3000);
            }
        });
        e.preventDefault();

    });


    $('#callEmail').submit( function( e ) {

        $.ajax({
            url: Routing.generate('bindu_build_contact'),
            type: 'POST',
            data: new FormData( this ),
            processData: false,
            contentType: false,
            beforeSend: function(){
                $('.ajax-loading').show().addClass('loading').fadeIn(3000);
            },
            success: function(response){
                document.getElementById('frame').contentDocument.location.reload(true);
                $('.ajax-loading').fadeOut(3000);
            }
        });
        e.preventDefault();

    });

    $('.contact').submit( function( e ) {

        $.ajax({
            url: Routing.generate('bindu_build_contact'),
            type: 'POST',
            data: new FormData( this ),
            processData: false,
            contentType: false,
            beforeSend: function(){
                $('.ajax-loading').show().addClass('loading').fadeIn(3000);
            },
            success: function(response){
                document.getElementById('frame').contentDocument.location.reload(true);
                $('.ajax-loading').fadeOut(3000);
            }
        });
        e.preventDefault();

    });

    $('.socialMedia').submit( function( e ) {

        $.ajax({

            url: Routing.generate('bindu_build_adsTool'),
            type: 'POST',
            data: new FormData( this ),
            processData: false,
            contentType: false,
            beforeSend: function(){
                $('.ajax-loading').show().addClass('loading').fadeIn(3000);
            },
            success: function(response){
                document.getElementById('frame').contentDocument.location.reload(true);
                $('.ajax-loading').fadeOut(3000);
            }
        });
        e.preventDefault();

    });


    $("#district").change(function(){

        var district = $(this).val();
        $.ajax({
            url: Routing.generate('bindu_build_find_child'),
            type: "POST",
            data: 'district='+ district,
            beforeSend: function() {
                $('#child').show().addClass('inner-load').fadeIn(3000);
            }
        }).done(function(child){
            $('#child').html(child);
            $('#thana').select2();
        });

    });


    $("#logoUpload").change(function(){
        logoPreview(this);
    });

    function logoPreview(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#logoPreview').show().attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#bgImage").change(function(){
        bgImagePreview(this);
    });

    function bgImagePreview(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#bgImagePreview').show().attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#headerBgImage").change(function(){
        headerBgImagePreview(this);
    });

    function headerBgImagePreview(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#headerBgImagePreview').show().attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }


    $(".theme").click(function () {

        var id = $(this).attr('rel');
        $(".theme").removeClass('themeSelected');
        $("#theme-"+id).addClass('themeSelected');
        $('#themeId').val(id);

    });

    $('.socialMedia').submit( function( e ) {

        $.ajax({
            url: Routing.generate('bindu_build_option'),
            type: 'POST',
            data: new FormData( this ),
            processData: false,
            contentType: false,
            success: function(data){}
        });
        e.preventDefault();

    });

}

