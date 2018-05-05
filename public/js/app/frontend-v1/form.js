define([
    'jquery', 'app/core', 'app/lang'
], function($, core, T) {
    console.log('[app/form]', 'Init');

    // Todo

    // var fnValidation = function() {
    //     var obj     = this,
    //         data    = $(obj).data(),
    //         error   = false;
    //
    //     if(data['validation']) {
    //         $('input[type="checkbox"]', obj).each(function() {
    //             var data    = $(this).data(),
    //                 e       = false;
    //
    //             if(data['required'] && !$(this).prop('checked')) {
    //                 e       = true;
    //                 error   = true;
    //             }
    //
    //             if(e) {
    //                 $(this).trigger('error.app.form');
    //             }
    //             else {
    //                 $(this).trigger('success.app.form');
    //             }
    //         });
    //
    //         $('select', obj).each(function() {
    //             var data    = $(this).data(),
    //                 e       = false;
    //
    //             if(data['required'] && !$.trim($(this).val())) {
    //                 e       = true;
    //                 error   = true;
    //             }
    //
    //             if(e) {
    //                 $(this).trigger('error.app.form');
    //             }
    //             else {
    //                 $(this).trigger('success.app.form');
    //             }
    //         });
    //
    //         $('textarea', obj).each(function() {
    //             var data    = $(this).data(),
    //                 e       = false;
    //
    //             if(data['required'] && !$.trim($(this).val())) {
    //                 e       = true;
    //                 error   = true;
    //             }
    //
    //             if(e) {
    //                 $(this).data('bubbleMessage', 'string_required');
    //                 $(this).trigger('error.app.form');
    //             }
    //             else {
    //                 $(this).trigger('success.app.form');
    //             }
    //         });
    //
    //         $('input[type="text"], input[type="password"], input[type="email"], input[type="url"]', obj).each(function() {
    //             var data    = $(this).data(),
    //                 e       = false,
    //                 val     = $.trim($(this).val().toString()),
    //                 bubbleMessage = null;
    //
    //             if(!val) {
    //                 if(data['required']) {
    //                     e       = true;
    //                     error   = true;
    //                     bubbleMessage = 'string_required';
    //                 }
    //             }
    //             else {
    //                 if(data['validation']) {
    //                     switch(data['validation']) {
    //                         case 'link':
    //                             if(
    //                                 !core.getVar().preg.link.test(val)
    //                             ) {
    //                                 e       = true;
    //                                 error   = true;
    //                                 bubbleMessage = 'url_incorrect';
    //                             }
    //                             break;
    //
    //                         case 'number':
    //                             if(
    //                                 !core.getVar().preg.number.test(val) ||
    //                                 (data['numberMin'] && data['numberMin'] > parseFloat(val)) ||
    //                                 (data['numberMax'] && data['numberMax'] < parseFloat(val))
    //                             ) {
    //                                 e       = true;
    //                                 error   = true;
    //                                 bubbleMessage = 'string_incorrect';
    //                             }
    //                             break;
    //
    //                         case 'phone':
    //                             if(
    //                                 !core.getVar().preg.phone.test(val)
    //                             ) {
    //                                 e       = true;
    //                                 error   = true;
    //                                 bubbleMessage = 'string_incorrect';
    //                             }
    //                             break;
    //
    //
    //                         case 'digital':
    //                             $(this).val(
    //                                 val
    //                                     .replace(',', '.')
    //                                     .replace(/\s+/, '')
    //                             );
    //
    //                             if(
    //                                 !core.getVar().preg.digital.test(val)
    //                             ) {
    //                                 e       = true;
    //                                 error   = true;
    //                                 bubbleMessage = 'string_incorrect';
    //                             }
    //                             break;
    //
    //                         case 'email':
    //                             if(
    //                                 !core.getVar().preg.email.test(val)
    //                             ) {
    //                                 e       = true;
    //                                 error   = true;
    //                                 bubbleMessage = 'email_incorrect';
    //                             }
    //                             break;
    //
    //                         case 'password':
    //                             if(
    //                                 !core.getVar().preg.password.test(val)
    //                             ) {
    //                                 e       = true;
    //                                 error   = true;
    //                                 bubbleMessage = 'string_incorrect';
    //                             }
    //                             break;
    //
    //                         default:
    //                             break;
    //
    //                     }
    //                 }
    //             }
    //
    //             if(e) {
    //                 if(bubbleMessage) {
    //                     $(this).data('bubbleMessage', bubbleMessage);
    //                 }
    //                 $(this).trigger('error.app.form');
    //             }
    //             else {
    //                 $(this).trigger('success.app.form');
    //             }
    //         });
    //     }
    //
    //     return error;
    // };
    //
    // // Submit
    // $(document).on({
    //     click: function(e) {
    //         var data = $(this).data();
    //
    //         if(!data['disabled']) {
    //             var $form = $(this).parents('form');
    //             var type = data['type'] ? data['type'] : 'submit';
    //
    //             $('[name="action_type"]', $form).val(type);
    //             $form.trigger('submit.app.form');
    //         }
    //
    //         e.stopPropagation();
    //     }
    // }, '[role="button"][data-type="submit"], [role="button"][data-type="cancel"]');
    //
    // // Form
    // $(document).on({
    //     'enable.app.form': function() {
    //         $(this).data('disabled', false);
    //
    //         $('[role="button"][data-type="submit"]', this)
    //             .removeClass('is-disabled')
    //             .data('disabled', false);
    //     },
    //     'disable.app.form': function() {
    //         $(this).data('disabled', true);
    //
    //         $('[role="button"][data-type="submit"]', this)
    //             .addClass('is-disabled')
    //             .data('disabled', true);
    //     },
    //     'submit.app.form': function(e) {
    //         var obj     = this,
    //             data    = $(obj).data(),
    //             error   = false;
    //
    //         if(data['disabled']) {
    //             e.preventDefault();
    //             return false;
    //         }
    //
    //         $(obj).trigger('disable.app.form');
    //
    //         error = fnValidation.call(obj);
    //         if(error) {
    //             $(obj).trigger('enable.app.form');
    //
    //             e.preventDefault();
    //             return false;
    //         }
    //         else {
    //             if(data['async']) {
    //                 if(data['asyncUrl']) {
    //                     var $busy = appPopup.busy();
    //
    //                     core.request(
    //                         core.url(data['asyncUrl']),
    //                         $(obj).serialize(),
    //                         function(response) {
    //                             var $popup;
    //
    //                             switch(data['asyncComplete']) {
    //                                 case 'reload':
    //                                     core.reload();
    //                                     break;
    //
    //                                 case 'redirect':
    //                                     core.reload(
    //                                         response.redirect
    //                                             ? response.redirect
    //                                             : '/'
    //                                     );
    //                                     break;
    //
    //                                 case 'message':
    //                                     $busy.trigger('hide.app.popup');
    //                                     appPopup.hideParent.call(obj);
    //
    //                                     $popup = appPopup.alert(
    //                                         (response.message ? T(response.message) : T('action_complete')),
    //                                         T('popup_header_alert'),
    //                                         function() {
    //                                             $(obj).trigger('enable.app.form');
    //                                             $popup.trigger('hide.app.popup');
    //                                         }
    //                                     );
    //
    //                                     break;
    //
    //                                 default:
    //                                     $busy.trigger('hide.app.popup');
    //                                     appPopup.hideParent.call(obj);
    //
    //                                     $popup = appPopup.alert(
    //                                         (response.message ? T(response.message) : T('action_complete')),
    //                                         T('popup_header_alert'),
    //                                         function() {
    //                                             if(response.redirect) {
    //                                                 core.reload(
    //                                                     response.redirect
    //                                                         ? response.redirect
    //                                                         : '/'
    //                                                 );
    //                                             }
    //                                             else {
    //                                                 $(obj).trigger('enable.app.form');
    //                                                 $popup.trigger('hide.app.popup');
    //                                             }
    //                                         }
    //                                     );
    //
    //                                     break;
    //                             }
    //                         },
    //                         function(response) {
    //                             $busy.trigger('hide.app.popup');
    //                             $(obj).trigger('enable.app.form');
    //                             core.responseError(response, $(obj));
    //
    //                             $('.js-grecaptcha', obj).trigger('reset.app.grecaptcha');
    //                         }
    //                     );
    //                 }
    //                 else {
    //                     $(obj).trigger('async.app.form');
    //                 }
    //
    //                 e.preventDefault();
    //                 return false;
    //             }
    //
    //             if($(obj).attr('target') == '_blank') {
    //                 $(obj).trigger('enable.app.form');
    //             }
    //         }
    //
    //         e.stopPropagation();
    //     }
    // }, 'form');
    //
    // var fnGetObj = function() {
    //     var tag         = this.tagName.toString().toLowerCase(),
    //         selector    = 'b-form__' + tag;
    //
    //     return $(this).parents('.' + selector)[0];
    // };
    //
    // // Input, Textarea
    // $(document).on({
    //     'mouseover.app.form': function(e) {
    //         var obj = fnGetObj.call(this);
    //         $(obj).addClass('has-hover');
    //     },
    //
    //     'mouseleave.app.form': function() {
    //         var obj = fnGetObj.call(this);
    //         $(obj).removeClass('has-hover');
    //     },
    //
    //     'focus.app.form': function() {
    //         var obj = fnGetObj.call(this);
    //         $(obj).addClass('has-focus');
    //
    //         $(this).trigger('active.app.form');
    //         $(this).trigger('success.app.form');
    //     },
    //
    //     'blur.app.form': function() {
    //         var field = this,
    //             obj = fnGetObj.call(field);
    //         $(obj).removeClass('has-focus');
    //
    //         $(this).trigger('active.app.form');
    //     },
    //
    //     'keyup.app.form': function() {
    //         $(this).trigger('active.app.form');
    //     },
    //
    //     'active.app.form': function() {
    //         var field = this,
    //             obj = fnGetObj.call(field);
    //
    //         var $bubble = $(field).data('bubble');
    //         if($bubble) {
    //             $bubble.trigger('close.app.bubble');
    //         }
    //
    //         if($(this).val()) {
    //             $(obj).addClass('is-active');
    //         }
    //         else {
    //             $(obj).removeClass('is-active');
    //         }
    //     },
    //
    //     'error.app.form': function() {
    //         var obj = fnGetObj.call(this);
    //         $(obj).addClass('has-error');
    //     },
    //
    //     'success.app.form': function() {
    //         var field = this,
    //             obj = fnGetObj.call(field);
    //
    //         if($(obj).hasClass('has-error')) {
    //             var bubbleMessage = $(field).data('bubbleMessage');
    //             if(bubbleMessage) {
    //                 require(['app/bubble'], function(appBubble) {
    //                     $(field).data({
    //                         'bubble': appBubble.show.call(obj, bubbleMessage)
    //                     });
    //                 });
    //
    //                 $(this).data('bubbleMessage', null);
    //             }
    //
    //             $(obj).removeClass('has-error');
    //         }
    //     }
    // }, 'input[type="password"], input[type="text"], input[type="email"], input[type="url"], textarea');
    // $('input[type="password"], input[type="text"], input[type="email"], input[type="url"], textarea').trigger('active.app.form');
    //
    // // a[data-url]
    // $(document).on({
    //     click: function(e) {
    //         e.preventDefault();
    //
    //         var obj = this,
    //             data = $(this).data();
    //
    //         var fnRequest = function() {
    //             var $busy = appPopup.busy(
    //                 T('busy_wait')
    //             );
    //
    //             var params = {};
    //             if(data['params']) {
    //                 params = data['params'].replace(/^\/\?/, '');
    //             }
    //
    //             core.request(
    //                 data['url'],
    //                 params,
    //                 function(response) {
    //                     $busy.trigger('hide.app.popup');
    //
    //                     if(response.redirect) {
    //                         core.reload(response.redirect);
    //                     }
    //                     else {
    //                         core.reload('/');
    //                     }
    //                 },
    //                 function(response) {
    //                     $busy.trigger('hide.app.popup');
    //                     core.responseError(response, obj);
    //                 }
    //             );
    //         };
    //
    //         fnRequest.call(obj);
    //     }
    // }, 'a[data-url]');
    
    return {
        grecaptcha: function() {
            var obj = this;
            
            if(!$('.js-grecaptcha', obj)[0]) {
                return false;
            }
            
            $('.js-grecaptcha', obj).each(function() {
                $(this).parents('form')
                    .trigger('disable.app.form');
            });
            
            // Инициализация recaptcha
            require(['app/grecaptcha'], function(grecaptcha) {
                $('.js-grecaptcha', obj).each(function() {
                    var obj = this;
                    $(obj).on({
                        'ready.app.grecaptcha': function() {
                            $(obj).parents('form')
                                .trigger('enable.app.form');
                            $(obj).trigger('render.app.grecaptcha');

                            $(window).trigger('resize.app.resize');
                        }
                    });
                    grecaptcha.add.call(obj);
                });
            });            
        }
    }
});
