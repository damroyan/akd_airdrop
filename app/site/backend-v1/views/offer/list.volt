{% extends "layouts/layout.volt" %}

{% block content %}

<div class="container">
    <h2>
        Список офферов

        <small>{{ page.total_items }} элементов</small>
    </h2>
    <hr />

    <div class="pull-right">
        <a href="{{ url({'for': 'backend-v1-offer-edit'}) }}"
           class="btn btn-danger"
                >
            <span class="glyphicon glyphicon-plus"></span>
            Добавить оффер</a>
    </div>

    <form action="{{ url({'for': 'backend-v1-offer-list'}) }}" method="get" class="form-inline">
        <div class="input-group">
            {{ text_field(
                "query",
                'class': 'form-control',
                'placeholder': 'Id / Имя '
            ) }}
        </div>


        <div class="input-group">
            {{ select(
            'offer_status',
            static("\Model\Offer", "getStatusArray"),
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
                "offer_delete",
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
                    <span class="glyphicon glyphicon-picture"></span>
                </th>
                <th>Имя</th>
                <th>Тип</th>
                <th>Профит</th>
                <th>Дата окончания</th>
                <th>Рейтинг</th>
                <th>Приоритет</th>
                <th>Статус</th>
                <th>Просмотры</th>
            </tr>
        </thead>

        <tbody>

    {% for item in page.items %}

            <tr>
                <td>
                    {{ item.offer_id|escape }}
                </td>

                <td>
        {% if item.offer_picture %}
                    <span class="glyphicon glyphicon-picture"></span>
        {% endif %}
                </td>

                <td>

                    {{ item.offer_name|escape }}
                </td>

                <td>
                    {{ static("\Model\Offer", "getTypeString", [item.offer_type])|escape }}
                </td>

                <td>
                    {{ item.offer_profit|escape }}
                </td>

                <td>
                    {{ item.offer_end_date|date_dmr }}
                </td>

                <td>
                    {{ item.offer_rating|escape }}
                </td>

                <td>
                    {{ item.offer_priority|escape }}
                </td>

                <td nowrap="nowrap">
        {% if item.offer_status == constant('\Model\User::STATUS_ACTIVE') %}
                    <span class="text-success">
        {% else %}
                    <span class="text-danger">
        {% endif %}
                        {{ static("\Model\User", "getStatusString", [item.offer_status])|escape }}
                    </span>
                </td>

                <td>
                    {{ item.offer_views|escape }}
                </td>

                <td nowrap="nowrap">
                    <div class="btn-group pull-right" role="group">

                        <a href="{{ url({'for': 'backend-v1-offer-edit'}, {'offer_id': item.offer_id}) }}"
                           class="btn btn-primary"
                           data-toggle="tooltip"
                           title="Редактировать"
                                >
                            <span class="glyphicon glyphicon-edit"></span>
                        </a>

                        {% if item.offer_status == constant('\Model\Offer::STATUS_ACTIVE') %}
                        <a href="javascript:;"
                           data-url="{{ url({'for': 'api-v1-offer-toggleban'}) }}"
                           data-params="{{ url('/', {'offer_id': item.offer_id}) }}"
                           data-confirm="true"
                           data-confirm-message="Вы уверены что хотите деактивировать оффер?"
                           class="btn btn-warning"
                           data-toggle="tooltip"
                           title="Деактивировать"
                        >
                            <span class="glyphicon glyphicon-ban-circle"></span>
                        </a>
                        {% else %}
                        <a href="javascript:;"
                           data-url="{{ url({'for': 'api-v1-offer-toggleban'}) }}"
                           data-params="{{ url('/', {'offer_id': item.offer_id}) }}"
                           data-confirm="true"
                           data-confirm-message="Вы уверены что хотите активировать оффер?"
                           class="btn btn-info"
                           data-toggle="tooltip"
                           title="Активировать"
                        >
                            <span class="glyphicon glyphicon-ok-sign"></span>
                        </a>
                        {% endif %}

                        {% if !item.offer_delete %}
                        <a href="javascript:;"
                           data-url="{{ url({'for': 'api-v1-offer-toggledelete'}) }}"
                           data-params="{{ url('/', {'offer_id': item.offer_id}) }}"
                           data-confirm="true"
                           data-confirm-message="Вы уверены что хотите удалить оффер?"
                           data-callback="fnRemoveTr"
                           class="btn btn-danger"
                           data-toggle="tooltip"
                           title="Удалить"
                        >
                            <span class="glyphicon glyphicon-trash"></span>
                        </a>
                        {% else %}
                        <a href="javascript:;"
                           data-url="{{ url({'for': 'api-v1-offer-toggledelete'}) }}"
                           data-params="{{ url('/', {'offer_id': item.offer_id}) }}"
                           data-confirm="true"
                           data-confirm-message="Вы уверены что хотите восстановить оффер?"
                           data-callback="fnRemoveTr"
                           class="btn btn-danger"
                           data-toggle="tooltip"
                           title="Восстановить"
                        >
                            <span class="glyphicon glyphicon-plus-sign"></span>
                        </a>
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

    {{ partial("page", {'route': {'for': 'backend-v1-offer-list'}, 'page': page, 'params_get': params_get, 'disable_arrow_force': true}) }}

</div>

{% endblock %}