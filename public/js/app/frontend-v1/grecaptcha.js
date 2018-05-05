define([
    'jquery', 'app/core', 'app/resize'
], function($, core, appResize) {
    console.log('[app/grecaptcha]', 'Init');

    var _ = [];

    var result = {
        isComplete: false,
        add: function() {
            _.push(this);
            result._callback();
        },

        _callback: function() {
            if(!result.isComplete) {
                return null;
            }

            for(var i in _) {
                if(!_.hasOwnProperty(i)) {
                    continue;
                }

                var obj = _[i];
                $(obj)
                    .on({
                        'render.app.grecaptcha': function() {
                            if($(this).data('grecaptchaRender')) {
                                $(this).trigger('reset.app.grecaptcha');
                                return true;
                            }

                            $(this).empty();
                            var id = (function() {
                                var id = $(this).attr('id');
                                if(!id) {
                                    id = 'id-grecaptcha-' + core.random();
                                    $(this).attr('id', id);
                                }

                                return id;
                            }).call(this);

                            var params = {
                                sitekey: globalVars['recaptchaPublicKey']
                            };
                            if(appResize.getVar('windowWidth') < 560) {
                                params['size'] = 'compact';
                            }

                            var grecaptchaId = window.grecaptcha.render(
                                id,
                                params
                            );

                            $(this).data({
                                'grecaptchaId': grecaptchaId,
                                'grecaptchaRender': true
                            });
                        },
                        'reset.app.grecaptcha': function() {
                            if($(this).data('grecaptchaRender')) {
                                window.grecaptcha.reset(
                                    $(this).data('grecaptchaId')
                                );
                            }
                            else {
                                $(this).trigger('render.app.grecaptcha');
                            }

                        }
                    })
                    .trigger('ready.app.grecaptcha')
                ;
            }
        }
    };
    
    if(typeof(window.grecaptcha) == 'undefined') {
        window.grecaptcha = null;
        
        (function() {
        	var ga = document.createElement('script');
            ga.type = 'text/javascript';
            ga.async = true;
        	ga.src = 'https://www.google.com/recaptcha/api.js?onload=fnRecaptchaOnloadCallback&render=explicit';
        	var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ga, s);
        })();

        window.fnRecaptchaOnloadCallback = function() {
            result.isComplete = true;
            result._callback();
        };
    }
    else {
        result._callback();
    }

    return result;
});
