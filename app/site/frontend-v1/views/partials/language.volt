{#
    ТЕХНИЧЕСКИЙ ШАБЛОН ДЛЯ ОБРАБОТКИ ЛОГИКИ ПОДКЛЮЧЕНИЯ ЯЗЫКА!
    Принимает параметры language & partial_name & params
#}

{% if language %}
    {% set file = ['../languages/',language,'/partials/',partial_name] | join %}
    {% if is_file(file) %}
        {{ partial( file, params ) }}
    {% else %}
        {{ partial(partial_name,params) }}
    {% endif %}
{% else %}
    {{ partial(partial_name,params) }}
{% endif %}