jQuery(document).ready(function ($) {
    $('.vp-dialog-close').on('click', function () {
        $('.vp-wishlist-dialog').slideUp('slow');
    });


    $("#vp-wishlist-" + vp_data.button_id).addClass("active");

    $('.vp_wishlist_add').on('click', function () {
        var toggle = $(this).hasClass('active');
        var product_id = $(this).attr('data-post-id');
        var this_elem = $(this);

        var stop = false;

        if (stop == false && $(this).hasClass('vp_wishlist_login')) {

            $.ajax({
                url: vp_data.adminurl,
                type: 'POST',
                data: {
                    action: 'wishlist_trigger',
                    post_id: product_id,
                    type: 'update'
                },
                beforeSend: function () {
                    stop = true;
                },
                success: function (json) {
                    var count;
                    console.log();
                    if (json.redirect_url != undefined) {
                        return window.location.href = json.redirect_url
                    }
                    if (json.current === true) {
                        this_elem.addClass('active');

                        count = this_elem.find('.vp-wishlist-count').text();
                        count = Number(count);
                        count = count + 1;
                        this_elem.find('.vp-wishlist-count').text(count);

                        $('.vp-wishlist-dialog').slideDown();
                    } else {

                        count = this_elem.find('.vp-wishlist-count').text();
                        count = Number(count);
                        count = count - 1;

                        if (count < 1) {
                            this_elem.find('.vp-wishlist-count').addClass('.vp-wishlist-count-null');
                        }

                        this_elem.find('.vp-wishlist-count').text(count);

                        this_elem.removeClass('active');
                    }
                },
                complete: function () {
                    stop = false;
                }
            });
        }

    });

    $('.vp_wishlist_remove').on('click', function () {
        var product_id = $(this).attr('remove-id');
        var parent_height = $(this).parent().height();
        var stop = false;
        if (stop == false) {
            if ($(this).hasClass('vp-wishlist-undo')) {
                $(this).siblings().fadeIn();
                var this_elem = $(this);
                $.ajax({
                    url: vp_data.adminurl,
                    type: 'POST',
                    data: {
                        action: 'wishlist_trigger',
                        post_id: product_id,
                        type: 'update'
                    },
                    beforeSend: function () {
                        stop = true;
                    },
                    success: function (json) {
                        this_elem.removeClass('vp-wishlist-undo');
                        this_elem.children().remove();
                        this_elem.addClass('icon-cross vp-wishlist-remove-cross');
                    },
                    complete: function () {
                        stop = false;
                    }

                });


            } else {
                $(this).siblings().fadeOut('fast');

                $(this).parent().css('height', parent_height);
                $(this).addClass('vp-wishlist-undo');
                $('.vp-wishlist-undo').css('top', parent_height / 3);
                $(this).removeClass('icon-cross vp-wishlist-remove-cross');
                $(this).html('<div class ="vp-wishlist-undo-icon icon-undo"><br>Undo?</div>');

                $.ajax({
                    url: vp_data.adminurl,
                    type: 'POST',
                    data: {
                        action: 'wishlist_trigger',
                        post_id: product_id,
                        type: 'remove'
                    },
                    beforeSend: function () {
                        stop = true;
                    },
                    success: function (json) {
                    },
                    complete: function () {
                        stop = false;
                    }
                });
            }

        }
    })
});