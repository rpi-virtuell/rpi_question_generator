/**
 * Leere Leitfragen (ohne inner blocks) ausblenden
 */
jQuery(document).ready(function ($){
    $('div[class*="wp-block-lazyblock-reli-leitfragen"]').each(function (i, elem) {
        const block = $(elem);
        const inner = block.find('.rpi-question-grid .rpi-question-inner-block .lazyblock-inner-blocks').first();
       // if(!inner.html()) block.remove();
        if(!inner.html()) block.css({opacity:0.3});

    })
});
