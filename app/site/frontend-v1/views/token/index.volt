{% extends "layouts/minimal-layout.volt" %}

{% block content %}



    <div class="color-line"></div>

    <div class="back-link">
        <a href="{{ url({'for':'frontend-v1-index-index'}) }}" class="btn btn-primary">{{ t.gettext('Back to Main Page') }}</a>
    </div>

    <div class="login-container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center m-b-md">
                    <h3>{{ t.gettext('This Action should be confirmed') }}</h3>
                    <small>{{ t.gettext("It's only for the security reasons") }}!</small>
                </div>
                <div class="hpanel">
                    <div class="panel-body">
                    {% if action_token.action_token_status != constant('\Model\ActionToken::STATUS_ACTIVE') %}

                        <div class="well">
                            {{ t.gettext('The current link was already used before') }}
                            {{ t.gettext('Contact Support for more information') }}
                        </div>

                    {% elseif action_token.action_token_edate|timestamp <= 'now'|timestamp %}

                        <div class="well">
                            {{ t.gettext('The current link are not available because time reason.') }}
                            {{ t.gettext('Contact Support for more information') }}
                        </div>

                    {% else %}
                        <form action="javascript:;"
                              method="post"
                              class="form-horizontal js-form"
                              data-url="{{ url({'for': 'api-v1-token-index'}) }}"
                              data-redirect-url="{{ url({'for': 'backend-v1-index-index'}) }}"
                        >


                                {% if action_token.action_token_action == constant('\Model\ActionToken::ACTION_PASSWORD_CHANGE') %}

                                    <p>
                                        {{ t.gettext('Please confirm that you need to change password') }}
                                    </p>

                                    <div class="form-group">
                                        <label for="label-for-user_password" class="col-sm-3 control-label">
                                            {{ t.gettext('New password') }}</label>
                                        <div class="col-sm-9">
                                            {{ text_field(
                                            "user_password",
                                            'class': 'form-control',
                                            'id': 'label-for-user_password'
                                            ) }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="label-for-user_password_reply" class="col-sm-3 control-label">
                                            {{ t.gettext('New password confirm') }}</label>
                                        <div class="col-sm-9">
                                            {{ text_field(
                                            "user_password_reply",
                                            'class': 'form-control',
                                            'id': 'label-for-user_password_reply'
                                            ) }}
                                        </div>
                                    </div>

                                {% elseif action_token.action_token_action == constant('\Model\ActionToken::ACTION_USER_REGISTRATION') %}

                                    <p class="text-center">
                                        {{ t.gettext('To activate your account please push the button below') }}
                                    </p>

                                {% else %}

                                    <p>
                                        {{ t.gettext('The current link are not available because time reason.') }}
                                        {{ t.gettext('Contact Support for more information') }}
                                    </p>

                                {% endif %}

                                <p class="text-center">
                                    <button type="submit" class="btn btn-primary">
                                        {{ t.gettext('Confirm Action') }}
                                    </button>
                                </p>

                            {{ hidden_field('action_token_id') }}
                            {{ hidden_field('action_type') }}
                        </form>

                    {% endif %}
                    </div>
                </div>
            </div>
        </div>
    </div>

{% endblock %}