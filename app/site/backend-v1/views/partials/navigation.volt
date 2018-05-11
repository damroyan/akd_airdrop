<nav class="navbar navbar-fixed-top navbar-default" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#bs-navbar" aria-controls="bs-navbar" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand" href="{{ url({'for': 'backend-v1-index-index'}) }}">
                Админка
            </a>
        </div>

        <nav id="bs-navbar" class="collapse navbar-collapse">

{% if user.user_id %}

    {% if user.inGroup(user.user_role, 'moderator') %}

            <ul class="nav navbar-nav navbar-left">

                <li class=" {% if preg_match('@^user_@ui', header_tab) %} active {% endif %} ">
                    <a  href="javascript:;"
                        class="dropdown-toggle"
                        data-toggle="dropdown"
                            >
                        Пользователи
                        <span class="caret"></span></a>

                    <ul class="dropdown-menu" role="menu">
                        <li class=" {% if preg_match('@^user_user@ui', header_tab) %} active {% endif %} ">
                            <a href="{{ url({'for': 'backend-v1-user-index'}) }}">
                                Пользователи</a></li>

                        <li class=" {% if preg_match('@^user_accesslog@ui', header_tab) %} active {% endif %} ">
                            <a href="{{ url({'for': 'backend-v1-user-accesslog'}) }}">
                                История авторизаций</a></li>
                    </ul>
                </li>
                <li class=" {% if preg_match('@^offer@ui', header_tab) %} active {% endif %} ">
                    <a  href="{{ url({'for': 'backend-v1-offer-list'}) }}"
                    >
                        Офферы
                    </a>
                </li>
                <li class=" {% if preg_match('@^user_@ui', header_tab) %} active {% endif %} ">
                    <a  href="{{ url({'for': 'frontend-v1-feedback-form'}) }}"
                    >
                        Feedback
                    </a>

                </li>
            </ul>

    {% endif %}

{% endif %}

{% if user.user_id %}
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="{{ url({'for':'frontend-v1-index-index'}) }}">{{ t.gettext('Back to Front') }}</a>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        Профиль
                        <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="{{ url({'for': 'backend-v1-user-profile'}, {'user_id': user.user_id}) }}">
                                {{ user.getName() }}</a></li>

                        <li>
                            <a href="{{ url({'for': 'frontend-v1-user-logout'}) }}">
                                {{ t.gettext('Exit') }}</a></li>
                    </ul>
                </li>
            </ul>
{% endif %}

        </nav>

    </div>
</nav>