define([
    'jquery',
    'jed',
    'bootstrap'
], function($) {

    console.log('[app]', 'Init');


    // Translation setup
    var _t = new Jed({});
   // console.log(_t.gettext('test'));
    $.get(
        '/assets/frontend.trns.js',
        {},
        function(response) {
            _t = new Jed({
                locale_data : JSON.parse(response),
                "domain" : "messages"
                });
        });


    // Modal - короткий вызов без подтверждения
    var fnAlert = function(content) {
        return fnModal(content);
    };

    // Modal - коротки вызов с подтверждением
    var fnConfirm = function(content, callback) {
        return fnModal(content, _t.gettext('Confirm this please'), _t.gettext('Confirm'), callback);
    };

    // Загружаю - лоадер
    var fnBusy = function() {
        var $modal = $('#overall').append(
            $('#templateBusy').html()
        ).find('> .modal');

        $modal.on({
            'hidden.bs.modal': function() {
                $modal.remove();
            }
        });

        return $modal.modal();
    };

    /**
     * Popup окно - собственно вызов
     *
     * @param content text - html отображаемый в окне
     *
     * @param header text - заголовок окна
     *
     * @param button string - название основной кнопки
     *
     * @param callback function - функция которая будет вызвана при нажатии на кнопку
     *
     */
    var fnModal = function(content, header, button, callback) {
        callback = callback ? callback : function() {};

        var $modal = $('#overall').append(
            $('#templateModal').html()
        ).find('> .modal');

        if(!header) {
            $('.modal-header', $modal).remove();
        }
        else {
            $('.modal-title', $modal).html(header);
        }

        if(!button) {
            $('.modal-button', $modal).remove();
        }
        else {
            $('.modal-button', $modal)
                .on({
                    click: function() {
                        $modal.modal('hide');
                        $modal.on({
                            'hidden.bs.modal': function() {
                                callback();
                            }
                        });
                    }
                })
                .text(button);
        }

        $('.modal-body', $modal).html(content);

        $modal.on({
            'shown.bs.modal': function() {
                $modal.find('button[data-dismiss]').focus();
            },
            'hidden.bs.modal': function() {
                $modal.remove();
            }
        });

        return $modal.modal();
    };

    // ajax _POST запрос
    var fnRequest = function(url, params, callback, fallback) {
        var obj = this;

        params = params ? params : {};
        callback = typeof(callback) == 'function' ? callback : function(response) {};
        fallback = typeof(fallback) == 'function' ? fallback : function(response) {
            fnResponseError.call(obj, response);
        };

        $.post(
            url,
            params,
            function(response) {

                fnResponse.call(
                    obj,
                    response,
                    function(response) {
                        callback.call(obj, response);
                    },
                    function(response) {
                        fallback.call(obj, response)
                    }
                );
            },
            'json'
        );
    };

    // Разбор ответа
    var fnResponse = function(response, callback, fallback) {
        var obj = this;

        callback = typeof(callback) == 'function' ? callback : function() {};
        fallback = typeof(fallback) == 'function' ? fallback : function() {};

        switch(response.response) {
            case 'ok':
                callback.call(obj, response.result);
                return true;

                break;

            default:
                fallback.call(obj, response);
                return false;

                break;
        }
    };

    // Обработка запроса error
    var fnResponseError = function(response) {
        var obj = this,
            $form = null;

        if(obj.tagName && obj.tagName.toString().toUpperCase() == 'FORM') {
            $form = $(obj);
        }

        var text = '';
        if(response.error_fields) {
            text += "FIELDS:\n"
            for(var i in response.error_fields) {
                text += i + ": " + _t.gettext(response.error_fields[i]) + "\n";

                if($form) {
                    var $parent = $form.find('[name="' + i + '"]').parents('.form-group');
                    if($parent[0]) {
                        $parent.addClass('has-error');
                    }
                }

            }
            text += "\n";
        }

        if(response.error_msg) {
            text += "ERROR:\n";
            text += _t.gettext(response.error_msg);
        }

        var $modal = fnModal($.trim(text).replace(/\n/gi, '<br />'), _t.gettext('Form Error!'));
    };

    var urlRandom = function(url) {
        return url + (url.toString().match(/\?/) ? '&' : '?') + parseInt(Math.random() * 10000000);
    };

    // Генератор random Id
    var fnRandomId = function() {
        return 'id-random-' + parseInt(Math.random() * 10000000);
    };

    // Callback на загрузку recaptcha
    window.fnRecaptchaOnloadCallback = function() {
        $('.js-grecaptcha').each(function() {
            var id = $(this).attr('id');

            if(!id) {
                id = fnRandomId();
                $(this).attr('id', id);
            }

            $(this).empty();
            $(this).parents('form').find('[type="submit"]')
                .removeAttr('disabled');

            grecaptcha.render(
                this, {
                    sitekey: globalVars['recaptchaPublicKey']
                }
            );
        });
    };

    var fnRecaptchaReset = function() {
        try {
            if($('.js-grecaptcha')[0]) {
                grecaptcha.reset();
            }
        }
        catch(e) {
            console.log('grecaptcha', e);
        }
    };

    /**
     * Обновление textarea перед тем как делать сабмит формы
     */
    // var fnCkUpdate = function() {
    //     for (instance in CKEDITOR.instances) {
    //         CKEDITOR.instances[instance].updateElement();
    //     }
    // };

    // CKEditor
    // (function() {
    //     $('.js-ckeditor').each(function() {
    //         CKEDITOR.timestamp = 'CACHE-'+ globalVars['version'];
    //         CKEDITOR.config.contentsCss = '/front/css/ckeditor.css?' + globalVars['version'];
    //
    //         var params = {};
    //         params['customConfig'] = '/front/admin-v1/ckeditor.js';
    //
    //         if($(this).data('ckeditor')) {
    //             return;
    //         }
    //
    //         var editor = CKEDITOR.replace(this, params);
    //
    //         $(this).data({
    //             ckeditor: true
    //         });
    //     });
    // })();

    var fnRemoveTr = function() {
        $(this).parents('tr')
            .fadeOut(400, function() {
                $(this).remove();
            });
    };

    var fnUrl = function(url) {
        if(url.match(/^\/api\/v1\//gi)) {
            return url;
        }
        else {
            return '/api/v1/' + url + '/';
        }
    };

    var fnReloadPage = function(url) {
        if(!url) {
            url = window.location.href.toString();
        }

        url = url.replace(/(\?|&)r=\d+/, '');
        if(url.match(/\?/)) {
            url += '&r=' + parseInt(Math.random() * 1000000);
        }
        else {
            url += '?r=' + parseInt(Math.random() * 1000000);
        }

        window.location.href = url;
    };

    var fnRedirect = function(response) {
        if(response.redirect) {
            fnReloadPage(response.redirect);
        }
        else {
            fnReloadPage();
        }
    };

    // $('#side-menu').each(function() {
    //     var obj = this;
    //
    //     $('a.active', this).parents('li').each(function() {
    //         if($.contains(obj, this)) {
    //             $(this).addClass('active');
    //         }
    //     });
    //
    //     $(obj).metisMenu();
    // });

    // Теги
    // require(['bootstrap3tagsinput'], function() {
    //     $('input[data-suggest-url]').each(function() {
    //         var obj = this,
    //             data = $(this).data(),
    //             split = (data['suggestSplit'] ? data['suggestSplit'] : ',');
    //
    //         $(obj).tagsinput({
    //             confirmKeys: [13, 59],
    //             splitValue: split,
    //             maxTags: (data['suggestLimit'] > 0 ? data['suggestLimit'] : null),
    //             allowDuplicates: false,
    //             freeInput: (data['suggestFreeInput'] ? true : false),
    //             onTagExists: function(item, $tag) {
    //                 $tag.hide().fadeIn();
    //
    //                 setTimeout(function() {
    //                     $(obj).tagsinput('input').val('');
    //                 }, 1);
    //             },
    //             typeahead: {
    //                 'delay': 250,
    //                 source: function(query) {
    //                     return $.post(
    //                         fnUrl(data['suggestUrl']), {
    //                             query: query,
    //                             limit: 5,
    //                             result_only: true
    //                         }
    //                     );
    //                 }
    //             }
    //         });
    //
    //         $(obj).on({
    //             'itemAdded': function(e) {
    //                 setTimeout(function() {
    //                     $(obj).tagsinput('input').val('')
    //                 }, 1);
    //             }
    //         });
    //     });
    // });

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

    // Кнопка с JS action
    $('a[data-url]').on({
        click: function(e) {
            e.preventDefault();

            var obj = this,
                data = $(this).data();

            if(data['confirm']) {
                var confirmMessage = _t.gettext('Confirm this please');

                if(data['confirmMessage']) {
                    confirmMessage = data['confirmMessage'];
                }

                fnConfirm(confirmMessage, function() {
                    var params = {};
                    if(data['params']) {
                        params = data['params'].replace(/^\/\?/, '');
                    }

                    var $busy = fnBusy();
                    fnRequest(
                        data['url'],
                        params,
                        function(response) {
                            $busy.on({
                                'hidden.bs.modal': function () {
                                    if (data['callback']) {
                                        eval(data['callback'] + '.call(obj, response)');
                                    }
                                    else {
                                        fnReloadPage();
                                    }
                                }
                            });
                            $busy.modal('hide');
                        },
                        function(response) {
                            $busy.on({
                                'hidden.bs.modal': function () {
                                    fnResponseError.call(obj, response);
                                }
                            });
                            $busy.modal('hide');
                        }
                    );
                });
            } else {
                if(data['url']) {
                    var params = {};
                    if(data['params']) {
                        params = data['params'].replace(/^\/\?/, '');
                    }

                    var $busy = fnBusy();
                    fnRequest(
                        data['url'],
                        params,
                        function(response) {
                            $busy.on({
                                'hidden.bs.modal': function () {
                                    if (data['callback']) {
                                        eval(data['callback'] + '.call(obj, response)');
                                    }
                                    else {
                                        fnReloadPage(
                                            (response['redirect']
                                                ? response['redirect']
                                                : null)
                                        );
                                    }
                                }
                            });
                            $busy.modal('hide');
                        },
                        function(response) {
                            $busy.on({
                                'hidden.bs.modal': function () {
                                    fnResponseError.call(obj, response);
                                }
                            });
                            $busy.modal('hide');
                        }
                    );
                }
                else {
                    fnReloadPage($(obj).attr('href'));
                }
            }
        }
    });

    // Recaptcha
    $('.js-grecaptcha:eq(0)').each(function() {
        require(['app'], function() {
            (function() {
            	var ga = document.createElement('script');
                ga.type = 'text/javascript';
                ga.async = true;
            	ga.src = 'https://www.google.com/recaptcha/api.js?onload=fnRecaptchaOnloadCallback&render=explicit';

                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(ga, s);
            })();
        });
    });

    // Поведение форм
    $('body').on({
        focus: function() {
            var $parent = $(this).parents('.form-group');
            if($parent[0]) {
                if($parent.hasClass('has-error')) {
                    $parent.removeClass('has-error');
                }
            }

        }
    }, 'input, textarea, select');

    // Удаление form-group
    $('body').on({
        click: function(e) {
            e.preventDefault();

            $(this).parents('.form-group')
                .fadeOut(400, function() {
                    $(this).remove()
                });
        }
    }, '.js-form-group-remove');

    // Сабмит формы
    $('.js-form').on({
        submit: function(e) {
            e.preventDefault();

            // fnCkUpdate();

            var $form = $(this),
                data = $form.data();

            $('.form-group', $form).each(function() {
                if($(this).hasClass('has-error')) {
                    $(this).removeClass('has-error');
                }
            });

            $form.find('[type="submit"]')
                .attr({
                    'disabled': 'disabled'
                })
                .addClass('disabled');

            var $busy = fnBusy();
            fnRequest.call(
                $form[0],
                data['url'],
                $form.serialize(),
                function(response) {
                    console.log('Response', response);

                    $busy.on({
                        'hidden.bs.modal': function () {
                            fnRecaptchaReset();

                            var callback = function() {
                                $form.find('[type="submit"]')
                                    .removeClass('disabled');

                                if(response.redirect) {
                                    window.location.href = response.redirect;
                                }
                                else if(data['redirectUrl']) {
                                    window.location.href = data['redirectUrl'];
                                }
                                else {
                                    fnReloadPage();
                                }
                            };

                            if(data['disabledCompleteAlert']) {
                                callback();
                            }
                            else {
                                var message = _t.gettext('Everything done!');
                                if(response['message']) {
                                    message = response['message'];
                                }

                                var $modal = fnAlert(message);
                                $modal.on({
                                    'hidden.bs.modal': function () {
                                        callback();
                                    }
                                });
                            }
                        }
                    });
                    $busy.modal('hide');
                },
                function(response) {
                    $busy.on({
                        'hidden.bs.modal': function() {
                            fnRecaptchaReset();

                            $form.find('[type="submit"]')
                                .removeAttr('disabled')
                                .removeClass('disabled');

                            fnResponseError.call($form[0], response);
                        }
                    });
                    $busy.modal('hide');
                }
            );
        }
    });

    // USER MESSAGES
    $('.js-message-close').on({
        click: function(e) {
            var d = $(this).data();
            if (d.message_id) {
                fnRequest(
                    "/api/v1/message/close/?message_id="+d.message_id,
                    {}
                );
            }
        }
    })
});
