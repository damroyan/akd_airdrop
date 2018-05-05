{% if page.total_pages > 1 %}
{% set how = 2 %}
{% set params_get = params_get is defined ? params_get : [] %}

<nav>
    <ul class="pagination">

    {% if page.current != 1 and (disable_arrow is not defined or disable_arrow is false) %}

        {% if disable_arrow_force is not defined or disable_arrow_force is false %}
        <li>
            <a href="{{ url(route, array_merge(params_get, {'page': null})) }}">
                <span>&laquo;&laquo;</span></a></li>
        {% endif %}

        <li>
            <a href="{{ url(route, array_merge(params_get, {'page': (page.before > 1 ? page.before : null)})) }}">
                <span>&laquo;</span></a></li>

    {% endif %}

    {% set hellip = false %}

    {% for index in 1..page.total_pages %}

        {% if index <= 2 or index > page.total_pages - 2 or (index > page.current - how and index < page.current + how) %}
        <li {% if index == page.current %} class="active" {% endif %} >
            <a href="{{ url(route, array_merge(params_get, {'page': (index > 1 ? index : null)})) }}">
                <span>{{ index }}</span></a></li>
            {% set hellip = false %}
        {% elseif hellip is false %}
            <li>
                <a href="{{ url(route, array_merge(params_get, {'page': (index > 1 ? index : null)})) }}">
                    <span>...</span></a></li>
            {% set hellip = true %}
        {% endif %}

    {% endfor %}

    {% if page.current != page.total_pages and (disable_arrow is not defined or disable_arrow is false) %}

        <li>
            <a href="{{ url(route, array_merge(params_get, {'page': (page.next > 1 ? page.next : null)})) }}">
                <span>&raquo;</span></a></li>

        {% if disable_arrow_force is not defined or disable_arrow_force is false %}
        <li>
            <a href="{{ url(route, array_merge(params_get, {'page': (page.total_pages > 1 ? page.total_pages : null)})) }}">
                <span>&raquo;&raquo;</span></a></li>
        {% endif %}

    {% endif %}

    </ul>
</nav>

{% endif %}
