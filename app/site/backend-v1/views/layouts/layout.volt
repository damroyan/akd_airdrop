<!DOCTYPE html>
<html lang="ru">
    {{ partial("head") }}

    <body>

        {{ partial("navigation") }}
        {{ partial('user_message') }}
{% block content %}{% endblock %}

        {{ partial("script") }}

{% block script_page %}{% endblock %}

        <div class="overall" id="overall"></div>
    </body>
</html>