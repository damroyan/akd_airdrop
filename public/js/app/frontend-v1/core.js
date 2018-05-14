define([
    'jquery',
    'app/lang',
    'app/tmpl'
], function($, T, TMPL) {
    console.log('[app/core]', 'Init');


    // Modal
    var fnAlert = function(content) {
        return fnModal(content, null, null, null, 'templateFrontendModalOk');
    };

    var fnConfirm = function(content, callback, contentHeader, contentButton) {
        // contentHeader = contentHeader ? contentHeader : T.gettext('popup.header.confirm_action');
        contentHeader = contentHeader ? contentHeader : T.gettext('Confirm this please');
        // contentButton = contentButton ? contentButton : T.gettext('popup.button.confirm_action');
        contentButton = contentButton ? contentButton : T.gettext('Confirm');
        
        return fnModal(content, contentHeader, contentButton, callback, 'templateFrontendModalInfo');
    };
    
    // Загружаю
    var fnBusy = function() {
        var $modal = $('#overall').append(
            $.tmpl(TMPL['templateFrontendModalLoader'], {T: T})
        ).find('> .modal');

        $modal.on({
            'hidden.bs.modal': function() {
                $modal.remove();
            }
        });

        $modal.modal({
            backdrop: 'static',
            keyboard: false
        });

        return $modal;
    };

    // показывает, что минимум одна модалка уже открыта. Используется для продотвращения массовых всплываний модалок
    var modal_busy = false;
    // Popup окно
    var fnModal = function(content, header, button, callback, template)  {
        if (modal_busy) return;

        callback = callback ? callback : function() {};
        template = template ? template : 'templateFrontendModalInfo';

        var $modal = $('#overall').append(
            $.tmpl(TMPL[template], {T: T})
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
                modal_busy = true;
                $modal.find('button[data-dismiss]').focus();
            },
            'hidden.bs.modal': function() {
                modal_busy = false;
                $modal.remove();
            }
        });

        $modal.modal();

        return $modal;
    };

    // ajax _POST запрос
    var fnRequest = function(url, params, callback, fallback, method) {
        var obj = this;

        params = params ? params : {};
        callback = typeof(callback) === 'function' ? callback : function(response) {};
        fallback = typeof(fallback) === 'function' ? fallback : function(response) {
            fnResponseError.call(obj, response);
        };
        method = method ? method : 'POST';

        // Todo ajax
        
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

        callback = typeof(callback) === 'function' ? callback : function() {};
        fallback = typeof(fallback) === 'function' ? fallback : function() {};


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
            for(var i in response.error_fields) {
                if(!response.error_fields.hasOwnProperty(i)) {
                    continue;
                }
                
                text += i + ": ";
                text += T.gettext(response.error_fields[i]) + "\n";

                if($form) {
                    var $field = $form.find('[name="' + i + '"]'),
                        $parent = $field.parents('.form-group');
                    if($parent[0]) {
                        $parent.addClass('has-error');
                    }
                    else {
                        $field.addClass('error')
                    }
                }

            }
            text += "\n";
        }

        if(response.error_msg) {
            text += T.gettext(response.error_msg);
        }

        // var $modal = fnModal($.trim(text).replace(/\n/gi, '<br />'), T.gettext('error.message.form_incorrect'));
        return fnModal(
            $.trim(text).replace(/\n/gi, '<br />'), 
            T.gettext('Error while form was procesed'),
            null,
            null,
            'templateFrontendModalError'
        );
    };

    var fnRandomString = function(length, chars) {
        if(!chars) {
            chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        var result = '';
        for(var i = length; i > 0; --i) {
            result += chars[Math.floor(Math.random() * chars.length)];
        }

        return result;
    };

    var fnRandomDigital = function() {
        return parseInt(Math.random() * 9999999);
    };

    var fnRandom = function() {
        return fnRandomString(32);
    };
    
    var fnRandomUrl = function(url) {
        return url + (url.toString().match(/\?/) ? '&' : '?') + fnRandomDigital();
    };

    // Генератор random Id
    var fnRandomId = function() {
        return 'id-random-' + fnRandom();
    };

    var fnGetElementId = function() {
        var id = $(this).attr('id');

        if(!id) {
            id = fnRandomId();
            $(this).attr('id', id);
        }

        return id;
    };

    var fnReloadPage = function(url) {
        if(!url) {
            url = window.location.href.toString();
        }

        var s = url.split('#');
        url = s[0];

        url = url.replace(/(\?|&)r=\d+/, '');
        if(url.match(/\?/)) {
            url += '&r=' + fnRandomDigital();
        }
        else {
            url += '?r=' + fnRandomDigital();
        }

        window.location.href = url + (s[1] ? '#' + s[1] : '');
    };

    var fnRedirect = function(response) {
        if(response.redirect) {
            fnReloadPage(response.redirect);
        }
        else {
            fnReloadPage();
        }
    };

    var fnRemoveTr = function() {
        $(this).parents('tr')
            .fadeOut(400, function() {
                $(this).remove();
            });
    };

    // Кнопка с JS action
    $('a[data-url]').on({
        click: function(e) {
            e.preventDefault();

            var obj = this,
                data = $(this).data();

            if(data['confirm']) {
                // var confirmMessage = T.gettext('popup.header.need_confirm');
                var confirmMessage = T.gettext('Confirm this please');

                if(data['confirmMessage']) {
                    confirmMessage = data['confirmMessage'];
                }
                
                fnConfirm(confirmMessage, function() {

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

                }, data['confirmMessageHeader'], data['confirmMessageButton']);
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

    // Сабмит формы
    $('.js-form').on({
        submit: function(e) {
            e.preventDefault();

            var $form = $(this),
                data = $form.data();

            $('.form-group.has-error', $form).removeClass('has-error');
            $('.error[name]', $form).removeClass('error');

            $form.find('[type="submit"]')
                .attr({'disabled': 'disabled'})
                .addClass('disabled');

            if (typeof CKEDITOR !== 'undefined') {
                for ( instance in CKEDITOR.instances ) CKEDITOR.instances[instance].updateElement();
            }


            var $busy = fnBusy();
            fnRequest.call(
                $form[0],
                data['url'],
                $form.serialize(),
                function(response) {
                    console.log('Response', response);

                    $busy.on({
                        'hidden.bs.modal': function () {
                            $('.js-grecaptcha', $form).trigger('reset.app.grecaptcha');

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

                            var message = null;
                            if(response['message']) {
                                message = T.gettext(response['message']);
                            }
                            else if(data['completeAlert']) {
                                message = T.gettext('popup.text.action_complete')
                            }

                            if(message) {
                                var $modal = fnAlert(message);
                                $modal.on({
                                    'hidden.bs.modal': function () {
                                        callback();
                                    }
                                });
                            }
                            else {
                                callback();
                            }
                        }
                    });
                    $busy.modal('hide');
                },
                function(response) {
                    console.log(response, $busy);
                    
                    $busy.on({
                        'hidden.bs.modal': function() {
                            $('.js-grecaptcha', $form).trigger('reset.app.grecaptcha');

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
    
    // Поведение форм
    $('body').on({
        focus: function() {
            var $parent = $(this).parents('.form-group');
            if($parent[0]) {
                if($parent.hasClass('has-error')) {
                    $parent.removeClass('has-error');
                }
            }
            
            if($(this).hasClass('error')) {
                $(this).removeClass('error');
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

    return {
        alert:          fnAlert,
        confirm:        fnConfirm,
        busy:           fnBusy,
        modal:          fnModal,
        request:        fnRequest,
        response:       fnResponse,
        responseError:  fnResponseError,
        getElementId:   fnGetElementId,

        random:         fnRandom,
        randomId:       fnRandomId,
        randomUrl:      fnRandomUrl,
        randomString:   fnRandomString,
        randomDigital:  fnRandomDigital,

        reloadPage:     fnReloadPage,
        redirect:       fnRedirect
    };
});