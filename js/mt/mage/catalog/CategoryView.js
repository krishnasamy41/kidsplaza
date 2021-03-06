/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

jQuery(function(){
    //init image lazy load
    if (jQuery.fn.lazyload){
        jQuery('img.lazy').lazyload({
            event: 'scroll|widgetnav',
            failure_limit: 10
        });
    }
    //init category banner
    if (jQuery.fn.owlCarousel){
        jQuery('.catalog-root-banner').owlCarousel({
            itemsScaleUp: true,
            singleItem: true,
            autoPlay: true,
            navigation: false,
            pagination: false,
            transitionStyle: 'fadeUp'
        });
        jQuery('.owl-carousel').each(function(i, slider){
            var id = jQuery(slider).attr('id');
            if (!id || !window[id]) return;
            jQuery(slider).owlCarousel(window[id]);
        });
    }
    //init filter scroller && list
    jQuery('.block-layered-nav .panel-body').each(function(i, container){
        initLayerFilterWithScrollAndList(container);
    });
    //init equal height
    ensureEqualHeight();
    //init countdown
    initCountDown();
});

//set product grid height
setGridItemsEqualHeight();

function initCountDown(){
    jQuery('.groupon-timedown').countdown({
        dataAttr: "cdate",
        leadingZero: false,
        yearText: dataTranslate.year,
        monthText: dataTranslate.month,
        weekText: dataTranslate.weeks,
        dayText: dataTranslate.day,
        daySingularText: dataTranslate.day,
        hourSingularText: ':',
        minSingularText: ':',
        hourText: ':',
        minText: ':',
        secText: ''
    });
}

function ensureEqualHeight(){
    jQuery.fn.equalHeights && jQuery.fn.equalHeights('.col-main', '.col-left-bottom');
}

function initLayerFilterWithScrollAndList(container){
    if (jQuery(container).find('.filter-item').length > 15){
        jQuery(container).addClass('has-scroll').mCustomScrollbar({
            scrollInertia: 0,
            theme: 'dark-thin',
            set_height: 300,
            mouseWheel: true,
            callbacks: {
                onScrollReady: function(elm){
                    //scroll to selected element
                    elm.mCustomScrollbar('scrollTo', 'li.selected');
                    //init List
                    var container = elm.parent();
                    if (List && container.length){
                        new List(container.get(0), {
                            valueNames: ['filter-item-name', 'filter-item-name-normalize']
                        })
                        .on('updated', function(ins){
                            var list = jQuery(ins.list).parents('.mCustomScrollbar');
                            list.mCustomScrollbar('update');
                            if(ins.visibleItems.length > 15){
                                list.addClass('has-scroll');
                            }else{
                                list.removeClass('has-scroll');
                            }
                        });
                    }
                }
            }
        });
    }else{
        jQuery('div.list-js').each(function(i, list){
            new List(list, {
                valueNames: ['filter-item-name', 'filter-item-name-normalize']
            });
        });
    }
}