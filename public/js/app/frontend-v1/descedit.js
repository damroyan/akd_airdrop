define([
    'jquery', 
    'app/core',
    'ckeditor'
], function($, core, ckeditor) {
    console.log('[app/ckeditor]', 'ckeditor');



    $('#editor:eq(0)').each(function() {

        console.log('[ckeditor]', 'ckeditor');

        CKEDITOR.replace( 'editor' );


        $( "#edit" ).click(function() {
            const data = CKEDITOR.instances.editor.getData();

            var urlParams = new URLSearchParams(window.location.search);
            var offer_id = urlParams.get('offer_id');
            var $busy = core.busy();

            core.request(
                "/api/v1/offer/descUpdate/",
                {
                    'offer_description': data,
                    'offer_id': offer_id
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
                }
            );
        });

    });
});