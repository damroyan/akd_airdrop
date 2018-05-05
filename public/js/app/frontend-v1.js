define(['jquery',
    'jed',
    'app/core',
    'bootstrap'
], function($, core) {
    console.log('[app]', 'Init');

    require(['app/homer']);

    require(['app/form'], function(appForm) {
        appForm.grecaptcha.call(document);
    });


    $('[data-message-action]:eq(0)').each(function() {
        require(['app/message']);
    });

    $('.js-welcome-tour-step1:eq(0)').each(function() {
        require(['app/tour']);
    });

    // МОДУЛИ НОВЫЕ ПОДКЛЮЧАЕМ ТУТ!!
    // ФОРМАТ
    /*

       $('.js-question_edit:eq(0)').each(function() {
        require(['app/question-edit']);
        });

     */

    // Initialize popover
    $('[data-toggle="popover"]').popover();
    // $('[data-toggle="popover"]:eq(0)').each(function () {
    //     require(['bootstrapPopover'], function() {
    //         $('[data-toggle="popover"]').popover();
    //     });
    // });
    
    // Initialize mask
    $('input[data-mask]:eq(0)').each(function() {
        require(['jqueryMask'], function() {
            $('input[data-mask]').each(function() {
                var data = $(this).data();
                switch(data['mask']) {
                    case 'time':
                        $(this).mask('99:99:99');
                        break;
    
                    case 'time_hr':
                        $(this).mask('99:99');
                        break;
    
                    case 'date':
                        $(this).mask('99.99.9999');
                        break;
    
                    case 'sql_date':
                        $(this).mask('9999-99-99');
                        break;
    
                    case 'year':
                        $(this).mask('9999');
                        break;
    
                    default:
                        $(this).mask(data['mask']);
                        break;
                }
            });
        });        
    });
    
    // Initialize upload
    $('.js-image-upload:eq(0)').each(function() {
        require(['app/upload']);
    });

    // Initialize datepicker
    $('input[data-datepicker]:eq(0)').each(function() {
        require(['jqueryUi'], function() {
            function getDate( element ) {
                var date,
                    dateFormat = $.datepicker._defaults['dateFormat'];

                try {
                    date = $.datepicker.parseDate( dateFormat, element.value );
                }
                catch( error ) {
                    date = null;
                }

                return date;
            }

            $('input[data-datepicker]').each(function() {
                var data = $(this).data(),
                    params = {};

                if(data['datepickerMaxDate'] === true) {
                    params['maxDate'] = '0d';
                }


                $(this).datepicker(params);

                if(data['datepickerSelectorTo']) {
                    $(this).on({
                        'change': function() {
                            $(data['datepickerSelectorTo']).datepicker(
                                'option', 'minDate', getDate(this)
                            );
                        }
                    });
                }

                if(data['datepickerSelectorFrom']) {
                    $(this).on({
                        'change': function() {
                            $(data['datepickerSelectorFrom']).datepicker(
                                'option', 'maxDate', getDate(this)
                            );
                        }
                    });
                }
            });
        });
    });
    
    /**
     * Обновление аватарки пользователя
     */
    $('[data-callback="user_picture"]').on({
        'callback.upload': function(e) {
            var obj = this,
                field = $(this).data('fieldSelector')
            ;

            core.request(
                '/api/v1/user/picture/',
                {
                    'user_picture': $(field, obj).val()
                },
                function(response) {

                    $('img', obj).attr({
                        src: response['user_picture']['140x140']
                    });

                }
            );
        }
    });

});