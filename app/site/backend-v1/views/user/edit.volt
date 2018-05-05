{% extends "layouts/layout.volt" %}

{% block content %}

<div class="container">
    <h2>
        Пользователи /
    {% if u.user_id %}
        редактирование
    {% else %}
        создание
    {% endif %}
    </h2>

    <hr />

    {% if u.user_delete %}
    <div class="well">
        <h4 class="text-danger">Пользователь удален</h4>
    </div>
    {% endif %}

    <form action="javascript:;"
          method="post"
          class="form-horizontal js-form"
          data-url="{{ url({'for': 'api-v1-user-edit'}) }}"
          data-redirect-url="{{ url({'for': 'backend-v1-user-index'}) }}"
            >

        <div class="form-group">
            <label for="label-for-user_login" class="col-sm-2 control-label">
                Логин</label>
            <div class="col-sm-10">
                {{ text_field(
                    "user_login",
                    'class': 'form-control',
                    'id': 'label-for-user_login',
                    'readonly': u.user_id ? 'readonly' : null,
                    'placeholder': 'Пример: noreply@ava.md'
                ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="label-for-user_email" class="col-sm-2 control-label">
                Email</label>
            <div class="col-sm-10">
                {{ email_field(
                    "user_email",
                    'class': 'form-control',
                    'id': 'label-for-user_email',
                    'placeholder': 'Пример: noreply@ava.md'
                ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="label-for-user_name" class="col-sm-2 control-label">
                Псевдоним</label>
            <div class="col-sm-10">
                {{ text_field(
                    "user_name",
                    'class': 'form-control',
                    'id': 'label-for-user_name',
                    'placeholder': 'Пример: Администратор'
                ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="label-for-user_firstname" class="col-sm-2 control-label">
                Имя</label>
            <div class="col-sm-10">
                {{ text_field(
                    "user_firstname",
                    'class': 'form-control',
                    'id': 'label-for-user_firstname',
                    'placeholder': 'Пример: Иван'
                ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="label-for-user_lastname" class="col-sm-2 control-label">
                Фамилия</label>
            <div class="col-sm-10">
                {{ text_field(
                    "user_lastname",
                    'class': 'form-control',
                    'id': 'label-for-user_lastname',
                    'placeholder': 'Пример: Иванов'
                ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="label-for-user_cdate" class="col-sm-2 control-label">
                Дата создания</label>
            <div class="col-sm-10">
                {{ text_field(
                    "user_cdate",
                    'class': 'form-control',
                    'id': 'label-for-user_cdate',
                    'readonly': 'readonly',
                    'placeholder': 'Пример: 2000-01-01 00:00:00'
                ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="label-for-user_role" class="col-sm-2 control-label">
                Роль</label>
            <div class="col-sm-10">
                {{ select(
                    'user_role',
                    static("\Model\User", "getRoleArray"),
                    'class': 'form-control',
                    'id': 'label-for-user_role'
                ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="label-for-user_status" class="col-sm-2 control-label">
                Статус</label>
            <div class="col-sm-10">
                {{ select(
                    'user_status',
                    static("\Model\User", "getStatusArray"),
                    'class': 'form-control',
                    'id': 'label-for-user_status'
                ) }}
            </div>
        </div>

        <hr />

        <div class="form-group">
            <label for="label-for-user_password" class="col-sm-2 control-label">Пароль</label>
            <div class="col-sm-10">
                {{ password_field(
                    "user_password",
                    'class': 'form-control',
                    'id': 'label-for-user_password',
                    'placeholder': 'Новый пароль'
                ) }}
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="checkbox">
                    <label>
                        {{ check_field(
                            "password",
                            "value": 1
                        ) }}
                        Сменить пароль
                    </label>
                </div>
            </div>
        </div>

        <hr />

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </div>

        {{ hidden_field('user_id') }}
    </form>

</div>

{% endblock %}
