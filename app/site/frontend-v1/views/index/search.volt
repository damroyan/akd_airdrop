{% extends "layouts/layout.volt" %}

{% block content %}


    <div class="normalheader transition animated fadeIn">
        <div class="hpanel">
            <div class="panel-body">
                <a class="small-header-action" href="">
                    <div class="clip-header">
                        <i class="fa fa-arrow-up"></i>
                    </div>
                </a>


                <h2 class="font-light m-b-xs">
                    {{ t.gettext('Searching for "%s"')|format( q )  }}
                </h2>
            </div>
        </div>
    </div>

    <div class="content animate-panel">
        <div class="hpanel">
            <div class="panel-body vertical-align">
                {% if results %}
                    {% for item in results %}
                        {{ loop.index0 }}<br>
                    {% endfor %}
                {% else %}
                    {{ t.gettext('We not found anything for this query. Please try to search something else.') }}
                {% endif %}


            </div>

        </div>

    </div>

{% endblock %}