{% extends "layouts/layout.volt" %}

{% block content %}

<div class="container">
    <h2>
        Пользователи /
        авторизации

        <small>{{ page.total_items }} элементов</small>
    </h2>
    <hr />

    <div>&nbsp;</div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Id</th>
                <th>Пользователь</th>
                <th>Дата авторизации</th>
                <th>IP</th>
                <th>UA</th>
            </tr>
        </thead>

        <tbody>

    {% for item in page.items %}

            <tr>
                <td>
                    {{ item.user_access_log_id|escape }}
                </td>
                <td>

        {% if item.user_id %}
            {% if item.user.user_delete %}
                    <span class="glyphicon glyphicon-trash"></span>
            {% endif %}

                    <a href="{{ url({'for': 'backend-v1-user-accesslog'}, {'user_id': item.user_id}) }}">
                        {{ item.user.getName()|escape }}</a>

                    <a href="{{ url({'for': 'backend-v1-user-edit'}, {'user_id': item.user_id}) }}">
                        <span class="glyphicon glyphicon-new-window"></span></a>
        {% endif %}

                </td>
                <td>
                    {{ item.user_access_log_cdate|date_dmr }}
                </td>
                <td>
                    <a href="{{ url({'for': 'backend-v1-user-accesslog'}, {'user_access_log_ip': item.user_access_log_ip}) }}">
                        {{ item.user_access_log_ip|escape }}</a>
                </td>
                <td>
                    {{ text_substr(item.user_access_log_ua, 128)|escape }}
                </td>
            </tr>

    {% endfor %}

        </tbody>
    </table>

    {% if !page.total_items %}
    <div class="well">
        <h4>
            Ничего не найдено :-(
        </h4>
    </div>
    {% endif %}

    {{ partial("page", {'route': {'for': 'backend-v1-user-accesslog'}, 'page': page, 'params_get': params_get, 'disable_arrow_force': true}) }}

</div>

{% endblock %}