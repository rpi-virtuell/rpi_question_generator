/**
 * Leere Leitfragen (ohne inner blocks) ausblenden und im entwurfsmodus
 */
jQuery(document).ready(function ($){
    $('div[class*="wp-block-lazyblock-reli-leitfragen"]').each(function (i, elem) {
        const block = $(elem);
        const inner = block.find('.lazyblock-inner-blocks').first();
        const html = inner.html();
        const str = html.replace(/(<([^>]+)>)/gi, "").trim();
        //if(!str)
        if(!str){
            if(inner.children().length>0 && inner.children()[0].tagName == 'FIGURE' ) {
                if($(inner.children()[0]).hasClass('wp-block-gallery')){
                    return;
                }

            }

            if($('article.status-draft').length>0){
                block.css({opacity:0.2});
            }else{
               // block.remove();
            }
        }
    });


});
