(function($){
    $(document).ready(function(){

        $('body').prepend($("<div id='slideout-container'></div>"));

        $('#slideout-container').append($('.main-menu-container').clone()).append($('.utility-container').clone());

        $('#slideout-container .menu-item--expanded > a').click(function(e){
            e.preventDefault();
            e.stopPropagation();
            $(this).parent().toggleClass('opened');
        });
        $('.field--name-field-contextual-boxes > .field__items ').masonry({
            // options
        });

        var dykcookie = Cookies.get('didyouknow-closed');
        if (typeof dykcookie == 'undefined' || dykcookie != 1 ){
            $('#block-didyouknow').removeClass('closed');
        }

        $('#block-didyouknow .close').click(function(){
            $('#block-didyouknow').addClass('closed');
            Cookies.set('didyouknow-closed','1', {expires: 7});
        });

        $('.membership-levels-benefits .view-content').masonry({
            // options
            itemSelector: '.views-row',
            columnWidth: '.views-row',
            percentPosition: true
        });

        var slideout = new Slideout({
            'panel': $('.layout-container')[0],
            'menu': $('#slideout-container')[0],
            'padding': 256,
            'tolerance': 70,
            'side':'right'
        });
        $('.block-views-blocknews-news-all').on('click','.custom-view-filters .category-tabs li a', function(e){
            //e.preventDefault();
            $('.news-all .views-exposed-form .js-form-item-field-news-item-type-target-id select').val($(e.target).parent().attr('data-value'));
            var search_val = $('.custom-view-filters .search .search-text');
            $('.news-all .views-exposed-form .js-form-item-combine input').val(search_val.val());
            $('.news-all .views-exposed-form .form-submit').click();
        });

        $('.block-views-blocknews-news-all').on('click','.custom-view-filters .filter-submit-btn', function(e){
            e.preventDefault();
            var filters = $(e.target).closest('.custom-view-filters');
            var category_id = $(filters).find('.category-tabs li.active').attr('data-value');
            $('.news-all .views-exposed-form .js-form-item-field-news-item-type-target-id select').val(category_id);
            var search_val = $('.custom-view-filters .search .search-text');
            $('.news-all .views-exposed-form .js-form-item-combine input').val(search_val.val());
            $('.news-all .views-exposed-form .form-submit').click();
        });


        $('.block-views-blockevents-event-list-block').on('click','.custom-view-filters .events-intervals-tabs li', function(e){
            e.preventDefault();

            var min_date = '';
            var max_date = '';
            var now = moment();
            var featured = "All";
            switch(parseInt($(e.target).attr('data-value'))){
                case -1:
                    featured = "1";
                    min_date = moment().add(-1, 'y').startOf('year').format('YYYY-MM-DD HH:mm:ss');
                    max_date = moment().add(1,'y').endOf('day').format('YYYY-MM-DD HH:mm:ss');
                break;
                case 0:
                    min_date = moment().startOf('day').format('YYYY-MM-DD HH:mm:ss');
                    max_date = moment().endOf('day').format('YYYY-MM-DD HH:mm:ss');
                break;
                case 1:
                    min_date = moment().add(1,'d').startOf('day').format('YYYY-MM-DD HH:mm:ss');
                    max_date = moment().add(1,'d').endOf('day').format('YYYY-MM-DD HH:mm:ss');
                break;
                case 7:
                    min_date = moment().startOf('day').format('YYYY-MM-DD HH:mm:ss');
                    max_date = moment().add(1,'w').startOf('week').endOf('day').format('YYYY-MM-DD HH:mm:ss');
                break;
                case 31:
                    min_date = moment().startOf('day').format('YYYY-MM-DD HH:mm:ss');
                    max_date = moment().endOf('month').endOf('day').format('YYYY-MM-DD HH:mm:ss');
                break;
                case 1000:
                  min_date = moment().startOf('day').format('YYYY-MM-DD HH:mm:ss');
                  max_date = moment().add(2, 'years').format('YYYY-MM-DD HH:mm:ss');
                break;
            }
            $('.events-all .views-exposed-form .js-form-item-field-date-value-min input').val(min_date);
            $('.events-all .views-exposed-form .js-form-item-field-date-value-max input').val(max_date);
            var search_val = $('.custom-view-filters .search .search-text');
            $('.events-all .views-exposed-form .js-form-item-search-events input').val(search_val.val());
            $('.events-all .views-exposed-form select[name="field_featured_value"]').val(featured);

            $('.events-all .views-exposed-form .form-submit').click();

            $(e.target).parent().find('.active').removeClass('active');
            $(e.target).addClass('active');
        });

        $('.block-views-blockevents-event-list-block').on('click','.custom-view-filters .filter-submit-btn', function(e){
            e.preventDefault();

            var filters = $(e.target).closest('.custom-view-filters');
            var interval_value = $(filters).find('.events-intervals-tabs li.active').attr('data-value');
            var min_date = '';
            var max_date = '';
            var featured = "All";

            switch(parseInt(interval_value)){
              case -1:
                featured = "1";
                min_date = moment().add(-1, 'y').startOf('year').format('YYYY-MM-DD HH:mm:ss');
                max_date = moment().add(1,'y').endOf('day').format('YYYY-MM-DD HH:mm:ss');
                break;
              case 0:
                min_date = moment().startOf('day').format('YYYY-MM-DD HH:mm:ss');
                max_date = moment().endOf('day').format('YYYY-MM-DD HH:mm:ss');
                break;
              case 1:
                min_date = moment().add(1,'d').startOf('day').format('YYYY-MM-DD HH:mm:ss');
                max_date = moment().add(1,'d').endOf('day').format('YYYY-MM-DD HH:mm:ss');
                break;
              case 7:
                min_date = moment().startOf('day').format('YYYY-MM-DD HH:mm:ss');
                max_date = moment().add(1,'w').startOf('week').endOf('day').format('YYYY-MM-DD HH:mm:ss');
                break;
              case 31:
                min_date = moment().startOf('day').format('YYYY-MM-DD HH:mm:ss');
                max_date = moment().endOf('month').endOf('day').format('YYYY-MM-DD HH:mm:ss');
                break;
              case 1000:
                min_date = moment().startOf('day').format('YYYY-MM-DD HH:mm:ss');
                max_date = moment().add(2, 'years').format('YYYY-MM-DD HH:mm:ss');
              break;
            }
            var now = moment();
            $('.events-all .views-exposed-form .js-form-item-field-date-value-min input').val(min_date);
            $('.events-all .views-exposed-form .js-form-item-field-date-value-max input').val(max_date);
            var search_val = $('.custom-view-filters .search .search-text');
            $('.events-all .views-exposed-form .js-form-item-search-events input').val(search_val.val());
            $('.events-all .views-exposed-form select[name="field_featured_value"]').val(featured);

            $('.events-all .views-exposed-form .form-submit').click();
        });

        if ($('.block-views-blockevents-event-list-block').length > 0){
            $('.block-views-blockevents-event-list-block .custom-view-filters .events-intervals-tabs li:first ').trigger('click');
        }

        $('.custom-view-filters .category-tabs li, .custom-view-filters .events-intervals-tabs li').click(function(e){
            $(e.target).parent().addClass('active');
            $(e.target).parent().siblings().removeClass('active');
        });


        $('.region-header').prepend('<span class="toggle-mobile-menu"><span class="icon"><span class="bar"></span><span class="bar"></span><span class="bar"></span></span><span class="text">Menu</span></span>');
        $('body').on('click', '.toggle-mobile-menu', function(){
            slideout.toggle();
        });
        /*$("#pick-event-date").daterangepicker({
            datepickerOptions: {
                numberOfMonths: 1
            }
        });*/
        if ($('.pick-event-date-container').length > 0){
            pickmeup('.pick-event-date-container',{
                flat:true,
                mode:'range',
                render:  function (date) {

                    var date_value = new Date(date);
                    var date_string = (date_value.getMonth() + 1) + '-' + date_value.getDate() + '-' + date_value.getFullYear();
                    return {class_name : 'date_'+date_string};

                }
            });

            $('.pick-event-date-container').on('pickmeup-fill', function(e){
                //$('.pmu-button').wrapInner('<span class="day-wrapper"></span>');
                $('.pmu-days .pmu-button').each(function(){
                    if ($(this).children('.day-wrapper').length == 0){
                        $(this).wrapInner('<span class="day-wrapper"></span>');
                    }
                    var btn_classes = $(this).attr('class');
                    //console.log(btn_classes);
                    var carray = btn_classes.split(' ');
                    for(var i = 0; i < carray.length; i++){
                        var didx = carray[i].indexOf('date_');
                        if (didx !== -1){
                            //console.log(new Date(carray[i].substring(didx+5)));
                            var btn_date = new Date(carray[i].substring(didx+5));
                            $(this).attr('data-date', btn_date.toISOString());
                        }
                    }
                });
                $('.event-date-tag').each(function(){
                    var date_value = new Date($(this).attr('data-date'));
                    var date_string = (date_value.getMonth() + 1) + '-' + date_value.getDate() + '-' + date_value.getFullYear();
                    $('.date_'+date_string).addClass('has-events');
                });

                var selected_elements = $('.pmu-days .pmu-selected');
                $(selected_elements).first().addClass('first-selected');
                $(selected_elements).last().addClass('last-selected');

            });
            pickmeup('.pick-event-date-container').update();
        }




        $('.pmu-days .pmu-button').each(function(){
            if ($(this).children('.day-wrapper').length == 0){
                $(this).wrapInner('<span class="day-wrapper"></span>');
            }
            //$(this).children('.day-wrapper').length
        });
        //$('.pmu-button').wrapInner('<span class="day-wrapper"></span>');
        $('.filter-events-btn').click(function(e){
            var event_range = pickmeup('.pick-event-date-container').get_date();

            var min_date = moment(event_range[0]).startOf('day').format('YYYY-MM-DD HH:mm:ss');
            var max_date = moment(event_range[1]).endOf('day').format('YYYY-MM-DD HH:mm:ss');
            $('.events-all .views-exposed-form .js-form-item-field-date-value-min input').val(min_date);
            $('.events-all .views-exposed-form .js-form-item-field-date-value-max input').val(max_date);
            var search_val = $('.custom-view-filters .search .search-text');
            $('.events-all .views-exposed-form .js-form-item-search-events input').val(search_val.val());
            $('.events-all .views-exposed-form .form-submit').click();
        });

        $('.faq-container').accordion({
            collapsible: true,
            active: false,
            heightStyle: "content"
        });

        /*/var hpPlayer = false;
        videojs('#homepage-main-video').ready(function(){
            hpPlayer = videojs('#homepage-main-video');
        });*/
        var player = false;
        $('.watch-video-hp').click(function(){

            $.featherlight('#video-lightbox',{
                afterContent: function (event) {
                    console.log("open");
                    $('.featherlight-inner').append($('#homepage-main-video'));
                    console.log("DOM added");
                },
                afterOpen: function (event) {
                    if (player === false){
                        player = videojs('homepage-main-video', {"controls": true, "autoload":true, "preload": "meta_data"}, function () {
                            console.log('Good to go!');
                            player.play();
                            // if you don't trust autoplay for some reason
                        });
                    } else{
                        $('.featherlight-inner').append($('#homepage-main-video'));
                        console.log(player);
                        player.play();
                    }

                },
                beforeClose: function (event) {
                    $('.field--name-field-header-video .video-container').append($('#homepage-main-video'));
                    player.pause();
                    //player.dispose();

                }});

            /*videojs('#homepage-main-video').ready(function(){
                hpPlayer = videojs('#homepage-main-video');
                hpPlayer.play();

            });*/

            /*if (hpPlayer.paused()){
                hpPlayer.play();
            } else {
                hpPlayer.pause();
            }*/
        });

        $('.field--name-field-image-gallery .field__item a').featherlightGallery({
            openSpeed: 300
        });

        var owl_homepage_hero = $('.field--name-field-hero-slides.field__items');
        if (owl_homepage_hero.children().length > 1){
            owl_homepage_hero.addClass('owl-carousel').addClass('owl-theme');
            owl_homepage_hero.owlCarousel({
                items: 1,
                dots: false,
                nav:true,
                navText: [
                "<span class='icon-chevron-left icon-white'>‹</span>",
                "<span class='icon-chevron-right icon-white'>›</span>"
                ],
                margin:0,
                itemsElement: '.views-row',
                loop:true,
                animateIn: 'fadeIn',
                animateOut: 'fadeOut',
                autoplay: true,
                autoplaySpeed: 4000
            });
        }

        // $('.exhibit-hero').on('click', 'img', function(){
        //   if ($(this).attr('src').indexOf('thm_50_hero_image_1920_600_web_wht-01') > 0) {
        //     window.location.href="https://www.thehealthmuseum.org/event/50th-anniversary-john-p-mcgovern-museum-health-and-medical-science";
        //     return;
        //   }
        //   if ($(this).attr('src').indexOf('ogk_homepage_banner_1920wx980h-02_0_0') > 0) {
        //     window.location.href="https://www.thehealthmuseum.org/exhibit/our_global_kitchen";
        //     return;
        //   }
        // });


        $('.field--name-field-header-video-cover').attr('data-midnight','blue');

        $('.addtoany_list a').addClass('bgck_target');
        if ($('.bgck_target').length > 0)
        {
            BackgroundCheck.init({
                targets: '.bgck_target',
                windowEvents: true,
                debug:true
            });
            BackgroundCheck.refresh();
        }


        var news_url = window.location.href;
        var news_tab = news_url.substring(news_url.indexOf("#")+1);
        if (typeof news_tab != 'undefined' && news_tab.length > 0){
            //console.log(news_tab);
            $('.custom-view-filters .category-tabs li').each(function(e){
                if ($(this).attr('data-slug') == news_tab){
                    console.log('active');
                    $(this).addClass('active');
                    $(this).siblings().removeClass('active');

                    $('.news-all .views-exposed-form .js-form-item-field-news-item-type-target-id select').val($(this).attr('data-value'));
                    var search_val = $('.custom-view-filters .search .search-text');
                    $('.news-all .views-exposed-form .js-form-item-combine input').val(search_val.val());
                    $('.news-all .views-exposed-form .form-submit').click();
                }
            });
        }

        $('.form-type-select select').select2({
            minimumResultsForSearch: Infinity
        });

        $(window).on('load', function(){
            if ($("#homepage-main-video").length > 0){
                player = videojs('homepage-main-video', {"controls": true, "autoload":true, "preload": "meta_data"}, function () {
                    //console.log('Good to go!');
                    $('.field--name-field-header-video').addClass('loaded');
                    player.play();
                    // if you don't trust autoplay for some reason
                });
            }
        });
    });


    jQuery(document).ajaxComplete(function(event, xhr, settings) {
            //console.log(settings);
            // see if it is from our view
            if (settings.data.indexOf( "display_id=news_all") != -1) {
                $('.block-views-blocknews-news-all div div .custom-view-filters').remove();
            }
            if (settings.data.indexOf( "view_display_id=event_list_block") != -1) {
                $('.block-views-blockevents-event-list-block div div .custom-view-filters').remove();
            }
        });
})(jQuery);
