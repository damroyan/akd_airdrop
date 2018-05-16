{#
    ТЕХНИЧЕСКИЙ ШАБЛОН ДЛЯ ОБРАБОТКИ ЛОГИКИ ПОДКЛЮЧЕНИЯ ЯЗЫКА!
    Принимает параметры language & partial_name & params
#}


{% if language %}
    {% set file = ['../languages/',language,'/partials/',partial_name] | join %}
    {{ partial( file, params ) }}

    {#{% if is_file([ file,'.volt'] | join) %}
        {{ partial( file, params ) }}
    {% else %}
        {{ partial(partial_name,params) }}
    {% endif %}#}
{% else %}
    {{ partial(partial_name,params) }}
{% endif %}