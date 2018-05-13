{# Modal #}
<script type="text/x-jquery-tmpl" id="templateModal">
<div class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                  <h4 class="modal-title"></h4>
            </div>

            <div class="modal-body"></div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary modal-button">Ok</button>

                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
</script>

<script type="text/x-jquery-tmpl" id="templateBusy">
<div class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <p>
                        <span class="ajax-loader"></span>
                    </p>
                    <div><small>Выполняю действие&hellip;</small></div>
                </div>
            </div>
        </div>
    </div>
</div>
</script>

<!--[if lte IE 8]>
<script type="text/javascript" src="/js/lib/console.js"></script>
<![endif]-->
<script type="text/javascript" src="/js/lib/require.js"></script>

<script type="text/javascript">
    var globalVars = {{ global_var|json_encode }};

    (function() {
        var urlArgs = '{{ global_var['version'] }}';
        {% if config.application.production %}
        urlArgs = {{ config.system.version|json_encode }};
        {% else %}
        urlArgs = {{ config.system.version|json_encode }} + '_' + Math.random();
        {% endif %}

        requirejs.config({
            baseUrl: '{{ '/js/' }}',
            urlArgs: urlArgs,
            paths: {

                'app':                      '/front/backend-v1/app',
                'app/lang':                 '/front/backend-v1/lang',

                'ckeditor':                 '/front/ckeditor/ckeditor',

                'bootstrap':                '/front/bootstrap/js/bootstrap',

                'jquery':                   'lib/jquery',
                'jqueryUi':                 'lib/jquery-ui/jquery-ui',

                'jqueryBrowser':            'lib/jquery.browser',
                'jqueryMask':               'lib/jquery.mask-1.4.1',
                'jqueryTmpl':               'lib/jquery.tmpl',

                'jqueryFileupload':         'lib/jquery-file-upload/js/jquery.fileupload',
                'jqueryFileuploadIframe':   'lib/jquery-file-upload/js/jquery.iframe-transport',

                'jed':                      'lib/jed'
            },
            shim: {
                'bootstrap': {
                    'deps': ['jquery']
                }
            },
            map: {
                "jqueryFileupload": {
                    'jquery.ui.widget': 'jqueryUi',
                    'jquery-ui/ui/widget': 'jqueryUi'
                }
            }
        });

        requirejs(['jquery', 'app']);
    })();

</script>
