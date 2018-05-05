{% extends "layouts/layout.volt" %}

{% block content %}

<div class="container">
    <h2>
        Пользователи /
        список

        <small>{{ page.total_items }} элементов</small>
    </h2>
    <hr />

    <div class="pull-right">
        <a href="{{ url({'for': 'backend-v1-user-edit'}) }}"
           class="btn btn-danger"
                >
            <span class="glyphicon glyphicon-plus"></span>
            Добавить пользователя</a>
    </div>

    <form action="{{ url({'for': 'backend-v1-user-index'}) }}" method="get" class="form-inline">
        <div class="input-group">
            {{ text_field(
                "query",
                'class': 'form-control',
                'placeholder': 'Id / Email / Ник'
            ) }}
        </div>

        &nbsp;

        <div class="input-group">
            {{ select(
                'user_role',
                static("\Model\User", "getRoleArray"),
                'class': 'form-control',
                'useEmpty': 1,
                'emptyText': '-- Выбрать роль --',
                'emptyValue': ''
            ) }}

        </div>

        &nbsp;

        <div class="input-group">
            {{ select(
                'user_status',
                static("\Model\User", "getStatusArray"),
                'class': 'form-control',
                'useEmpty': 1,
                'emptyText': '-- Выбрать статус --',
                'emptyValue': ''
            ) }}
        </div>

        &nbsp;

        <div class="checkbox">
            <label>
                {{ check_field(
                    "user_delete",
                    "value": 1
                ) }}
                Показать удаленные
            </label>
        </div>

        &nbsp;

        <button type="submit" class="btn btn-primary">Искать</button>
    </form>

    <div>&nbsp;</div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Id</th>
                <th>
                    <span class="glyphicon glyphicon-trash"></span>
                </th>
                <th>
                    <span class="glyphicon glyphicon-picture"></span>
                </th>
                <th>Логин</th>
                <th>Ник</th>
                <th>Имя</th>
                <th>Фамилия</th>
                <th>Роль</th>
                <th>Статус</th>
                <th>Дата добавления</th>
                <th></th>
            </tr>
        </thead>

        <tbody>

    {% for item in page.items %}

            <tr>
                <td>
                    {{ item.user_id|escape }}
                </td>

                <td>
        {% if item.user_delete %}
                    <span class="glyphicon glyphicon-trash"></span>
        {% endif %}
                </td>

                <td>
        {% if item.user_picture %}
                    <span class="glyphicon glyphicon-picture"></span>
        {% endif %}
                </td>

                <td>
        {% if item.user_delete %}
                    <span class="glyphicon glyphicon-trash"></span>
        {% endif %}

                    <a href="{{ url({'for': 'backend-v1-user-profile'}, {'user_id': item.user_id}) }}">
                        {{ item.user_login|escape }}</a>
                </td>

                <td>

                    {{ item.user_name|escape }}
                </td>

                <td>
                    {{ item.user_firstname|escape }}
                </td>

                <td>
                    {{ item.user_lastname|escape }}
                </td>

                <td>
                    {{ static("\Model\User", "getRoleString", [item.user_role])|escape }}
                </td>

                <td nowrap="nowrap">
        {% if item.user_status == constant('\Model\User::STATUS_ACTIVE') %}
                    <span class="text-success">
        {% else %}
                    <span class="text-danger">
        {% endif %}
                        {{ static("\Model\User", "getStatusString", [item.user_status])|escape }}
                    </span>
                </td>

                <td>
                    {{ item.user_cdate|date_dmr }}
                </td>

                <td nowrap="nowrap">
                    <div class="btn-group pull-right" role="group">
                        <a href="{{ url({'for': 'backend-v1-user-accesslog'}, {'user_id': item.user_id}) }}"
                           class="btn btn-default"
                           data-toggle="tooltip"
                           title="История авторизаций"
                                >
                            <span class="glyphicon glyphicon-log-in"></span>
                        </a>


                        <a href="{{ url({'for': 'backend-v1-user-edit'}, {'user_id': item.user_id}) }}"
                           class="btn btn-primary"
                           data-toggle="tooltip"
                           title="Редактировать"
                                >
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>

        {# admin #}
        {% if user.inGroup(user.user_role, 'admin') %}
            {% if item.user_status == constant('\Model\User::STATUS_INACTIVE') %}
            {% elseif item.user_status == constant('\Model\User::STATUS_ACTIVE') %}
                        <a href="javascript:;"
                           data-url="{{ url({'for': 'api-v1-user-toggleban'}) }}"
                           data-params="{{ url('/', {'user_id': item.user_id}) }}"
                           data-confirm="true"
                           data-confirm-message="Вы уверены что хотите заблокировать данного пользователя?"
                           class="btn btn-warning"
                           data-toggle="tooltip"
                           title="Заблокировать"
                                >
                            <span class="glyphicon glyphicon-ban-circle"></span>
                        </a>
            {% else %}
                        <a href="javascript:;"
                           data-url="{{ url({'for': 'api-v1-user-toggleban'}) }}"
                           data-params="{{ url('/', {'user_id': item.user_id}) }}"
                           data-confirm="true"
                           data-confirm-message="Вы уверены что хотите разблокировать данного пользователя?"
                           class="btn btn-info"
                           data-toggle="tooltip"
                           title="Разблокировать"
                                >
                            <span class="glyphicon glyphicon-ok-sign"></span>
                        </a>
            {% endif %}
        {% endif %}

        {# root #}
        {% if user.inGroup(user.user_role, 'root') %}
            {% if !item.user_delete %}
                        <a href="javascript:;"
                           data-url="{{ url({'for': 'api-v1-user-toggledelete'}) }}"
                           data-params="{{ url('/', {'user_id': item.user_id}) }}"
                           data-confirm="true"
                           data-confirm-message="Вы уверены что хотите удалить данного пользователя?"
                           data-callback="fnRemoveTr"
                           class="btn btn-danger"
                           data-toggle="tooltip"
                           title="Удалить"
                                >
                            <span class="glyphicon glyphicon-trash"></span>
                        </a>
            {% else %}
                        <a href="javascript:;"
                           data-url="{{ url({'for': 'api-v1-user-toggledelete'}) }}"
                           data-params="{{ url('/', {'user_id': item.user_id}) }}"
                           data-confirm="true"
                           data-confirm-message="Вы уверены что хотите восстановить данного пользователя?"
                           data-callback="fnRemoveTr"
                           class="btn btn-danger"
                           data-toggle="tooltip"
                           title="Восстановить"
                                >
                            <span class="glyphicon glyphicon-plus-sign"></span>
                        </a>
            {% endif %}
        {% endif %}

                    </div>
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

    {{ partial("page", {'route': {'for': 'backend-v1-user-index'}, 'page': page, 'params_get': params_get, 'disable_arrow_force': true}) }}

</div>

{% endblock %}