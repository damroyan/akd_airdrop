{% extends "layouts/layout.volt" %}

{% block content %}

    <div class="error-container">
        <i class="pe-7s-way text-success big-icon"></i>
        <h1>{{ t.gettext('Internal Service Error') }}</h1>
        <p>
            {{ t.gettext('If you see this message too often please let us know!') }}

        </p>
        <a href="{{ url({'for': 'frontend-v1-feedback-form'}) }}" class="btn btn-xs btn-success">{{ t.gettext('Contact Support') }}</a>
    </div>

{% endblock %}