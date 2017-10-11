jQuery(document).ready(function() {

    jQuery('.call-popup-letter').click(function() {
        jQuery('.modal').modal({
            onOpen: function (dialog) {
                dialog.overlay.fadeIn(300, function () {
                    dialog.container.fadeIn(300)
                    dialog.data.fadeIn(300);
                });
            },
            minWidth:520,
            minHeight: 355,
            overlayClose:true
        });
        jQuery('.popup-letter').show();

    });


    /*if(!$.cookie('dismissed_subscribe')) {
     jQuery('.popup-letter').modal({
     onOpen: function (dialog) {
     dialog.overlay.fadeIn(300, function () {
     dialog.container.fadeIn(300)
     dialog.data.fadeIn(300);
     });
     },
     minWidth:500,
     minHeight: 335,
     overlayClose:true,
     onClose: function (dialog) {
     $.cookie('dismissed_subscribe', 'on', { expires: 365, path: '/'});
     $.modal.close();
     }
     });
     }*/

    $('.popup-letter .email').keypress(function(e) {
        if(e.which != 13) {
            jQuery(this).removeClass('error');
            jQuery('.error-msg').fadeOut('slow');
        }
    });

    jQuery('.popup-letter .email').focus(function() {
        jQuery('#pop_email_label').hide();
    });


    jQuery('.popup-letter .email').focusout(function() {
        if ( jQuery('.popup-letter .email').val() == 0 ) {
            jQuery('#pop_email_label').show();
        }
    });

    jQuery('.get-a-gift').hover( function(){
        jQuery(this).stop(true, true);
        jQuery(this).animate({ left: '+=116'}, 300);
    }, function(){
        jQuery(this).stop(true, true);
        jQuery(this).animate({ left: '-=116'}, 300);
    });

    jQuery(function() {
        jQuery('.scroll-link').bind('click',function(event){
            var $anchor = $(this);
            jQuery('html, body').stop().animate({
                scrollTop: $($anchor.attr('href')).offset().top
            }, 800,'jswing');
            event.preventDefault();
        });
    });


    jQuery('#foot_email').change( function() {
        var edata = $('#foot_email').val()
        jQuery('#pop_email_label').hide();
        jQuery('#pop_email').val(edata);
    })

    jQuery('.foot-optin .email').focus(function() {
        jQuery('.foot-optin label').hide();
    });

    jQuery('.foot-optin .email').focusout(function() {
        if ( jQuery('.foot-optin .email').val() == 0 ) {
            jQuery('.foot-optin label').show();
        }
    });


    //breadcrumbs

    jQuery('.breadcrumbs .current').click(function(){
        if (jQuery('.current .list').is(':hidden') || jQuery('.filters-block .filter-list').is(':visible') || jQuery('.geo-block .cities-list').is(':visible')) {
            jQuery(this).addClass('current-active').find('.list').show();
            jQuery('.filter').removeClass('current-active').find('.list').hide();
            jQuery('.geo-block').removeClass('geo-active').find('.cities-list').hide();
        }
        else {
            jQuery(this).removeClass('current-active').find('.list').hide();
        }
    });

    //filter-listing

    jQuery('.filters-block .filter').click(function(){
        if(jQuery(this).find('.list').is(':hidden')) {
            jQuery('.filters-block .filter').addClass('current-active').not(this).removeClass('current-active');
            jQuery('.filters-block .filter').find('.list').show();
            jQuery('.filters-block .filter').not(this).find('.list').hide();
            jQuery('.breadcrumbs .current').removeClass('current-active').find('.list').hide();
        }
        else {
            jQuery(this).removeClass('current-active').find('.list').hide();
        }
    });

    //city
    jQuery('.geo-container .geo-block').click(function(){
        if (jQuery('.geo-block .cities-list').is(':hidden')) {
            jQuery(this).addClass('geo-active').find('.cities-list').show();
        }
        else {
            jQuery(this).removeClass('geo-active').find('.cities-list').hide();
        }
    });

    //click out of blocks
    jQuery(document).click(function() {
        jQuery('.current, .filter').removeClass('current-active').find('.list').hide();
        jQuery('.geo-block').removeClass('geo-active').find('.cities-list').hide();

    });

    jQuery(document).keyup(function(e) {
        if (e.keyCode == 27) {
            jQuery('.current, .filter, .cur-city').removeClass('current-active').find('.list').hide();
            jQuery('.geo-block').removeClass('geo-active').find('.cities-list').hide();
        }
    });

    jQuery(".filter, .current, .geo-block").click(function(e) {
        e.stopPropagation();
    });



});





$(document).ready(function(){
    var options = {
        target: "#subscribe_error", // баЛаЕаМаЕаНб, аКаОбаОббаЙ аБбаДаЕб аОаБаНаОаВаЛаЕаН аПаО аОбаВаЕбб баЕбаВаЕбаА
        beforeSubmit: validate_email, // ббаНаКбаИб, аВбаЗбаВаАаЕаМаАб аПаЕбаЕаД аПаЕбаЕаДаАбаЕаЙ
        success: complete_subscribe, // ббаНаКбаИб, аВбаЗбаВаАаЕаМаАб аПбаИ аПаОаЛббаЕаНаИаИ аОбаВаЕбаА
        timeout: 3000 // баАаЙаМ-аАбб
    };

    $('#subscribe').submit(function() {
        $(this).ajaxSubmit(options);
        return false; //аВбаЕаГаДаА аВаОаЗаВбаАбаАаЕаМ false, ббаОаБб аПбаЕаДбаПбаЕаДаИбб аОбаПбаАаВаКб баОбаМб
    });

    $('#subscribe_foot').submit(function() {
        $('#subscribe').ajaxSubmit(options);
        return false; //аВбаЕаГаДаА аВаОаЗаВбаАбаАаЕаМ false, ббаОаБб аПбаЕаДбаПбаЕаДаИбб аОбаПбаАаВаКб баОбаМб
    });
});

// аВбаЗаОаВ аПаЕбаЕаД аПаЕбаЕаДаАбаЕаЙ аДаАаНаНбб
function validate_email(formData, jqForm, options) {

    $('#subscribe_error').html('<img src="/bitrix/templates/homeme/img/ajax-loader2.gif" border="0">');

    var email = $("#pop_email").val();
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if(!re.test(email)) {
        $(".error-msg").show();
        jQuery(".error-msg").text("абаИаБаКаА аВ аАаДбаЕбаЕ баЛаЕаКббаОаНаНаОаЙ аПаОббб");
        jQuery('#pop_email').addClass('error');
        return false;
    }


    return true;
}

// аВбаЗаОаВ аПаОбаЛаЕ аПаОаЛббаЕаНаИб аОбаВаЕбаА
function complete_subscribe(responseText, statusText) {
    if(responseText=='OK') {
        $(".error-msg").hide();
        jQuery('.optin-form').hide();

        jQuery('.optin-form').fadeOut('fast', function(){
            jQuery('.optin-success').fadeIn('fast'); //аПаОаКаАаЗбаВаАаЕаМ аБаЛаОаК баПаАбаИаБаО
            //jQuery('.optin-fail').fadeIn('fast'); аЕбаЛаИ баЖаЕ аЗаАбаЕаГаЕаН
        });
    } else {
        jQuery('#pop_email').addClass('error');
        $(".error-msg").show();
    }
}