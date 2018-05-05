<!-- Header -->
<div id="header">
    <div class="color-line">
    </div>
    <div id="logo" class="light-version">
        <span>
            Skeleton
        </span>
    </div>
    <nav role="navigation">
        <div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
        <div class="small-logo">
            <span class="text-primary">Skeleton by D.A.</span>
        </div>
        <form
                role="search"
                class="navbar-form-custom"
                method="get"
                action="{{ url({'for':'frontend-v1-index-search'}) }}">
            <div class="form-group">
                <input type="text" placeholder="{{ t.gettext('Search is easy to use...') }}" class="form-control" name="q">
            </div>
        </form>
        <div class="mobile-menu">
            <button type="button" class="navbar-toggle mobile-menu-toggle" data-toggle="collapse" data-target="#mobile-collapse">
                <i class="fa fa-chevron-down"></i>
            </button>
            <div class="collapse mobile-navbar" id="mobile-collapse">
                <ul class="nav navbar-nav">
                    {% if user.user_id %}
                        <li>
                            <a class="" href="{{ url({'for':'frontend-v1-user-profile'},{'user_id':user.user_id}) }}">{{ t.gettext('Profile') }}</a>
                        </li>
                        <li>
                            <a class="" href="{{ url({'for':'frontend-v1-user-logout'}) }}">{{ t.gettext('Logout') }}</a>
                        </li>
                    {% else %}
                        <li>
                            <a class="" href="{{ url({'for':'frontend-v1-user-auth'}) }}">{{ t.gettext('Login') }}</a>
                        </li>
                    {% endif %}
                </ul>
            </div>
        </div>
        <div class="navbar-right">
            <ul class="nav navbar-nav no-borders">
                {% if user.user_id %}
                    <li>
                        <a href="{{ url({'for':'frontend-v1-user-logout'}) }}">
                            <i class="pe-7s-upload pe-rotate-90"></i>
                        </a>
                    </li>
                {% else %}
                    <li>
                        <a href="{{ url({'for':'frontend-v1-user-auth'}) }}">
                            <i class="pe-7s-upload pe-rotate-270"></i>
                        </a>
                    </li>
                {% endif %}
            </ul>
        </div>
    </nav>
</div>