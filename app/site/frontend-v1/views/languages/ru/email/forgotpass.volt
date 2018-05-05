{% extends "/email/layout.volt" %}

{% block content %}

    Вы забыли свой пароль!

    Перейдите по ссылке <a href="{{ tokenUrl }}">{{ tokenUrl }}</a> чтобы получить новый


{% endblock %}
