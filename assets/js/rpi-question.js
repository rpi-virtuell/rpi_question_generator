(function ($) {

    //kürzt die schreibweisen:

    const {
        select,                 //statt: www.data.select(...) jetzt nur: select(...)
        subscribe,              //...
        dispatch
    } = wp.data;


    const question = {

        onHelpIconClick: function (e) {
            console.log('click', e.target);
            let elem = $(e.target);
            if (!elem.hasClass('modal-helper')) {
                elem = elem.closest('.modal-helper');
            }
            const slug = elem.attr('data-slug');
            console.log('slug', slug);
            $.post(
                ajaxurl,
                {
                    action: 'getLeitfrage',
                    slug: slug,
                },
                function (response) {
                    tb_show('Hilfe', '#TB_inline?width=100%');
                    $('<div id="TB_content"></div>').insertBefore($('#TB_ajaxContent'));
                    $('#TB_content').html(response);
                    //$('#TB_ajaxContent').css({'height':0});
                    let ht = $('#TB_window').height()-30;
                    $('#TB_content').css({'max-height':ht+'px', 'overflow':'auto'});


                });

        },
        alterDisplay: function () {

            $('div[class*="wp-block-lazyblock-reli-"] .lzb-content-title span').each(function (i, elem) {

                let icon = $(elem);
                let header = icon.closest('.lazyblock').find('.components-base-control__field').first();
                header.parent().parent().addClass('block-header');
                header.prepend(icon);
            })
            $('div[class*="wp-block-lazyblock-reli-leitfragen"]').each(function (i, elem) {

                const parent = $(elem);
                const block = parent.closest('.wp-block').first();

                const slug = block.attr('data-type').replace('lazyblock/reli-leitfragen-', '');
                const id = block.attr('id');

                if (block.find('#modal-helper-' + id).length === 0) {
                    const icon = '<div id="modal-helper-' + id + '" class="modal-helper" data-slug="' + slug + '" title="Weitergehende Hilfen"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 18h2v-2h-2v2zm1-16C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2c0 2-3 1.75-3 5h2c0-2.25 3-2.5 3-5 0-2.21-1.79-4-4-4z"/></svg></div>';
                    block.find('.components-base-control__field').first().append(icon);
                }

            })
            $('.modal-helper').off('click', question.onHelpIconClick);
            $('.modal-helper').on('click', question.onHelpIconClick);


        },

    }

    window.question = question;
    wp.hooks.addAction('lzb.components.PreviewServerCallback.onChange', 'bausteine', function (props) {

        question.alterDisplay();

    });
})(jQuery);
