{% extends "layouts/minimal-layout.volt" %}

{% block content %}
   {# <div class="color-line"></div>

    <div class="back-link">
        <a href="{{ url({'for':'frontend-v1-user-auth'}) }}" class="btn btn-primary">{{ t.gettext('Back to Login') }}</a>
    </div>

    <div class="login-container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center m-b-md">
                    <h3>{{ t.gettext('Forgot Password?') }}</h3>
                    <small>{{ t.gettext('We will help to recover it!') }}</small>
                </div>
                <div class="hpanel">
                    <div class="panel-body">
                        <p>
                            {{ t.gettext('Enter your email address and your password will be reset and emailed to you.') }}
                        </p>
                        <form id="loginForm"
                              action="javascript:;"
                              method="post"
                              class="js-form"
                              data-url="{{ url({'for': 'api-v1-user-forgot'}) }}"
                              data-redirect-url="{{ url({'for': 'frontend-v1-user-auth'}) }}">

                            <div class="form-group">
                                <label class="control-label" for="username">{{ t.gettext('Email') }}</label>

                                {{ email_field(
                                "user_login",
                                'class': 'form-control',
                                'id': 'label-for-user_login',
                                'placeholder': t.gettext('Example: your@email.com')
                                ) }}

                                <span class="help-block small">{{ t.gettext('Email address that you use while registration') }}</span>
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

                            <button
                                    type="submit"
                                    class="btn btn-success btn-block">{{ t.gettext('Reset password') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                2006-till now
            </div>
        </div>
    </div>#}

{% endblock %}
