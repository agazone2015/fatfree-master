/*
* Specifically used for menu pages
*/
$(document).on('click', '.menuItem', function (event) {
    $('#menuBar').find('.active').removeClass('active');
    $(this).addClass('active');
    var categoryId = $(this).attr('data-id');
    $.ajax({
        url: 'menu/items/',
        type: 'post',
        data: {cafeId: cafeId, categoryId: categoryId},
        success: function (result) {
            console.log('result:', result);
            if (result.success) {
                var records = result.records;
                $('#menuList').html('');
                for (var i = 0; i < records.length; i++) {
                    $('#menuList').append('<li class="item">' +
                        '<span class="itemHeader">' + records[i].itemName +
                            (records[i].isPopular ? '<i class="fa fa-star" title="Recommended Dish"></i>' : '') +
                            (records[i].isVegetarian ? '<img src="ui/img/icons/vegetarian.png" title="Vegetarian/Optional"/>' : '') +
                        '</span>' +
                        '<span class="itemDescription">'+records[i].description +'</span>' +
                        '<span class="itemPrice">$'+records[i].price.format(2,3) +'</span>' +
                    '</li>');
                }
                $('#menuListContainer').perfectScrollbar('update');
            }
        }
    })
});

var cafeId       = $('#menu').attr('data-id');
var currentIndex = 0;
var carousel     = $('.carousel').first();
var sliderWidth  = carousel.width();
var sliderHeight = carousel.height();

//carousel.width(totalLenth * sliderWidth);
carousel.find('.carouselItem:last-child').prependTo(carousel);

function moveRight () {
    carousel.animate({
        left: -sliderWidth
    }, 200, function () {
        carousel.find('.carouselItem:first-child').appendTo(carousel);
        carousel.css('left', '');
    });
}

function moveLeft () {
    carousel.animate({
        left: +sliderWidth
    }, 200, function () {
        carousel.find('.carouselItem:last-child').prependTo(carousel);
        carousel.css('left', '');
    });
}

/**
 * Number.prototype.format(n, x, s, c)
 *
 * @param integer n: length of decimal
 * @param integer x: length of whole part
 * @param mixed   s: sections delimiter
 * @param mixed   c: decimal delimiter
 */
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

$(function() {
    $('#menuBar').find('.menuItem').first().addClass('active');
    $('#carouselContainer').append('<button class="carouselBtn" id="prevItem"><i class="fa fa-chevron-left"></i></button><button class="carouselBtn" id="nextItem"><i class="fa fa-chevron-right"></i></button>')
    $('#menuListContainer').perfectScrollbar();

    $(document).on('click', '.carouselItem', function (e) {
        $.colorbox({
            html : '<img src="'+e.target.getAttribute('data-link')+'"/>'
        });
    })
    .on('click', '.carouselBtn', function (e) {
        // If click on previous button
        if ($(this)[0].id.indexOf('prev') !== -1) {

            moveLeft();
        }
        // Else click on next button
        else {
            moveRight();
        }
    });

});
