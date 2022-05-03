(function ($) {

    //kürzt die schreibweisen:

    const {
        select,                 //statt: www.data.select(...) jetzt nur: select(...)
        subscribe,              //...
        dispatch
    } = wp.data;


    const question = {

        alterDisplay: function () {

            $('div[class*="wp-block-lazyblock-reli-"] .lzb-content-title span').each(function (i) {

                let icon = $(this);
                let header = icon.closest('.lazyblock').find('.components-base-control__field').first();
                header.parent().parent().addClass('block-header');
                header.prepend(icon);
            })
        },

    }

    window.question = question;
    wp.hooks.addAction('lzb.components.PreviewServerCallback.onChange', 'bausteine', function (props) {

        question.alterDisplay();

    });
})(jQuery);