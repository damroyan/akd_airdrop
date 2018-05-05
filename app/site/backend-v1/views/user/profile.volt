{% extends "layouts/layout.volt" %}

{% block content %}

<div class="container">
    <h2>Пользователи / {{ u.getName()|escape }}</h2>
    <hr />

    {% if u.user_delete %}
    <div class="well">
        <h4 class="text-danger">Пользователь удален</h4>
    </div>
    {% endif %}

    <table class="table table-striped">
        <tbody>
            <tr>
                <th>Id</th>
                <td>
                    {{ u.user_id|escape }}
                    (<a href="{{ url({'for': 'backend-v1-user-edit'}, {'user_id': u.user_id}) }}">редактировать</a>
                    <span class="glyphicon glyphicon-edit"></span>)
                </td>
            </tr>
            <tr>
                <th>Логин</th>
                <td>{{ u.user_login|escape }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ u.user_email|escape }}</td>
            </tr>
            <tr>
                <th>Псевдоним</th>
                <td>{{ u.user_name|escape }}</td>
            </tr>
            <tr>
                <th>Имя</th>
                <td>{{ u.user_firstname|escape }}</td>
            </tr>
            <tr>
                <th>Фамилия</th>
                <td>{{ u.user_lastname|escape }}</td>
            </tr>
            <tr>
                <th>Дата создания</th>
                <td>{{ u.user_cdate|date_dmr }}</td>
            </tr>
            <tr>
                <th>Статус</th>
                <td>{{ static("\Model\User", "getStatusString", [u.user_status])|escape }}</td>
            </tr>
            <tr>
                <th>Роль</th>
                <td>{{ static("\Model\User", "getRoleString", [u.user_role])|escape }}</td>
            </tr>
        </tbody>
    </table>
</div>

{% endblock %}