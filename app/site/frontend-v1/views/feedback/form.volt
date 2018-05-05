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
                    {{ t.gettext('Send feedback!') }}
                </h2>
                <small>{{ t.gettext('We will be happy to help you in any reason') }}</small>
            </div>
        </div>
    </div>

    <div class="content animate-panel">
        <div class="hpanel">
            <div class="panel-body">
                <form
                        class="form-horizontal js-form"
                        id="feedbackform"
                        data-url="{{ url({'for':'api-v1-feedback-send'}) }}"
                        method="post">
                    <div class="form-group">
                        <label for="inputStandard" class="col-lg-2 control-label">{{ t.gettext('Your name') }}*:</label>
                        <div class="col-lg-8">
                            <input type="text" id="inputName" name="feedback_user_name" required class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword" class="col-lg-2 control-label">Email:*</label>
                        <div class="col-lg-8">
                            <input type="text" value="{{ user.user_email }}" required class="form-control" name="feedback_user_email" id="inputEmail">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="standard-list1" class="col-lg-2 control-label">{{ t.gettext('Request Type') }}*:</label>
                        <div class="col-lg-8">
                            <select required class="form-control" id="standard-list1" name="feedback_type">
                                <option value="{{ constant("\Model\Feedback::TYPE_TECHNICAL") }}">{{ t.gettext('Technical issues') }}</option>
                                <option value="{{ constant("\Model\Feedback::TYPE_FINANCIAL") }}">{{ t.gettext('Financial issues') }}</option>
                                <option value="{{ constant("\Model\Feedback::TYPE_OTHER") }}">{{ t.gettext('Other') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label" for="textArea">{{ t.gettext('Message') }}*:</label>
                        <div class="col-lg-8">
                            <textarea required name="feedback_description" class="form-control" id="textArea" rows="3"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="col-lg-offset-2 btn btn-success btn-gradient"><i class="glyphicon glyphicon-send"></i> {{ t.gettext('Send') }}</button>
                </form>
        </div>
    </div>
{% endblock %}