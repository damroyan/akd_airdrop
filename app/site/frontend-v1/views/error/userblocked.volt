{% extends "layouts/layout.volt" %}

{% block content %}


    <div class="error-container">
        <i class="pe-7s-way text-success big-icon"></i>
        <h1>{{ t.gettext('Account was blocked!') }}</h1>
        <p>
            {{ t.gettext('This account was blocked! If you have any questions about this: please contact support.') }}

        </p>
        <a href="{{ url({'for': 'frontend-v1-feedback-form'}) }}" class="btn btn-xs btn-success">{{ t.gettext('Write to support') }}</a>
    </div>

{% endblock %}