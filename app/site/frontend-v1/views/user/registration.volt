{% extends "layouts/minimal-layout.volt" %}

{% block content %}

  {#  <div class="color-line"></div>
    <div class="back-link">
        <a href="{{ url({'for':'frontend-v1-user-auth'}) }}" class="btn btn-primary">{{ t.gettext('Back to Login') }}</a>
    </div>
    <div class="register-container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center m-b-md">
                    <h3>{{ t.gettext('Registration') }}</h3>
                    <small>{{ t.gettext("Join Skeleton to view it's opportunities") }}</small>
                </div>
                <div class="hpanel">
                    <div class="panel-body">
                        <form
                                action="#"
                                id="loginForm"
                                class="js-form"
                                data-url="{{ url({'for': 'api-v1-user-registration'}) }}"
                                data-redirect-url="{{ url({'for': 'frontend-v1-user-auth'}) }}"
                                data-modal-type="info">

                            <div class="row">

                                <div class="form-group col-lg-12">
                                    <label>Email Address</label>
                                    {{ email_field(
                                        "user_login",
                                        'class': 'form-control',
                                        'id': 'label-for-user_login'
                                    ) }}
                                </div>

                                <div class="form-group col-lg-6">
                                    <label> {{ t.gettext('First Name') }}</label>
                                    {{ text_field(
                                        "user_firstname",
                                        'class': 'form-control',
                                        'id': 'label-for-user_firstname'
                                        ) }}
                                </div>

                                <div class="form-group col-lg-6">
                                    <label>{{ t.gettext('Last Name') }}</label>
                                    {{ text_field(
                                    "user_lastname",
                                    'class': 'form-control',
                                    'id': 'label-for-user_lastname'
                                    ) }}
                                </div>


                                <div class="form-group col-lg-6">
                                    <label>{{ t.gettext('Password') }}</label>
                                    {{ password_field(
                                        "user_password",
                                        'class': 'form-control',
                                        'id': 'label-for-user_password'
                                        ) }}
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>{{ t.gettext('Confirm Password') }}</label>
                                    {{ password_field(
                                        "user_password_reply",
                                        'class': 'form-control',
                                        'id': 'label-for-user_password_reply'
                                        ) }}
                                </div>
                                <div class="checkbox col-lg-6">
                                    <input type="checkbox" class="i-checks" checked name="user_newsletter_subscription_date">
                                    {{ t.gettext('Sigh up for our newsletter') }}
                                </div>
                                {% if captcha_needed %}
                                    <div class="form-group col-lg-6">
                                        <div class="js-grecaptcha">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 100%;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                {% endif %}
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">{{ t.gettext('Register') }}</button>
                                <a class="btn btn-default" href="{{ url({'for':'frontend-v1-index-index'}) }}">{{ t.gettext('Cancel') }}</a>
                            </div>
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