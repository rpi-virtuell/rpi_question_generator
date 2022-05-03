(function($) {

    //kürzt die schreibweisen:

    const {
        select,                 //statt: www.data.select(...) jetzt nur: select(...)
        subscribe,              //...
        dispatch
    } = wp.data;


    const question = {
        init: function () {
            question.doBlockListObserve(question.onChange);
            window.question = question;
            question.alterDisplay();

        },

        alterDisplay: function () {
            let block = $('#block-' + clientId);


            let questiontitle = block.find('.lzb-content-title');

            //Erstes EingabeControlFeld im Question Block ermitteln
            let titleInputControl = block.find('.lzb-content-controls div').first();
            titleInputControl.addClass('question-header');

            //falls vorhanden Block Icon löschen
            var icon = block.find('.question-icon');
            titleInputControl.prepend(icon);
        },

    }



})(jQuery);