{% extends "layouts/minimal-layout.volt" %}

{% block content %}

    <div class="color-line"></div>

    <div class="back-link">
        <a href="{{ url({'for':'frontend-v1-index-index'}) }}" class="btn btn-primary">{{ t.gettext('Back to Index') }}</a>
    </div>

    <div class="login-container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center m-b-md">
                    <h3>{{ t.gettext('Login to') }} {{ config.system.projectName }}</h3>
                    <small>{{ t.gettext("It's only for the security reasons") }}!</small>
                </div>
                <div class="hpanel">
                    <div class="panel-body">
                        <form id="loginForm"
                              method="post"
                              class="js-form"
                              data-disabled-complete-alert="true"
                              data-url="{{ url({'for': 'api-v1-user-auth'}) }}"
                              data-redirect-url="{{ url({'for': 'frontend-v1-index-index'}) }}"
                             >
                            <div class="form-group">
                                <label class="control-label" for="username">{{ t.gettext('Email') }}</label>
                                {{ email_field(
                                "user_login",
                                'class': 'form-control',
                                'id': 'label-for-user_login',
                                'placeholder': 'example@gmail.com',
                                'required': ''
                                ) }}

                            </div>
                            <div class="form-group">
                                <label class="control-label" for="password">{{ t.gettext('Password') }}</label>
                                {{ password_field(
                                "user_password",
                                'class': 'form-control',
                                'id': 'label-for-user_password',
                                'placeholder': '*******',
                                'required':''
                                ) }}
                            </div>

                            {% if captcha_needed %}
                            <div class="form-group">
                                <div class="js-grecaptcha">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 100%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {% endif %}

                            <div class="form-group">
                                <span class="pull-right">
                                    <a href="{{ url({'for': 'frontend-v1-user-forgot'}) }}">
                                        {{ t.gettext('Forgоt password?') }}</a>
                                </span>

                                <div class="checkbox">
                                    <label>
                                        {{ check_field('member', 'value': '1') }}
                                        {{ t.gettext('Remember me') }}
                                    </label>
                                </div>
                            </div>

                            <button
                                    class="btn btn-success btn-block"
                                    type="submit"
                            >{{ t.gettext('Login') }}</button>

                            <a class="btn btn-default btn-block" href="{{ url({'for': 'frontend-v1-user-registration'}) }}">{{ t.gettext('Register') }}</a>

                            {{ hidden_field("_for") }}
                            {{ hidden_field("_params") }}
                            {{ hidden_field("_query") }}
                            {{ hidden_field("redirect") }}

                        </form>
                    </div>

                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                2006-till now <b>{{ config.system.projectName }}</b>
            </div>
        </div>
    </div>

{% endblock %}