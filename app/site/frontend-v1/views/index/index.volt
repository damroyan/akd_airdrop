{% extends "layouts/layout.volt" %}

{% block content %}

        <div class="row">

            <div class="col-md-9">
                {% if offers|length == 0 %}
                <div class="hpanel">
                    <div class="panel-body">

                        <h2 class="font-light m-b-xs text-center">
                            На данный момент нет активных офферов
                        </h2>
                        <small></small>
                    </div>
                </div>
                {% endif %}
                {% for offer in offers %}
                {# отображение Projects #}
                <div class="col-md-6" style="padding-left: 0px !important;">
                    <div class="hpanel">
                        <div class="panel-body">

                            <div class="row">
                                <div class="col-sm-3 project-info">
                                    <div class="project-action m-t-md text-center">
                                        <a href="{{ url({'for':'frontend-v1-offer-page','offer_id':offer.offer_id}) }}">
                                            <img src="{% if offer.offer_picture %}{{ image_path(offer.offer_picture, 140, 140, '1x1', 'png', true) }}{% else %}/front/img/logo.png{% endif %}" class="ico-icon" />
                                        </a>

                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="badges">
                                                {% for i in 1..offer.offer_rating %}
                                                <i class="fa fa-star text-warning"></i>
                                                {% endfor %}
                                            </div>
                                        </div>
                                    </div>
                                    {% if offer.offer_id in hot_offers %}
                                    <span class="label label-danger pull-right">{{ t.gettext('hot') }}</span>
                                    {% elseif offer.offer_id in fresh_offers %}
                                    <span class="label label-success pull-right">{{ t.gettext('new') }}</span>
                                    {% endif %}
                                    <h4><a href="{{ url({'for':'frontend-v1-offer-page','offer_id':offer.offer_id}) }}"> {{offer.offer_name}} {% if offer.offer_code %}({{ offer.offer_code }}){% endif %}</a></h4>
                                    <div class="row chld-mt5">
                                        <div class="col-sm-5">
                                            <div class="project-label font-uppercase">{{ t.gettext('Estimated value') }}</div>
                                            ${{offer.offer_profit}}
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="project-label font-uppercase">{{ t.gettext('dedline') }}</div>
                                            <small>{{offer.offer_end_date}}</small>
                                        </div>
                                    </div>

                                    <div class="row mt10 chld-mt5">
                                        <div class="col-sm-4">
                                            <div class="project-label font-uppercase"><small>{{ t.gettext('Views') }}</small></div>
                                            {{offer.offer_views}}
                                        </div>
                                        <div class="col-sm-8">
                                            <a href="{{ url({'for':'frontend-v1-offer-page','offer_id':offer.offer_id}) }}" class="btn btn-success">
                                                {{ t.gettext('Get') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                {% endfor %}
            </div>

            {{ partial('right_col') }}

        </div>



{% endblock %}