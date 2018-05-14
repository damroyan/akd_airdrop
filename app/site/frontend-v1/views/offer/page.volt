{% extends "layouts/layout.volt" %}

{% block content %}
    <div class="row">
        <div class="col-md-9">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <img src="{% if offer.offer_picture %}{{ image_path(offer.offer_picture, 140, 140, '1x1', 'png', true) }}{% else %}/front/img/logo.png{% endif %}" class="ico-icon">
                        </div>
                        <div class="col-sm-10">
                            <h1>{{offer.offer_name}}</h1>
                            <div class="badges">
                                {% for i in 1..offer.offer_rating %}
                                <i class="fa fa-star text-warning"></i>
                                {% endfor %}
                                | <i class="fa fa-money"></i> <span class="font-uppercase">{{ t.gettext('Estimated value') }}:</span>
                                ${{offer.offer_profit}} |
                                <i class="fa fa-street-view"></i> <span class="font-uppercase">{{ t.gettext('Views') }}:</span> {{offer.offer_views}}
                            </div>
                        </div>
                    </div>
                    <div class="row mt10">
                        <div class="col-sm-12">
                        {{offer.offer_description}}
                        </div>
                    </div>

                    <div class="row mt10">
                        <div class="col-sm-12">
                            <a href="{{ offer.offer_url }}" class="btn btn-success text-capitalize" style="font-size: 28px;" target="_blank"><i class="fa fa-check-circle"></i> {{ t.gettext('Activate offer') }}</a> <br/>
                            <span class="text-uppercase">{{ t.gettext('or') }}</span> <a href="{{ offer.offer_site_url }}" target="_blank">{{ t.gettext('Visit site') }}</a>
                        </div>
                    </div>
                    <hr />

                    <hr />
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <p class="text-danger">
                                {{ t.gettext("Please never share your private key with anyone as they will be able to steal money from you!") }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ partial('right_col') }}
    </div>
{% endblock %}