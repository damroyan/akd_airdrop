{% extends "layouts/layout.volt" %}

{% block content %}

<div class="container">
    <div class="row">
        <h2>
            {{ seo['title']|escape }}
        </h2>

        <hr />

        <div class="well">
            <h4>
                {{ seo['description']|escape }}
            </h4>
        </div>

        <p>
            <a href="{{ url({'for': 'frontend-v1-index-index'}) }}"
               class="btn btn-primary btn-lg"
                    >Главная</a>
        </p>
    </div>
</div>

{% endblock %}