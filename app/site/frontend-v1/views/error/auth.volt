{% extends "layouts/layout.volt" %}

{% block content %}

    <div class="error-container">
        <i class="pe-7s-way text-success big-icon"></i>
        <h1>{{ t.gettext('Authorzation needed!') }}</h1>
        <p>
            {{ t.gettext('You should be authorized to use this page') }}

        </p>
        <a href="{{ url({'for': 'frontend-v1-user-auth'}) }}" class="btn btn-xs btn-success">{{ t.gettext('Login In') }}</a>
    </div>

{% endblock %}