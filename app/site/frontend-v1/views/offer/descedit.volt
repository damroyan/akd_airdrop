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

    {% if o.offer_id %}
    <div class="form-group">
        <div class="panel-heading">
            <div class="panel-title"> <i class="fa fa-photo"></i> {{ t.gettext('Фото или лого') }}</div>
        </div>
        <div class="panel-body">
            <div class="vertical-align">

                <div class="text-center admin-upload-image-block js-image-upload"
                     style="width: 190px !important;"
                     data-image-blank="/front/nologo.png"
                     data-callback="user_picture"
                     data-field-selector="input[name='user_picture']">
                    <img src="{% if o.offer_picture %}{{ image_path(o.offer_picture, 140, 140, '1x1', 'png', true) }}{% else %}/front/nologo.png{% endif %}"
                         class="img-circle m-b"
                         data-element="user_picture"
                         style="display: block; width: 140px !important; height: 140px !important;"
                         alt="" />

                    <div class="fileinput-button"
                         data-fileupload-ration="140x140_hires">
                        <input type="file" name="file" class="fileinput" />
                    </div>

                    {{ hidden_field("user_picture") }}
                </div>
                <div>
                    {{ t.gettext('Please click on image to download new one ') | escape}}
                </div>
            </div>
        </div>

    </div>
    {% endif %}

    <div class="form-group">
        <div class="panel-heading">
            <div class="panel-title"> <i class="fa fa-photo"></i> {{ t.gettext('Описание оффера') }}</div>
        </div>
        {{ text_area(
        "offer_description",
        'class': 'form-control',
        'id': 'editor'
        ) }}

    </div>

    <div class="col-sm-offset-0 col-sm-10">
        <button id='edit' class="btn btn-primary">Сохранить</button>
    </div>

    <br><br><br><br><br><br><br><br><br>



</div>

{% endblock %}

