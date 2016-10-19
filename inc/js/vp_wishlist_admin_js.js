jQuery(document).ready(function($) {

    $('input[name="vp_wishlist_color_icon"]').on('keyup', function() {
        var wishlist_color = $(this).val();
        if (wishlist_color == '') {
            $('.wishlist-icon').css('color', 'red');
        } else {
            $('.wishlist-icon').css('color', wishlist_color);
        }
    });

    function vp_rating_check() {
        if ($('select[name="vp_wishlist_position_icon"]').val() == 'woocommerce_after_shop_loop_item_title+6') {
            $('.vp_wishlist_position_message').html('<b>Make sure product rating is on.</b>');
        } else {
            $('.vp_wishlist_position_message').html('');
        }
    }
    vp_rating_check();
    $('select[name="vp_wishlist_position_icon"]').change(function() {
        vp_rating_check()
    });

     $(".vp-wishlist-pages-info").hover(function(){
        $('.vp-pages-info').fadeIn('slow');
        }, function(){
        $('.vp-pages-info').css("display", "none");
    });


});