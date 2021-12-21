(function($) {
    "use strict";
    /*-- Menu Sticky --*/
    var $window = $(window);

    $('.nav-tour-content a').on('click', function(){
        $('.nav-tour-content a').removeClass('active');
        $(this).addClass('active');
    })
    /*-- Mobile Menu -- */
    var $menu = $("#navbar-mobile");
    $menu.removeAttr( "id" );
    $menu.removeAttr( "class" );
    $menu.find( "[id]" ).removeAttr( "id" );
    $menu.find( "[class]" ).removeAttr( "class" );

    $menu.mmenu();

    var $icon = $("#mmenu");
    var API = $menu.data("mmenu");

    $icon.on( "click", function() {
       API.open();
    });

    /*-- WOW --new WOW().init();*/

    /*-- Nivo Slider -- 
    $('#home-slider').nivoSlider({
        directionNav: true,
        animSpeed: 1000,
        effect: 'random',
        slices: 18,
        pauseTime: 5000,
        pauseOnHover: true,
        controlNav: false,
        prevText: '<i class="pe-7s-angle-left"></i>',
        nextText: '<i class="pe-7s-angle-right"></i>'
    });
    */
    /*
    var $container = $('div.collection-wrap');
    $container.imagesLoaded(function () {
        $container.isotope({
            itemSelector: '.collection-item',
            layoutMode: 'packery',
            packery: {}
        }).isotope('layout');
    });
    $('div.collection-item .image').hoverdir({
        hoverElem: '.desc'
    });*/

    /*-- Slick Slider --*/
    $('.slick-deals').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        prevArrow: '<div class="arrow-prev"><i class="pe-7s-angle-left pe-7s-5x"></i></div>',
        nextArrow: '<div class="arrow-next"><i class="pe-7s-angle-right pe-7s-5x"></i></div>',
        responsive: [
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 3,
                }
            }, {
                breakpoint: 767,
                settings: {
                    slidesToShow: 2,
                }
            }, {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
    });

    /*-- Slick Slider --*/
    $('.slick-bottom').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        prevArrow: '<div class="arrow-prev"><i class="pe-7s-angle-left pe-7s-5x"></i></div>',
        nextArrow: '<div class="arrow-next"><i class="pe-7s-angle-right pe-7s-5x"></i></div>',
        responsive: [
            {
                breakpoint: 1199,
                settings: {
                    slidesToShow: 4,
                }
            }, {
                breakpoint: 991,
                settings: {
                    slidesToShow: 3,
                }
            }, {
                breakpoint: 767,
                settings: {
                    slidesToShow: 2,
                }
            }, {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
    });

    $('.slick-gallery').slick({
        autoplay: true,
		autoplaySpeed: 3000,
        slidesToShow: 1,
        slidesToScroll: 1,
        prevArrow: '<div class="arrow-prev"><i class="pe-7s-angle-left pe-7s-4x"></i></div>',
        nextArrow: '<div class="arrow-next"><i class="pe-7s-angle-right pe-7s-4x"></i></div>',
        responsive: [{
            breakpoint: 767,
            settings: {
                arrows: false,
            }
        },]
    });

    var countProImg = $('.slick-product-thumb img').length;
    $('.slick-product-image').slick({
        speed: 700,
        slidesToShow: 1,
        slidesToScroll: 1,
        prevArrow: '<div class="arrow-prev"><i class="pe-7s-angle-left pe-7s-4x"></i></div>',
        nextArrow: '<div class="arrow-next"><i class="pe-7s-angle-right pe-7s-4x"></i></div>',
        asNavFor: '.slick-product-thumb',
        responsive: [{
            breakpoint: 767,
            settings: {
                arrows: false,
            }
        },]
    });
    $('.slick-product-thumb').slick({
        asNavFor: '.slick-product-image',
        centerMode: countProImg > 3 ? true : false,
        centerPadding: '60px',
        speed: 700,
        slidesToShow: countProImg > 3 ? 3 : 4,
        slidesToScroll: 1,
        focusOnSelect: true,
        prevArrow: '<div class="arrow-prev"><i class="pe-7s-angle-left pe-7s-3x"></i></div>',
        nextArrow: '<div class="arrow-next"><i class="pe-7s-angle-right pe-7s-3x"></i></div>',
        responsive: [{
            breakpoint: 1199,
            settings: {
                centerPadding: '30px',
            }
        },{
            breakpoint: 767,
            settings: {
                arrows: false,
            }
        },]
    });

    $('.slick-product-other').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        prevArrow: '<div class="arrow-prev"><i class="pe-7s-angle-left pe-7s-5x"></i></div>',
        nextArrow: '<div class="arrow-next"><i class="pe-7s-angle-right pe-7s-5x"></i></div>',
        responsive: [{
            breakpoint: 991,
            settings: {
                slidesToShow: 3,
            }
        }, {
            breakpoint: 767,
            settings: {
                slidesToShow: 2,
            }
        }, {
            breakpoint: 479,
            settings: {
                slidesToShow: 1,
            }
        }]
    });

    /*-- Load Product tabs --*/
    $('.section-tabs').on('click', '.nav-link', function(e){
        e.preventDefault();
        $('.section-tabs .nav-link').removeClass('active');
        $('.section-tabs .tab-pane').removeClass('show active');
        var btn = $(this);
        btn.addClass('active');
        if( $('.section-tabs ' + btn.attr('href')).length > 0 ){
            $('.section-tabs ' + btn.attr('href')).addClass('show active');
            return false;
        }
        var dataAjax = btn.data('ajax').replace(/\|/g,'&')+'&_token='+Laravel.csrfToken;
        $.ajax({
            type: 'POST',
            url : Laravel.baseUrl+'/ajax',
            data: dataAjax,
            beforeSend: function(){

            }
        }).done(function(response){
            $('.section-tabs .tab-content').append(response.products);
        });
    });

    // $("#ex2").bootstrapSlider({
    //     tooltip: 'always',
    //     tooltip_split: true,
    //     formatter: function(value) {
    //         return value.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + ' đ';
    //     }
    // });

    $('.frm-filter input[type="checkbox"], .frm-filter input[type="radio"], .frm-filter select[name="order_by"]').on('change', function(){
        var dataAjax = $('.frm-filter').serialize()+'&act=filters&_token='+Laravel.csrfToken;
        $.ajax({
            type: 'POST',
            url : Laravel.baseUrl+'/ajax',
            data: dataAjax,
            beforeSend: function(){

            }
        }).done(function(response){
            $('.filter-result').html(response.products);
        });
    })

    $('#fast-search').on('click', function(e){
        e.preventDefault();
        $('#show-search').toggle();
    })
    
    /*-- Youtube Background Video 
    $(".youtube-bg").YTPlayer();
    /*-- Text Animation 
    $('.tlt').textillate({
        loop: true,
        in: {
            effect: 'fadeInRight',
        },
        out: {
            effect: 'fadeOutLeft',
        },
    });--*/

    /*-- ScrollUp --*/
    $.scrollUp({
        scrollText: '<i class="fa fa-angle-up"></i>',
        easingType: 'linear',
        scrollSpeed: 900,
        animation: 'fade'
    });
    var now = new Date().getTime();
    var targetPopup = localStorage.getItem("targetPopup");
    if( targetPopup <= now ){
        $('#modal-popup').modal('show').on('shown.bs.modal', function () {
            localStorage.clear()
            localStorage.setItem("targetPopup",now+3*24*60*60*1000);
        });
    }

    $window.on('load resize', function(){

        if( $window.width() <= 767 ){
            $('.sidebar-widget .title').on('click', function(){
                $(this).next().toggleClass('active');
            })
            $('.sidebar-widget .content').on('click', function(){
                $(this).toggleClass('active');
            })
        }


        $(".sticker").each(function(){
            $(this).attr('data-offset-top',$(this).offset().top + 300);
            $(this).attr('data-offset-limit',$($(this).attr('data-limit')).offset().top);
        });

        $window.on('scroll', function() {
            var scroll = $window.scrollTop();
            $(".sticker").each(function(){
                var top = $(this).attr('data-offset-top');
                var limit = $(this).attr('data-offset-limit');
                var destroy = $(this).attr('data-destroy');
                if( typeof destroy !== 'undefined' && $window.width() <= destroy ){
                    return false;
                }
                scroll += $(this).height();
                if( typeof top !== 'undefined' ){
                    if (scroll > top && scroll < limit ) {
                        $(this).css('width',$(this).innerWidth());
                        $(this).parent().css('height',$(this).height());
                        $(this).addClass("stick");
                    } else {
                        $(this).css('width','100%');
                        $(this).parent().css('height','auto');
                        $(this).removeClass("stick");
                    }
                }
            });
        });
    });

})(jQuery);
