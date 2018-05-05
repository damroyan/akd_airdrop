{# Запрос на доступ к франшизе #}

{% extends "/email/layout.volt" %}

{% block content %}

    Добро пожаловать на сервис! Это письмо о регистрации!

    Перейдите по ссылке, чтобы активировать аккаунт <a href="{{ tokenUrl }}">{{ tokenUrl }}</a>


{% endblock %}
