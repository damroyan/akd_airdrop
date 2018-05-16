{% extends "layouts/layout.volt" %}

{% block content %}


    <div class="hpanel">
        <div class="panel-body">
            <a class="small-header-action" href="javascript:;">
                <span class="clip-header">
                    <i class="fa fa-arrow-up"></i></span></a>


            <h2 class="font-light m-b-xs">
                {{ t.gettext('FAQ') }}
            </h2>
            <small>{{ t.gettext('If you not find answer here please use <a href="%s">feedback form</a>') | format(url({'for':'frontend-v1-feedback-form'})) }}</small>
        </div>
    </div>

    {{  partial('language',['language': language, 'partial_name':'faq'])  }}

{% endblock %}