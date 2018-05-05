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
                    {{ t.gettext('Your Company Profile') }}
                </h2>
            </div>
        </div>
    </div>


    <div class="content animate-panel">
        <div class="hpanel">
            <div class="panel-heading">
                <div class="panel-title"> <i class="fa fa-building"></i> {{ t.gettext('Company General Data') }}</div>
            </div>
            <div class="panel-body">
                <form action="javascript:;"
                      method="post"
                      class="form-horizontal js-form"
                      data-url="{{ url({'for': 'api-v1-company-dataEdit'}) }}"
                >
                    <input type="hidden" name="redirect" id="redirect" value="{{ url({'for': 'frontend-v1-index-index'}) }}">
                    <div class="form-group">
                        <label for="label-for-user_login" class="col-sm-3 control-label">
                            {{ t.gettext('Company name') }}</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control"
                                   id="label-for-company_name" name="company_name"
                                   value="{{ company.company_name }}" placeholder="{{ t.gettext('Company Name') }}"
                            />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="label-for-user_login" class="col-sm-3 control-label">
                            {{ t.gettext('Company short description') }}</label>
                        <div class="col-sm-9">
                            <textarea   name="company_description"
                                        id="label-for-company_description"
                                        class="form-control">
                                    {{ company.company_description }}
                            </textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary">{{ t.gettext('Save') }}</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {# todo save button && image size #}
    <div class="content animate-panel">
        <div class="hpanel">
            <div class="panel-heading">
                <div class="panel-title"> <i class="fa fa-photo"></i> {{ t.gettext('Logo') }}</div>
            </div>
            <div class="panel-body">
                <div class="vertical-align js-image-upload col-md-4"
                     data-field-selector="input[name='company_logo']">

                    <div class="text-center admin-upload-image-block" style="width: 190px !important;">
                        <img src="{% if company.company_logo %}{{ image_path(company.company_logo, 50, 50, 'mwh', 'png', true) }}{% else %}/front/nologo.png{% endif %}"
                             class="img-circle m-b js-company-logo" alt="logo">
                        <div class="fileinput-button">
                            <input type="file" name="file" class="fileinput">
                        </div>

                    </div>
                    <div>
                        {{ t.gettext('Please click on image to download new one')}}
                    </div>
                    <input type="hidden" class="js-company-logo-save" name="company_logo">
                </div>
            </div>
            <div class="panel-footer">
                {{ t.gettext('Logo will help us to brand everything that we can. Invoices for example.') }}

            </div>
        </div>

    </div>

{% endblock %}