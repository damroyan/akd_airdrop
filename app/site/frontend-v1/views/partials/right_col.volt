
<div class="col-md-3">
    {% if featured_offers|length > 0 %}

    <div class="hpanel">
            <div class="panel-body">
                <h5>{{ t.gettext('Featured offers') }}</h5>
                {% for offer in featured_offers %}
                <hr />
                <div class="row">
                    <div class="col-sm-12">
                        <a href="{{ url({'for':'frontend-v1-offer-page','offer_id':offer['offer_id']}) }}"><strong>{{offer['offer_name']}}</strong></a> <br />
                        <small>{{ t.gettext('Ends in') }} {{offer['offer_days_left']}} дня(ей), {{offer['offer_views']}} {{ t.gettext('views') }}</small>
                    </div>
                </div>
                {% endfor %}
            </div>
        </div>
    {% endif %}

        <div class="hpanel">
            <div class="panel-body">
                <a href="https://t.me/freecryptopay" class="text-uppercase" target="_blank"><i class="fa fa-telegram text-info" style="font-size: 22px;"></i> &nbsp; Получать Обновления </a>
            </div>
        </div>


        <a href="">
            <img src="/front/img/banners/banner.png" width="100%" />
        </a>
</div>
