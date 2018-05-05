require(['app/core', 'jqueryFileupload', 'jqueryFileuploadIframe'], function(core) {

    $('.js-image-upload').each(function () {
        if (!$.support.fileInput) {
            return false;
        }

        var $obj = $(this),
            data = $obj.data();

        var fnChangeImage = function (val, image) {
            $('img', $obj).attr({
                src: image
            });

            $(data['fieldSelector'], $obj).val(val);
            if($obj.data('callback')) {
                $obj.trigger('callback.upload');
            }

            $('.remove', $obj).trigger('toggle');
        };
        
        $('.fileinput-button', $obj).fileupload({
            url: '/api/v1/upload/temp/',
            type: 'POST',
            dataType: 'json',
            done: function (e, response) {
                var fileuploadRation = $(this).data('fileuploadRation');
                fileuploadRation = fileuploadRation ? fileuploadRation : '1x1';

                core.response(response.result, function (response) {
                    fnChangeImage(
                        response.file,
                        response.preview[fileuploadRation]
                    );
                },
                function (response) {
                    core.responseError(response);
                });
            }
        });

        $('.remove', $obj).on({
            toggle: function (e) {
                e.stopPropagation();
                var v = $(data['fieldSelector'], $obj).val();

                if (v) {
                    $(this).show();
                }
                else {
                    $(this).hide();
                }
            },
            click: function (e) {
                e.preventDefault();

                fnChangeImage(
                    '',
                    data['imageBlank']
                );
            }
        }).trigger('toggle');
    });
});