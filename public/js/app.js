var App = function() {

    var isRTL = false;
    var isIE8 = false;
    var isIE9 = false;
    var isIE10 = false;

    var resizeHandlers = [];

    var handleInit = function() {

        if ($('body').css('direction') === 'rtl') {
            isRTL = true;
        }

        isIE8 = !!navigator.userAgent.match(/MSIE 8.0/);
        isIE9 = !!navigator.userAgent.match(/MSIE 9.0/);
        isIE10 = !!navigator.userAgent.match(/MSIE 10.0/);

        if (isIE10) {
            $('html').addClass('ie10');
        }

        if (isIE10 || isIE9 || isIE8) {
            $('html').addClass('ie');
        }
    };

    var _runResizeHandlers = function() {
        for (var i = 0; i < resizeHandlers.length; i++) {
            var each = resizeHandlers[i];
            each.call();
        }
    };

    var handleOnResize = function() {
        var resize;
        if (isIE8) {
            var currheight;
            $(window).resize(function() {
                if (currheight == document.documentElement.clientHeight) {
                    return;
                }
                if (resize) {
                    clearTimeout(resize);
                }
                resize = setTimeout(function() {
                    _runResizeHandlers();
                }, 50);
                currheight = document.documentElement.clientHeight;
            });
        } else {
            $(window).resize(function() {
                if (resize) {
                    clearTimeout(resize);
                }
                resize = setTimeout(function() {
                    _runResizeHandlers();
                }, 50);
            });
        }
    };

    var handleStripUnicode = function(str){
        str = str.toLowerCase();
        str = str.replace(/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/g, 'a');
        str = str.replace(/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/g, 'e');
        str = str.replace(/(ì|í|ị|ỉ|ĩ)/g, 'i');
        str = str.replace(/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/g, 'o');
        str = str.replace(/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/g, 'u');
        str = str.replace(/(ỳ|ý|ỵ|ỷ|ỹ)/g, 'y');
        str = str.replace(/(đ)/g, 'd');
        str = str.replace(/([^0-9a-z-\s])/g, '');
        str = str.replace(/(\s+)/g, '-');
        str = str.replace(/^-+/g, '');
        str = str.replace(/--+$/g, '-');
        return str;
    }

    var handleAlerts = function() {
        $('body').on('click', '[data-close="alert"]', function(e) {
            $(this).parent('.alert').hide();
            $(this).closest('.note').hide();
            e.preventDefault();
        });

        $('body').on('click', '[data-close="note"]', function(e) {
            $(this).closest('.note').hide();
            e.preventDefault();
        });

        $('body').on('click', '[data-remove="note"]', function(e) {
            $(this).closest('.note').remove();
            e.preventDefault();
        });
    };

    var handleTextareaAutosize = function() {
        if (typeof(autosize) == "function") {
            autosize(document.querySelector('textarea.autosizeme'));
        }
    }

    var handleFixInputPlaceholderForIE = function() {
        if (isIE8 || isIE9) {
            $('input[placeholder]:not(.placeholder-no-fix), textarea[placeholder]:not(.placeholder-no-fix)').each(function() {
                var input = $(this);

                if (input.val() === '' && input.attr("placeholder") !== '') {
                    input.addClass("placeholder").val(input.attr('placeholder'));
                }

                input.focus(function() {
                    if (input.val() == input.attr('placeholder')) {
                        input.val('');
                    }
                });

                input.blur(function() {
                    if (input.val() === '' || input.val() == input.attr('placeholder')) {
                        input.val(input.attr('placeholder'));
                    }
                });
            });
        }
    };
    var handleHeight = function() {
        $('[data-auto-height]').each(function() {
            var parent = $(this);
            var items = $('[data-height]', parent);
            var height = 0;
            var mode = parent.attr('data-mode');
            var offset = parseInt(parent.attr('data-offset') ? parent.attr('data-offset') : 0);

            items.each(function() {
                if ($(this).attr('data-height') == "height") {
                    $(this).css('height', '');
                } else {
                    $(this).css('min-height', '');
                }

                var height_ = (mode == 'base-height' ? $(this).outerHeight() : $(this).outerHeight(true));
                if (height_ > height) {
                    height = height_;
                }
            });

            height = height + offset;

            items.each(function() {
                if ($(this).attr('data-height') == "height") {
                    $(this).css('height', height);
                } else {
                    $(this).css('min-height', height);
                }
            });

            if(parent.attr('data-related')) {
                $(parent.attr('data-related')).css('height', parent.height());
            }
        });       
    }

    var handleToastr = function(){
        $('.tooltips').tooltip();
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "slideDown",
            "hideMethod": "fadeOut"
        };
        Waves.init({duration: 500, delay: 200});
        Waves.attach('.btn', ['waves-light']);
    }
    var handleCounterup = function() {
        if (!$().counterUp) {
            return;
        }

        $("[data-counter='counterup']").counterUp({
            delay: 5,
            time: 1500
        });
    };

    // Places
    var handlePlaces = function(){

        $('.simple-province').each(function(){
            var group = $(this).attr('data-group');
            var simpleProvince = $(this).attr('data-value');
            var simpleDistrict = $('.simple-district[data-group="'+group+'"]').attr('data-value');
            if(simpleProvince !='' && simpleProvince > 0){
                $(this).html(province[simpleProvince].name);
                $('.simple-district[data-group="'+group+'"]').length > 0 ? $('.simple-district[data-group="'+group+'"]').html(district[simpleProvince][simpleDistrict].name) : ''; 
            }
        });

        $('select.province').each(function(){
            var listProvince = [];
            var group = $(this).attr('data-group');
            var eleProvince = $(this);

            if( typeof eleProvince.attr('data-multiple') !== 'undefined' && eleProvince.attr('data-multiple') !='' ){
                var curProvince = eleProvince.attr('data-multiple');
            }else{
                var curProvince = eleProvince.val();
            }
            
            if( curProvince != null && curProvince.length > 0 ){
                curProvince = curProvince.split(',');
            }else{
                var curProvince = [];
            }

            var eleDistrict = $('select.district[data-group="'+group+'"]');
            var curDistrict = eleDistrict.val();
            $.each( province, function( key, val ) {
                listProvince.push('<option value="'+key+'" '+ ( $.inArray(key,curProvince) >=0 ? 'selected' : '' ) +'>'+val.name+'</option>');
            });
            eleProvince.html(listProvince.join(""));
            if(eleDistrict.length > 0){
                eleProvince.on('change', function(){
                    var province_id = $(this).val();
                    var listDistrict = [];
                    $.each( district[province_id], function( key, val ) {
                        listDistrict.push('<option value="'+key+'" '+ ( key == curDistrict ? 'selected' : '' ) +'>'+val.name+'</option>');
                    });
                    eleDistrict.html(listDistrict.join("")).selectpicker('refresh');
                }).change();
            }
        });

    }

    var handGetCookie = function (cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
        }
        return "";
    }

    var handleCart = function (){
        var countCart = $('.countCart');
        var sumCartPrice = $('.sumCartPrice');
        var sumOrderPrice = $('.sumOrderPrice');
        var miniCart = $('.mini-cart .cart-items');
        $.ajax({
            type: 'GET',
            url : Laravel.baseUrl+'/gio-hang/load',
        }).done(function(response){
            countCart.html(response.countCart);
            sumCartPrice.html(response.sumCartPrice);
            sumOrderPrice.html(response.sumOrderPrice);
            miniCart.html(response.miniCart);
        });

        $('body').on('click', '#add-to-cart', function(e){
            e.preventDefault();
            var btn = $(this);
            var qty = $('.product-quantity input').val();
            if( typeof qty === 'undefined' ) qty = 1;

            var color = $('.color-list .active').attr('data-id');
            if( typeof color === 'undefined' ) color = 0;

            var size = $('.size-list .active').attr('data-id');
            if( typeof size === 'undefined' ) size = 0;

            var dataAjax = btn.data('ajax').replace(/\|/g,'&')+'&qty='+qty+'&color='+color+'&size='+size+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/gio-hang/add',
                data: dataAjax,
                beforeSend: function(){
                    btn.button('loading');
                }
            }).done(function(response){
                btn.button('reset');
                location.href=Laravel.baseUrl+"/gio-hang";
                // countCart.html(response.countCart);
                // sumCartPrice.html(response.sumCartPrice);
                // sumOrderPrice.html(response.sumOrderPrice);
                // miniCart.html(response.miniCart);
                // toastr[response.type](response.message, response.title);
            });
        });

        $('body').on('click', '.add-to-cart', function(e){
            e.preventDefault();
            var btn = $(this);
            var qty = 1;
            var color = 0;
            var size = 0;
            var dataAjax = btn.data('ajax').replace(/\|/g,'&')+'&qty='+qty+'&color='+color+'&size='+size+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/gio-hang/add',
                data: dataAjax,
                beforeSend: function(){
                    btn.find('i').removeClass().addClass('fa fa-spinner fa-pulse');
                }
            }).done(function(response){
                btn.find('i').removeClass().addClass('fa fa-shopping-cart');
                countCart.html(response.countCart);
                sumCartPrice.html(response.sumCartPrice);
                sumOrderPrice.html(response.sumOrderPrice);
                miniCart.html(response.miniCart);
                toastr[response.type](response.message, response.title);
            });
        });

        $('body').on('click', '.delete-cart', function(e){
            e.preventDefault();
            var btn = $(this);
            var dataAjax = btn.data('ajax').replace(/\|/g,'&')+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/gio-hang/delete',
                data: dataAjax,
                beforeSend: function(){
                }
            }).done(function(response){
                if(response.type == 'success'){
                    $('.pro-key-'+response.key).remove();
                }
                countCart.html(response.countCart);
                sumCartPrice.html(response.sumCartPrice);
                sumOrderPrice.html(response.sumOrderPrice);
                miniCart.html(response.miniCart);
                if( response.coupon.length > 0 ){
                    App.alert({
                        container: $('#result-coupon'),
                        place: 'append',
                        type: response.coupon.type,
                        message: response.coupon.message,
                        close: true,
                        reset: true,
                        focus: false,
                        closeInSeconds: 0,
                        icon: response.coupon.icon
                    });
                }
                toastr[response.type](response.message, response.title);
            });
        });

        $('body').on('change', '.update-cart', function(e){
            e.preventDefault();
            var btn = $(this);
            var qty = btn.val();
            var dataAjax = btn.data('ajax').replace(/\|/g,'&')+'&qty='+qty+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/gio-hang/update',
                data: dataAjax,
                beforeSend: function(){
                }
            }).done(function(response){
                $('.pro-key-'+response.key+' .sumProPrice').html( response.sumProPrice );
                countCart.html(response.countCart);
                sumCartPrice.html(response.sumCartPrice);
                sumOrderPrice.html(response.sumOrderPrice);
                miniCart.html(response.miniCart);
                if( response.coupon.length > 0 ){
                    App.alert({
                        container: $('#result-coupon'),
                        place: 'append',
                        type: response.coupon.type,
                        message: response.coupon.message,
                        close: true,
                        reset: true,
                        focus: false,
                        closeInSeconds: 0,
                        icon: response.coupon.icon
                    });
                }
                toastr[response.type](response.message, response.title);
            });
        });

        /*-- Product Quantity --*/
        $('.product-quantity').append('<span class="dec qtybtn"><i class="fa fa-angle-left"></i></span><span class="inc qtybtn"><i class="fa fa-angle-right"></i></span>');
        $('.qtybtn').on('click', function() {
            var $button = $(this);
            var $input = $button.parent().find('input');
            var oldValue = $input.val();

            if ($button.hasClass('inc')) {
                var newVal = parseFloat(oldValue) + 1;
            } else {
                if (oldValue == 1) return;
                // Don't allow decrementing below zero
                if (oldValue > 0) {
                    var newVal = parseFloat(oldValue) - 1;
                } else {
                    newVal = 0;
                }
            }
            $input.val(newVal);
            $input.trigger('change');
        });

        $('.cart-coupon').on('click', 'button', function(e){
            e.preventDefault();
            var $button = $(this);
            var $input = $('.cart-coupon').find('input');

            if( $input.val() === ''){
                App.alert({
                    container: $('#result-coupon'),
                    place: 'append',
                    type: 'danger',
                    message: 'Bạn chưa nhập mã coupon',
                    close: true,
                    reset: true,
                    focus: false,
                    closeInSeconds: 5,
                    icon: 'warning'
                });
                return false;
            }

            var dataAjax = 'code='+$input.val()+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/gio-hang/coupon',
                data: dataAjax,
                beforeSend: function(){
                }
            }).done(function(response){
                if(response.type == 'success'){
                    sumOrderPrice.html(response.sumOrderPrice);
                }
                App.alert({
                    container: $('#result-coupon'),
                    place: 'append',
                    type: response.type,
                    message: response.message,
                    close: true,
                    reset: true,
                    focus: false,
                    closeInSeconds: 0,
                    icon: response.icon
                });
            });
        });

        $('.color-list').on('click', 'button', function(){
            var $button = $(this);
            if ($button.hasClass('active')) {
                $button.removeClass('active');
                return;
            }
            $('.color-list button').removeClass('active');
            $button.addClass('active');
        });

        $('.size-list').on('click', 'button', function(){
            var $button = $(this);
            if ($button.hasClass('active')) {
                $button.removeClass('active');
                return;
            }
            $('.size-list button').removeClass('active');
            $button.addClass('active');
        });
    }

    var handleWishList = function(){
        $('body').on('click', '.add-to-wishlist', function(e){
            e.preventDefault();
            var btn = $(this);
            var dataAjax = btn.data('ajax').replace(/\|/g,'&')+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/wishlist/add',
                data: dataAjax,
                beforeSend: function(){
                    btn.button('loading');
                }
            }).done(function(response){
                btn.button('reset');
                toastr[response.type](response.message, response.title);
            });
        });

        $('body').on('click', '.delete-wishlist', function(e){
            e.preventDefault();
            var btn = $(this);
            var dataAjax = btn.data('ajax').replace(/\|/g,'&')+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/wishlist/delete',
                data: dataAjax,
                beforeSend: function(){
                }
            }).done(function(response){
                if(response.type == 'success'){
                    $('.pro-key-'+response.key).remove();
                }
                toastr[response.type](response.message, response.title);
            });
        });
    }

    var handleComment = function(){
        $('.comment-list').on('click','.reply', function(e){
            e.preventDefault();
            var btn = $(this);
            var container = btn.closest('.timeline-wrap');
            if( container.find('.comment-form').length > 0 ) return false;
            var parentID = parseInt(btn.attr('data-parent'));
            var productID = parseInt(btn.attr('data-product'));
            var postID = parseInt(btn.attr('data-post'));
            // var form = $('.comment-form.main-form').clone().removeClass('main-form').addClass('display-hide');
            var form = $('<form action="#" method="post" class="comment-form display-hide">'+
                    '<input type="hidden" name="score" value="1">'+
                    '<input type="hidden" name="parent" value="'+parentID+'">'+
                    '<input type="hidden" name="product_id" value="'+productID+'">'+
                    '<input type="hidden" name="post_id" value="'+postID+'">'+
                    '<div class="form-group"><textarea name="description" class="form-control" rows="6"></textarea></div>'+
                    '<div class="form-group"><button type="submit" class="btn btn-success btn-ajax" data-ajax="act=reply|type=default"> Trả lời </button></div>'+
                '</form>');

            $('.comment-list .comment-form').slideUp('fast', function(){
                $(this).remove();
            });
            form.appendTo(container);
            form.slideDown('fast', function(){
                App.scrollTo(form);
            });
        });
        $('.comment-form .rating .fa-star').hover(function(e){
            var rate = $(this).attr('data-rate');
            for(var i=0; i<=4; i++){
                if(i<rate) $('.comment-form .rating .fa').eq(i).addClass('active');
                else $('.comment-form .rating .fa').eq(i).removeClass('active');
            }
        }, function(e){
            $('.comment-form .rating .fa').removeClass('active');
        });

        $('.comment-form .rating').on('click', '.fa-star', function(e){
            var rate = $(this).attr('data-rate');
            $('.comment-form input[name="score"]').val(rate);
            for(var i=0; i<=4; i++){
                if(i<rate) $('.comment-form .rating .fa').eq(i).addClass('selected');
                else $('.comment-form .rating .fa').eq(i).removeClass('selected');
            }
        });
    }

    var handleRegister = function(){
        $('body').on('click', '.btn-ajax', function(e){
            e.preventDefault();
            var btn = $(this);
            var frm = btn.parents('form');
            var dataAjax = frm.serialize()+'&'+btn.data('ajax').replace(/\|/g,'&')+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/ajax',
                data: dataAjax,
                beforeSend: function(){
                    btn.button('loading');
                }
            }).done(function(response){
                btn.button('reset');
                if( typeof response.redirect !== 'undefined'){
                    window.location.href=response.redirect;
                }
                if(response.type == 'success'){
                    frm.find('*:not([type="hidden"])').val('');
                }
                App.alert({
                    container: frm,
                    place: 'prepend',
                    type: response.type,
                    message: response.message,
                    close: true,
                    reset: true,
                    focus: false,
                    closeInSeconds: 5,
                    icon: response.icon
                });
                if( typeof response.remove_element !== 'undefined'){
                    frm.find('*:not(.alert, .fa, .close)').remove();
                }
                if( typeof response.close_modal !== 'undefined'){
                    setTimeout(function() { $('.modal').modal('hide'); }, 3000);
                }
            });
        });

        $('body').on('click', '.btn-login', function(e){
            e.preventDefault();
            var btn = $(this);
            var frm = btn.parents('form');
            var dataAjax = frm.serialize()+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/login',
                data: dataAjax,
                beforeSend: function(){
                    btn.button('loading');
                }
            }).fail(function(status){
                App.alert({
                    container: frm,
                    place: 'prepend',
                    type: 'danger',
                    message: status.responseJSON[Object.keys(status.responseJSON)[0]],
                    close: true,
                    reset: true,
                    focus: false,
                    closeInSeconds: 5,
                    icon: 'warning'
                });
            }).done(function(response){
                if( typeof response.redirect !== 'undefined'){
                    window.location.href=response.redirect;
                }
                App.alert({
                    container: frm,
                    place: 'prepend',
                    type: response.type,
                    message: response.message,
                    close: true,
                    reset: true,
                    focus: false,
                    closeInSeconds: 5,
                    icon: response.icon
                });
            }).always(function(){
                btn.button('reset');
            });
        });

        $('body').on('click', '.btn-forgot', function(e){
            e.preventDefault();
            var btn = $(this);
            var frm = btn.parents('form');
            var dataAjax = frm.serialize()+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/password/email',
                data: dataAjax,
                beforeSend: function(){
                    btn.button('loading');
                }
            }).fail(function(status){
                App.alert({
                    container: frm,
                    place: 'prepend',
                    type: 'danger',
                    message: status.responseJSON[Object.keys(status.responseJSON)[0]],
                    close: true,
                    reset: true,
                    focus: false,
                    closeInSeconds: 5,
                    icon: 'warning'
                });
            }).done(function(response){
                if( typeof response.redirect !== 'undefined'){
                    window.location.href=response.redirect;
                }
                App.alert({
                    container: frm,
                    place: 'prepend',
                    type: response.type,
                    message: response.message,
                    close: true,
                    reset: true,
                    focus: false,
                    closeInSeconds: 5,
                    icon: response.icon
                });
            }).always(function(){
                btn.button('reset');
            });
        });

        $('body').on('click', '.btn-register', function(e){
            e.preventDefault();
            var btn = $(this);
            var frm = btn.parents('form');
            var dataAjax = frm.serialize()+'&_token='+Laravel.csrfToken;
            $.ajax({
                type: 'POST',
                url : Laravel.baseUrl+'/register',
                data: dataAjax,
                beforeSend: function(){
                    btn.button('loading');
                }
            }).fail(function(status){
                App.alert({
                    container: frm,
                    place: 'prepend',
                    type: 'danger',
                    message: status.responseJSON[Object.keys(status.responseJSON)[0]],
                    close: true,
                    reset: true,
                    focus: false,
                    closeInSeconds: 5,
                    icon: 'warning'
                });
            }).done(function(response){
                if(response.type == 'success'){
                    frm.find('*:not([type="hidden"])').val('');
                }
                App.alert({
                    container: $('#ajax-modal-login form'),
                    place: 'prepend',
                    type: response.type,
                    message: response.message,
                    close: true,
                    reset: true,
                    focus: false,
                    closeInSeconds: 5,
                    icon: response.icon
                });
                $('#ajax-modal-register').modal('hide').on('hidden.bs.modal', function (e) {
                    $('#ajax-modal-login').modal('show');
                });
            }).always(function(){
                btn.button('reset');
            });
        });
    }

    var handleSearch = function(){
        $('.search-form').on('keyup', '.typeahead', function(e){
            var btn = $(this);

            if(btn.val().length < 3 ){
                $('.search-result').html('<ul><li><a href="javascript:;">Vui lòng nhập hơn 3 ký tự</a></li></ul>').fadeIn(100);
                btn.focus();
                return false;
            }
            
            clearTimeout($.data(this, 'timer'));
            var wait = setTimeout(function(){
                $.ajax({
                    type: 'GET',
                    url : Laravel.baseUrl+'/tim-kiem/'+btn.val(),
                    beforeSend: function(){
                        $('.search-result').fadeOut(100);
                    }
                }).done(function(response){
                    if( e.keyCode == 13 ){
                        window.location.href=Laravel.baseUrl + '/tim-kiem/' + handleStripUnicode(btn.val());
                        return false;
                    }
                    $('.search-result').html(response.data);
                }).always(function(){
                    $('.search-result').fadeIn(100);
                });
            }, 500);
            btn.data('timer', wait);
        });

        $('.search-form').on('focusout', '.typeahead', function(e){
            $('.search-result').fadeOut(1000);
        });

        $('.search-form').on('click', 'button[type="submit"]', function(e){
            var btn = $('.typeahead');

            if(btn.val().length < 3 ){
                $('.search-result').html('<ul><li><a href="javascript:;">Vui lòng nhập hơn 3 ký tự</a></li></ul>').fadeIn(100);
                btn.focus();
                return false;
            }

            window.location.href=Laravel.baseUrl + '/tim-kiem/' + handleStripUnicode(btn.val());
            return false;
        });
    }
    
    //* END:CORE HANDLERS *//

    return {
        init: function() {
            handleInit();
            handleOnResize();
            handleAlerts();
            this.addResizeHandler(handleHeight);

            handleFixInputPlaceholderForIE();
            handleToastr();
            handleCounterup();
            handlePlaces();
            handleCart();
            handleWishList();
            handleComment();
            handleRegister();
            handleSearch();
        },

        initAjax: function() {
        },

        initComponents: function() {
            this.initAjax();
        },

        addResizeHandler: function(func) {
            resizeHandlers.push(func);
        },

        runResizeHandlers: function() {
            _runResizeHandlers();
        },

        alert: function(options) {

            options = $.extend(true, {
                container: "",
                place: "append",
                type: 'success',
                message: "",
                close: true,
                reset: true,
                focus: true,
                closeInSeconds: 0,
                icon: ""
            }, options);

            var id = App.getUniqueID("App_alert");

            var html = '<div id="' + id + '" class="custom-alerts alert alert-' + options.type + ' fade show">' + (options.close ? '<button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>' : '') + (options.icon !== "" ? '<i class="fa-lg fa fa-' + options.icon + '"></i>  ' : '') + options.message + '</div>';

            if (options.reset) {
                $('.custom-alerts').remove();
            }

            if (!options.container) {
                if ($('.page-fixed-main-content').length === 1) {
                    $('.page-fixed-main-content').prepend(html);
                } else if (($('body').hasClass("page-container-bg-solid") || $('body').hasClass("page-content-white")) && $('.page-head').length === 0) {
                    $('.page-title').after(html);
                } else {
                    if ($('.page-bar').length > 0) {
                        $('.page-bar').after(html);
                    } else {
                        $('.page-breadcrumb, .breadcrumbs').after(html);
                    }
                }
            } else {
                if (options.place == "append") {
                    $(options.container).append(html);
                } else {
                    $(options.container).prepend(html);
                }
            }

            if (options.focus) {
                App.scrollTo($('#' + id));
            }

            if (options.closeInSeconds > 0) {
                setTimeout(function() {
                    $('#' + id).remove();
                }, options.closeInSeconds * 1000);
            }

            return id;
        },

        getURLParameter: function(paramName) {
            var searchString = window.location.search.substring(1),
                i, val, params = searchString.split("&");

            for (i = 0; i < params.length; i++) {
                val = params[i].split("=");
                if (val[0] == paramName) {
                    return unescape(val[1]);
                }
            }
            return null;
        },

        isTouchDevice: function() {
            try {
                document.createEvent("TouchEvent");
                return true;
            } catch (e) {
                return false;
            }
        },

        getUniqueID: function(prefix) {
            return 'prefix_' + Math.floor(Math.random() * (new Date()).getTime());
        },

        getResponsiveBreakpoint: function(size) {
            var sizes = {
                'xs' : 480,
                'sm' : 768,
                'md' : 992,
                'lg' : 1200
            };

            return sizes[size] ? sizes[size] : 0; 
        }
    };

}();

$(document).ready(function() {
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    App.init();
});