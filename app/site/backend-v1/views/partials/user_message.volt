{#
Стандартная система сообщений
Отображает пользователю внутри системы

ожидает на входе массив массивов
user_messages= [array[
'message_text' => 'text', // обязательный
'message_id'   => int //опциональный,если передан, то стучится по закрытию на сервер, чтобы больше не показывалось
'message_type' => 'string' //опционально, по умолчанию warning
],]

типы message_type

default - серый
primary - синий
success - зеленый
info - голубой
warning - оранжевый
danger - красный
#}




{# пользовательские сообщения #}
{% if user_messages %}
    {% for item in user_messages %}
        <div
                class="alert alert-{% if item['message_type'] %}{{ item['message_type'] }}{% else %}warning{% endif %} alert-dismissible fade in"
                role="alert">
            <button
                    type="button"
                    class="close js-message-close"
                    data-dismiss="alert"
                    data-message_id="{{ item['message_id'] }}"
                    aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            {{ item['message_text'] }}
        </div>
    {% endfor %}
{% endif %}

{# системные сообщения #}
{% if system_messages %}
    {% for item in system_messages %}
        <div
                class="alert alert-{% if item['message_type'] %}{{ item['message_type'] }}{% else %}warning{% endif %} alert-dismissible fade in"
                role="alert">
            <button
                    type="button"
                    class="close js-message-close"
                    data-dismiss="alert"
                    data-message_id="{{ item['message_id'] }}"
                    aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            {{ item['message_text'] }}
        </div>
    {% endfor %}
{% endif %}
