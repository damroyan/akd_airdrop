{% extends "layouts/layout.volt" %}

{% block content %}

    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-lg-offset-2">

                <h1>{{ t.gettext('Authorization Error') }}</h1>
                <hr />


                <p>
                    <b>{{ t.gettext('Message: ') }}:</b>
                </p>

                <div class="well">
                    {{ message|escape }}
                </div>

                <p class="text-center">
                    <a href="{{ url({
                        'for': redirect_for
                    }, {
                        'redirect': redirect
                    }) }}" class="btn btn-primary">
                        <span class="button">{{ t.gettext('Retry') }}</span>
                    </a>

                    <a href="{{ redirect }}"
                       class="btn btn-default">
                        <span class="button">{{ t.gettext('Cancel') }}</span>
                    </a>
                </p>
            </div>
        </div>
    </div>

{% endblock %}
