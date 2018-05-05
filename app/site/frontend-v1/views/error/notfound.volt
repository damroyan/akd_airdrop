{% extends "layouts/layout.volt" %}

{% block content %}
    <div class="error-container">
        <i class="pe-7s-way text-success big-icon"></i>
        <h1>{{ t.gettext('Page was not found!') }}</h1>
        <p>
            {{ t.gettext('Sorry, but the page you are looking for has note been found. Try checking the URL for error, then hit the refresh button on your browser or try found something else in our app.') }}

        </p>
        <a href="{{ url({'for': 'frontend-v1-index-index'}) }}" class="btn btn-xs btn-success">{{ t.gettext('Go to main page') }}</a>
    </div>

{% endblock %}