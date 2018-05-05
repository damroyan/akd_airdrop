{% extends "layouts/layout.volt" %}

{% block content %}

    <div class="error-container">
        <i class="pe-7s-way text-success big-icon"></i>
        <h1>{{ t.gettext('Permission denied!') }}</h1>
        <p>
            {{ t.gettext('You should be authorized or have another User Role in system') }}

        </p>
        <a href="{{ url({'for': 'frontend-v1-user-auth'}) }}" class="btn btn-xs btn-success">{{ t.gettext('Login In') }}</a>
    </div>

{% endblock %}