<!--[if lte IE 8]>
<script type="text/javascript" src="/js/lib/console.js"></script>
<![endif]-->

<script type="text/javascript" src="/js/lib/require.js"></script>

<script type="text/javascript">
    {# передача глобальных переменных. Задаются в di.php #}
    window.globalVars = {{ global_var|json_encode }};
    window.appLocale = {{ language|json_encode }};

    (function() {
        var urlArgs = '{{ global_var['version'] }}';
        {% if config.application.production %}
        urlArgs = {{ config.system.version|json_encode }};
        {% else %}
        urlArgs = {{ config.system.version|json_encode }} + '_' + Math.random();
        {% endif %}

        requirejs.config({
            baseUrl: '/js/',
            urlArgs: urlArgs,
            paths: {
                'app':                      'app/frontend-v1',
                'app/tmpl':                 '/assets/frontend.tmpl',

                'text':                     'lib/requirejs-plugins/text',
                'json':                     'lib/requirejs-plugins/json',

                'jed':                      'lib/jed',

                'jquery':                   'lib/jquery',
                'jqueryUi':                 'lib/jquery-ui/jquery-ui',

                'jqueryBrowser':            'lib/jquery.browser',
                'jqueryMask':               'lib/jquery.mask-1.4.1',
                'jqueryTmpl':               'lib/jquery.tmpl',

                'jqueryFileupload':         'lib/jquery-file-upload/js/jquery.fileupload',
                'jqueryFileuploadIframe':   'lib/jquery-file-upload/js/jquery.iframe-transport',

                'bootstrap':                '/front/bootstrap/js/bootstrap',
                'bootstrapMetisMenu':       '/front/bootstrap-metismenu/metisMenu'
            },
            shim: {
                'bootstrap':            {deps: ['jquery']}
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
