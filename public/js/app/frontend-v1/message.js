define([
    'jquery', 
    'app/core'
], function($, core) {
    console.log('[app/message]', 'Init');

    $('[data-message-action="close"]').on({
        click: function(e) {
            e.preventDefault();

            var obj = $(this),
                data = obj.data();

            if (!data['messageId']) {
                return;
            }

            if($(obj).data('disabled')) {
                return;
            }

            var $busy = core.busy();
            $(obj)
                .data({disabled: true})
                .attr({disabled: true});

            core.request(
                "/api/v1/message/close/",
                {
                    'message_id': data['messageId']
                },
                function(response) {
                    $busy.modal('hide');
                },
                function(response) {
                    $busy.on({
                        'hidden.bs.modal': function () {
                            core.responseError.call(null, response);
                            $(obj)
                                .data({disabled: false})
                                .attr({disabled: false});
                        }
                    }).modal('hide');
                }
            );
        }
    });
});