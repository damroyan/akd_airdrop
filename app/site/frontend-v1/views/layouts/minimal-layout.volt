<!DOCTYPE html>
<html>
    {{ partial("head") }}

    <body class="blank">

        {# сплешка-лоадер. Возникает при загрузке страницы #}
        {{ partial('splash') }}

        {# пользовательские сообщения. Читаем в WIKI как пользоваться #}
        {{ partial('user_message') }}

        {# основной контент #}
        {% block content %}{% endblock %}

        {{ partial("script") }}

        {# внутри каждого шаблона можно вставлять блок с таким именем и он подставится #}
        {% block script_page %}{% endblock %}

        <div class="overall" id="overall"></div>

    </body>
</html>