define([
    'jquery',
    'app/core',
    'app/tmpl',
    'app/lang'
], function($, core, TMPL, T) {
    console.log('[app/compare]', 'Init');

    var fnFormToggle = function(status) {
        var obj = this;

        status = status ? status : false;
        if(status) {
            $('[type="submit"]', obj)
                .attr({
                    'disabled': null
                })
                .removeClass('disabled');

            $('[type="text"]', obj).attr({
                'readonly': null,
                'disabled': null
            });
        }
        else {
            $('.form-group.has-error', obj).removeClass('has-error');
            $('.error[name]', obj).removeClass('error');

            $('[type="submit"]', obj)
                .attr({
                    'disabled': true
                })
                .addClass('disabled');

            $('[type="text"]', obj)
                .attr({
                    'readonly': true,
                    'disabled': true
                });
        }
    };

    $('[data-rankval-item="compare"]').each(function() {
        $('form', this).on({
            submit: function (e) {
                e.preventDefault();

                var $form = $(this),
                    data = $form.data();

                if($form.find('[type="submit"]').hasClass('disabled')) {
                    return;
                }

                fnFormToggle.call($form[0], false);
                
                var url = [];
                $('[name^=url]', $form).each(function() {
                    // todo (валидация URL)
                    if($(this).val()) {
                        url.push($(this).val())
                    }
                });

                if(url.length) {
                    var $busy = core.busy();
                    core.request(
                        '/api/v1/compare/add/', {
                            'url': url
                        },
                        function(response) {
                            $busy.on({
                                'hidden.bs.modal': function () {
                                    core.reloadPage(
                                        (response['redirect']
                                            ? response['redirect']
                                            : null)
                                    );
                                }
                            });
                            $busy.modal('hide');
                        },
                        function(response) {
                            var obj = this;

                            $busy.on({
                                'hidden.bs.modal': function () {
                                    core.responseError.call(obj, response);
                                }
                            });
                            $busy.modal('hide');

                            fnFormToggle.call($form[0], true);
                        }
                    );

                }
                else {
                    fnFormToggle.call($form[0], true);
                }
            }
        });

        // todo refresh
        $('[data-rankval-item="compare-wait"]', this).each(function() {
            var obj = this;

            fnGetStatus = function() {
                var data = $(obj).data();

                var urlCompareId = data['urlCompareId'];

                core.request(
                    '/api/v1/compare/status/', {
                        'url_compare_id': urlCompareId
                    },
                    function(response) {
                        if(response['status'] == 1) {
                            core.reloadPage();
                        }
                        else {
                            setTimeout(function() {
                                fnGetStatus()
                            }, 1000);
                        }
                    },
                    function(response) {
                        core.responseError.call(obj, response);
                    }
                );
            };
            fnGetStatus();

            // fnTimeout = function(count) {
            //     var $elem = $('div.text-center > span', obj).eq(0);
            //     $elem.html(count);
            //
            //     if(count > 0) {
            //         setTimeout(function() {
            //             count--;
            //             fnTimeout(count);
            //         }, 1000);
            //     }
            //     else {
            //         core.reloadPage();
            //     }
            // };
            // fnTimeout(5);
        });
    });
});
