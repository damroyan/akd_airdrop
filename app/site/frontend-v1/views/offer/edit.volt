{% extends "layouts/layout.volt" %}

{% block content %}

<div class="hpanel">
    <h2>
        Оффер /
    {% if o.offer_id %}
        редактирование
    {% else %}
        создание
    {% endif %}

    </h2>

    <hr />



    <hr />

    <div>

        <form action="javascript:;"
              method="post"
              class="form-horizontal js-form"
              data-url="{{ url({'for': 'api-v1-offer-edit'}) }}"
              data-redirect-url="{{ url({'for': 'frontend-v1-offer-descedit'}, {'offer_id': o.offer_id}) }}"
        >

            <div class="form-group">
                <label for="label-for-user_login" class="col-sm-2 control-label">
                    Название</label>
                <div class="col-sm-10">
                    {{ text_field(
                    "offer_name",
                    'class': 'form-control'
                    ) }}
                </div>

            </div>

            <div class="form-group">
                <label for="label-for-user_login" class="col-sm-2 control-label">
                    Код валюты</label>
                <div class="col-sm-10">
                    {{ text_field(
                    "offer_code",
                    'class': 'form-control'
                    ) }}
                </div>

            </div>

            <hr>

            <div class="form-group">
                <label for="label-for-user_email" class="col-sm-2 control-label">
                    Тип</label>
                <div class="col-sm-10">
                    {{ select(
                    'offer_type',
                    static("\Model\Offer", "getTypeArray"),
                    'class': 'form-control',
                    'id': 'label-for-user_role'
                    ) }}
                </div>
            </div>

            <div class="form-group">
                <label for="label-for-user_firstname" class="col-sm-2 control-label">
                    Рассчетный доход</label>
                <div class="col-sm-10">
                    {{ text_field(
                    "offer_profit",
                    'class': 'form-control',
                    'id': 'label-for-user_firstname'
                    ) }}
                </div>
            </div>

            <div class="form-group">
                <label for="label-for-user_lastname" class="col-sm-2 control-label">
                    Ссылка на оффер</label>
                <div class="col-sm-10">
                    {{ text_field(
                    "offer_url",
                    'class': 'form-control',
                    'id': 'label-for-user_lastname'
                    ) }}
                </div>
            </div>

            <div class="form-group">
                <label for="label-for-user_cdate" class="col-sm-2 control-label">
                    Ссылка на сайт оффера</label>
                <div class="col-sm-10">
                    {{ text_field(
                    "offer_site_url",
                    'class': 'form-control',
                    'id': 'label-for-user_cdate'
                    ) }}
                </div>
            </div>

            <div class="form-group">
                <label for="label-for-user_role" class="col-sm-2 control-label">
                    Дата окончания оффера</label>
                <div class="col-sm-10">
                    {{ text_field(
                    "offer_end_date",
                    'class': 'form-control',
                    'id': 'label-for-user_cdate',
                    'placeholder': 'Пример: 2000-01-01'
                    ) }}
                </div>
            </div>

            <div class="form-group">
                <label for="label-for-user_status" class="col-sm-2 control-label">
                    Рейтинг</label>
                <div class="col-sm-10">
                    {{ text_field(
                    "offer_rating",
                    'class': 'form-control',
                    'id': 'label-for-user_cdate',
                    'placeholder': 'от 1 до 5'
                    ) }}
                </div>
            </div>

            <div class="form-group">
                <label for="label-for-user_status" class="col-sm-2 control-label">
                    Статус</label>
                <div class="col-sm-10">
                    {{ select(
                    'offer_status',
                    static("\Model\Offer", "getStatusArray"),
                    'class': 'form-control',
                    'id': 'label-for-user_status'
                    ) }}
                </div>
            </div>

            <div class="form-group">
                <label for="label-for-user_status" class="col-sm-2 control-label">
                    Просмотры</label>
                <div class="col-sm-10">
                    {{ text_field(
                    "offer_views",
                    'class': 'form-control',
                    'id': 'label-for-user_cdate'
                    ) }}
                </div>
            </div>

            <hr />

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <div class="checkbox">
                        <label>
                            {{ check_field(
                            "offer_priority",
                            "value": 1
                            ) }}
                            Приоритетный
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <div class="checkbox">
                        <label>
                            {{ check_field(
                            "offer_featured",
                            "value": 1
                            ) }}
                            Featured
                        </label>
                    </div>
                </div>
            </div>

            <hr />

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">Сохранить и продолжить</button>
                </div>
            </div>
            {{ hidden_field('offer_id') }}

        </form>
    </div>


</div>

{% endblock %}

