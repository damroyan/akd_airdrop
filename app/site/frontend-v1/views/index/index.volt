{% extends "layouts/layout.volt" %}

{% block content %}
    <div class="content animate-panel">
        <div class="hpanel blog-article-box">
            <div class="panel-heading">
                <h4>Skeleton Startup FAUC MVP приветсвует тебя</h4>
                <small>FAUC MVP - Fast As yoU Can Minimal Viable Product </small>
                <div class="text-muted small">
                    Created by: <span class="font-bold">Dmitry Amroyan</span>
                </div>
            </div>
            <div class="panel-body">
                <p>Привет, <b>{% if user.user_id %} {{ user.getName() }} {% else %} Незнакомец {% endif %}!</b></p>

                <p>Это главная страница базового проекта. Цель и задача - быстрое разворачивание твоих идей. Здесь я наверное буду описывать какие-то основные моменты как этим пользоваться.
                    Начальная комплектация должна содержать в себе модули:
                </p>

                <p>
                    <h4>Физические:</h4>
                    <ul>
                        <li>Пользователя (регистрация, авторизация, напоминалка пароля, смена пароля, выход)</li>
                        <li>Фидбека</li>
                        <li>Админка к этому</li>
                    </ul>
                </p>

                <p>
                    <h4>Технические:</h4>
                    <ul>
                        <li>Локализации</li>
                        <li>Хелперы</li>
                        <li>Отправка мейлов</li>
                    </ul>
                </p>
                <p>
                    Подробно компоненты и их использование должны быть описаны здесь: <a href="https://bitbucket.org/damroyan/skeleton/wiki/browse/" target="_blank">WIKI</a>
                </p>

                <p>
                    Короче, если ты это видишь, значит проект завелся, теперь можно начинать навешивать все, что тебе кажется нужным навешивать.
                </p>

                <p>
                    Удачи! Новые идеи это всегда круто :)
                </p>
            </div>
        </div>
    </div>

{% endblock %}