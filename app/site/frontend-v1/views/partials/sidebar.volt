<!-- Navigation -->
<aside id="menu">
    <div id="navigation">

        {% if user.user_id %}
        <div class="profile-picture">
            <a href="{{ url({'for':'frontend-v1-user-profile'},{'user_id':user.user_id}) }}">
                <img src="{% if user.user_picture %}{{ image_path(user.user_picture, 50, 50, 'mwh', 'png', true) }}{% else %}/front/nologo.png{% endif %}" class="img-circle m-b js-user-picture" alt="logo">
            </a>

            <div class="stats-label text-color">
                <span class="font-extra-bold font-uppercase">{{ user.user_name }}</span>
                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false">
                        <small class="text-muted">{{ t.gettext('More') }} <b class="caret"></b></small>
                    </a>
                    <ul class="dropdown-menu animated flipInX m-t-xs">
                        <li><a href="{{ url({'for': 'frontend-v1-user-profile'},{'user_id':user.user_id}) }}">{{ t.gettext('Profile') }}</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ url({'for': 'frontend-v1-user-logout'}) }}">{{ t.gettext('Logout') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
        {% endif %}

        <ul class="nav" id="side-menu">
            <li {% if active_tab == 'index' %} class="active" {% endif %}>
                <a href="{{ url({'for':'frontend-v1-index-index'}) }}"> <span class="nav-label">{{ t.gettext('Main Page') }}</span></a>
            </li>

            <li {% if active_tab == 'offers' %} class="active" {% endif %}>
                <a href="{{ url({'for':'frontend-v1-index-index'}) }}"> <span class="nav-label">{{ t.gettext('Active Offers') }}</span></a>
            </li>

            <li {% if active_tab == 'closed_offers' %} class="active" {% endif %}>
                <a href="{{ url({'for':'frontend-v1-index-index'}) }}"> <span class="nav-label">{{ t.gettext('Closed Offers') }}</span></a>
            </li>

            <li {% if active_tab == 'faq' %} class="active" {% endif %}>
                <a href="{{ url({'for':'frontend-v1-index-index'}) }}"> <span class="nav-label">{{ t.gettext('FAQ') }}</span></a>
            </li>

            {% if user.user_id %}

                {# если нужна дополнительная фильтрация в зависимости от роли пользвоателя #}
                {% if user.user_role == 'admin' %}
                    {# пункты меню одмина #}

                    <li class=" {% if preg_match('@^user_@ui', active_tab) %} active {% endif %} ">
                        <a href="javascript:;">
                            <span class="nav-label">{{ t.gettext('Офферы') }}</span>
                            <sup>admin</sup>


                            <span class="fa arrow"></span></a>


                        <ul class="nav nav-second-level">
                            <li class=" {% if preg_match('@^user_user@ui', active_tab) %} active {% endif %} ">
                                <a href="{{ url({'for': 'frontend-v1-offer-list'}) }}">
                                    {{ t.gettext('Список') }}</a></li>

                            <li class=" {% if preg_match('@^user_accesslog@ui', active_tab) %} active {% endif %} ">
                                <a href="{{ url({'for': 'frontend-v1-offer-edit'}) }}">
                                    {{ t.gettext('Создать новый') }}</a></li>
                        </ul>
                    </li>

                {% elseif user.user_role == 'moderator' %}
                    {# пункты меню модератора #}
                {% endif %}

                <li>
                    <a href="{{ url({'for': 'frontend-v1-user-profile'},{'user_id':user.user_id}) }}">{{ t.gettext('Profile') }}</a>
                </li>
                <li>
                    <a href="{{ url({'for': 'frontend-v1-user-logout'}) }}">{{ t.gettext('Logout') }}</a>
                </li>
            {% else %}
                {# здесь пункты меню неавторизованного #}
               {# <li>
                    <a href="{{ url({'for':'frontend-v1-user-auth'}) }}">{{ t.gettext('Login') }}</a>
                </li>#}
            {% endif %}

            {# а сюда все общие для любого типа пользователей #}
            <li class=" {% if preg_match('@^feedback_@ui', active_tab) %} active {% endif %} ">
                <a  href="{{ url({'for': 'frontend-v1-feedback-form'}) }}">
                    <span class="nav-label">{{ t.gettext('Feedback') }}</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
