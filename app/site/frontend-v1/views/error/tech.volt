{% extends "layouts/layout.volt" %}

{% block content %}

    <div class="lock-container">
        <div>

            <div class="hpanel">
                <div class="panel-body text-center">

                    <i class="pe-7s-lock big-icon text-success"></i>
                    <br/>
                    <h4>{{ t.gettext('Technical Timout') }}</h4>
                    {{ t.gettext('We are working hard now to make your experience better! Just whait several minutes!') }}
                </div>
            </div>


        </div>
    </div>
    <h2>
        {{ t.gettext('Technical Timout') }}
    </h2>

    <p>
        {{ t.gettext('We are working hard now to make your experience better! Just whait several minutes!') }}
    </p>

{% endblock %}