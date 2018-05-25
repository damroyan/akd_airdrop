{% extends "layouts/layout.volt" %}

{% block content %}

<div class="hpanel js-offer_edit">
    <h2>
        Оффер /
    {% if o.offer_id %}
        редактирование
    {% else %}
        создание
    {% endif %}

    </h2>

    <div class="panel-body">

        <form action="javascript:;"
              method="post"
              class="form-horizontal js-form"
              data-url="{{ url({'for': 'api-v1-offer-edit'}) }}"
              data-redirect-url="{{ url({'for': 'frontend-v1-offer-list'}, {'offer_id': o.offer_id}) }}"

        >

            <div class="form-group">
                <label for="label-for-user_login" class="col-sm-2 control-label">
                    Название</label>
                <div class="col-sm-10">
                    {{ text_field(
                        "offer_name",
                        'class': 'form-control',
                        'placeholder': 'BITCOIN'
                    ) }}
                </div>

            </div>

            <div class="form-group">
                <label for="label-for-user_login" class="col-sm-2 control-label">
                    Код валюты</label>
                <div class="col-sm-10">
                    {{ text_field(
                        "offer_code",
                        'class': 'form-control',
                        'placeholder': 'BTC'
                    ) }}
                </div>

            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label"> {{ t.gettext('Image') }}</label>

                <div class="col-sm-10">
                    <div class="panel-body">
                        <div class="vertical-align">

                            <div class="text-center admin-upload-image-block js-image-upload"
                                 style="width: 190px !important;"
                                 data-image-blank="/front/nologo.png"
                                 data-callback="offer_picture"
                                 data-field-selector="input[name='offer_picture']">
                                <img src="{% if o.offer_picture %}{{ image_path(o.offer_picture, 140, 140, '1x1', 'png', true) }}{% else %}/front/nologo.png{% endif %}"
                                     class="img-circle m-b"
                                     data-element="offer_picture"
                                     style="display: block; width: 140px !important; height: 140px !important;"
                                     alt="" />

                                <div class="fileinput-button"
                                     data-fileupload-ration="140x140_hires">
                                    <input type="file" name="file" class="fileinput" />
                                </div>

                                {{ hidden_field("offer_picture") }}
                            </div>
                            <div>
                                {{ t.gettext('Please click on image to download new one ') | escape}}
                            </div>
                        </div>
                    </div>
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
                    <div class="input-group m-b">
                        <span class="input-group-addon">$</span>
                        {{ text_field(
                            "offer_profit",
                            'class': 'form-control',
                            'id': 'label-for-user_firstname',
                            'placeholder': '0.00'
                        ) }}
                    </div>
                </div>
            </div>

            <div id="add_field_area">
                {% for item in offer_urls%}
                <div class="form-group">
                    <label for="label-for-user_lastname" class="col-sm-2 control-label">
                        Ссылка на оффер
                        {%if loop.index == 1%}
                        <small class="bg-info js_add_button"><a href="javascript:;">+добавить еще ссылку</a> </small>
                        {%else%}
                        {{loop.index}}
                        {%endif%}
                    </label>
                    <div class="col-sm-10">
                        {{ text_field(
                        "offer_url[]",
                        'class'         : 'form-control',
                        'id'            : 'label-for-user_lastname',
                        'placeholder'   : 'ОБЕРНУТАЯ В byt.ly!',
                        'value'         : item
                        ) }}
                    </div>
                </div>

                {%endfor%}

            </div>


            <div class="form-group">
                <label for="label-for-user_cdate" class="col-sm-2 control-label">
                    Ссылка на сайт оффера</label>
                <div class="col-sm-10">
                    {{ text_field(
                        "offer_site_url",
                        'class': 'form-control',
                        'id': 'label-for-user_cdate',
                        'placeholder'   : 'просто ссылка на сайт'
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
                        'data-datepicker':'true',
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
                        'id': 'label-for-user_cdate',
                        'placeholder' : 'Дефолтное значение'
                    ) }}
                </div>
            </div>

            <div class="form-group">
                <label for="label-for-user_status" class="col-sm-2 control-label">
                    {{ t.gettext('Offer description') }}
                </label>
                <div class="col-sm-10">
                    {{ text_area(
                        "offer_description",
                        'class': 'form-control'
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

