<!DOCTYPE html>
<html>
    {{ partial("head") }}

    <body class="light-skin fixed-navbar fixed-sidebar">

        {# сплешка-лоадер. Возникает при загрузке страницы #}
        {{ partial('splash') }}

        {# пользовательские сообщения. Читаем в WIKI как пользоваться #}
        {{ partial('user_message') }}

        {# топовая навигация #}
        {{ partial('navigation') }}

        {# левое меню #}
        {{ partial('sidebar') }}

        {# основной контент #}
        <div id="wrapper">

            {% block content %}{% endblock %}

            {# выдвигающаяся панель справа #}
          {#  {{ partial('right_sidebar') }} #}

            {# прибитый футер  #}
            {{ partial('footer') }}

        </div>

        {# подключени JS #}
        {{ partial("script") }}

        {# внутри каждого шаблона можно вставлять блок с таким именем и он подставится #}
        {% block script_page %}{% endblock %}

        <div class="overall" id="overall"></div>

    </body>
</html>