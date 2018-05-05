{% extends "layouts/layout.volt" %}

{% block content %}


    <div class="normalheader transition animated fadeIn">
        <div class="hpanel">
            <div class="panel-body">
                <a class="small-header-action" href="">
                    <div class="clip-header">
                        <i class="fa fa-arrow-up"></i>
                    </div>
                </a>


                <h2 class="font-light m-b-xs">
                    {{ t.gettext('Your profile') }}
                </h2>
            </div>
        </div>
    </div>

    {# todo save button && image size #}
    <div class="content animate-panel">
        <div class="hpanel">
            <div class="panel-heading">
                <div class="panel-title"> <i class="fa fa-photo"></i> {{ t.gettext('Photo') }}</div>
            </div>
            <div class="panel-body">
                <div class="vertical-align js-image-upload col-md-4"
                     data-field-selector="input[name='user_picture']">

                    <div class="text-center admin-upload-image-block" style="width: 190px !important;">
                        <img src="{% if user.user_picture %}{{ image_path(user.user_picture, 50, 50, 'mwh', 'png', true) }}{% else %}/front/nologo.png{% endif %}" class="img-circle m-b js-user-picture" alt="logo">
                        <div class="fileinput-button">
                            <input type="file" name="file" class="fileinput">
                        </div>

                    </div>
                    <div>
                        {{ t.gettext('Please click on image to download new one')}}
                    </div>
                    <input type="hidden" class="js-user-picture-save" name="user_picture">
                </div>
            </div>
            <div class="panel-footer">
                {{ t.gettext('Good image will looks better in account.') }}

            </div>
        </div>

    </div>

    <div class="content animate-panel">
        <div class="hpanel">
            <div class="panel-heading">
                <div class="panel-title"> <i class="fa fa-male"></i> {{ t.gettext('Personal data change') }}</div>
            </div>
            <div class="panel-body">

                <form action="javascript:;"
                      method="post"
                      class="form-horizontal js-form"
                      data-url="{{ url({'for': 'api-v1-user-profileEdit'}) }}"
                >
                    <input type="hidden" name="redirect" value="{{ url({'for': 'frontend-v1-index-index'}) }}">
                    <div class="form-group">
                        <label for="label-for-user_login" class="col-sm-3 control-label">
                            {{ t.gettext('First name') }}</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control"
                                   id="label-for-user_firstname" name="user_firstname"
                                   value="{{ user.user_firstname }}" placeholder="{{ t.gettext('Your First Name') }}"
                            />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="label-for-user_login" class="col-sm-3 control-label">
                            {{ t.gettext('Last name') }}</label>
                        <div class="col-sm-9">
                            <input  type="text" class="form-control"
                                    id="label-for-user_lastname" name="user_lastname"
                                    value="{{ user.user_lastname }}" placeholder="{{ t.gettext('Your Last Name') }}"
                            />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="label-for-user_login" class="col-sm-3 control-label">
                            {{ t.gettext('Account language') }}</label>
                        <div class="col-sm-9">
                           <select name="user_lang" class="form-control">
                                {% for item in accepted_languages %}
                                    <option value="{{ item }}"
                                            {% if user.user_lang == item %}selected{% endif %}>{{ item }}</option>
                                {% endfor  %}
                           </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary">{{ t.gettext('Save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="panel-footer">
                {{ t.gettext('Please use correct data. It will help us to collaborate better') }}
            </div>
        </div>

    </div>

    <div class="content animate-panel">
        <div class="hpanel">
            <div class="panel-heading">
                <div class="panel-title"> <i class="fa fa-th-large"></i> {{ t.gettext('Password Change') }}</div>
            </div>
            <div class="panel-body">
                <form action="javascript:;"
                      method="post"
                      class="form-horizontal js-form"
                      data-url="{{ url({'for': 'api-v1-user-password'}) }}"
                >
                    <input type="hidden" name="redirect" value="{{ url({'for': 'frontend-v1-user-profile'}) }}">
                    <div class="form-group">
                        <label for="label-for-user_login" class="col-sm-3 control-label">
                            {{ t.gettext('Old Password') }}</label>
                            <div class="col-sm-9">
                                {{ password_field(
                                "user_password_old",
                                'class': 'form-control',
                                'id': 'label-for-user_password',
                                'placeholder': t.gettext('Your old password')
                                ) }}
                            </div>
                    </div>
                    <div class="form-group">
                        <label for="label-for-user_login" class="col-sm-3 control-label">
                            {{ t.gettext('New Password') }}</label>
                        <div class="col-sm-9">
                            {{ password_field(
                            "user_password",
                            'class': 'form-control',
                            'id': 'label-for-user_new_password',
                            'placeholder': t.gettext('Your new password')
                            ) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="label-for-user_login" class="col-sm-3 control-label">
                            {{ t.gettext('Confirm New Password') }}</label>
                        <div class="col-sm-9">
                            {{ password_field(
                            "user_password_reply",
                            'class': 'form-control',
                            'id': 'label-for-user_new_password_confirm',
                            'placeholder': t.gettext('Confirm new password')
                            ) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary">{{ t.gettext('Save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="panel-footer">
                {{ t.gettext('Use security save password. It will help to save your account from everyone') }}
            </div>
        </div>
    </div>

    <div class="content animate-panel">
        <div class="hpanel">
            <div class="panel-heading">
                <div class="panel-title"> <i class="fa fa-male"></i> {{ t.gettext('Account Delete') }}</div>
            </div>
            <div class="panel-body">
                {{ t.gettext('Be carefull. It can destroy all your data and history from the system! Please doublecheck that you want to do that. ') }}
                <a class="btn btn-default"
                   data-confirm="1"
                   data-confirm-message="{{ t.gettext('Are you sure you want delete your account? It will crush all your data.') }}"
                   data-url="{{ url({'for':'api-v1-user-delete','user_id':user.user_id})  }}">
                    {{ t.gettext('Disable Account') }}
                </a>
            </div>
        </div>
    </div>

{% endblock %}