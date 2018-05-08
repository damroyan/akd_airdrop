{% extends "layouts/layout.volt" %}

{% block content %}
    <div class="row">
        <div class="col-md-9">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <img src="/front/img/logos/opet-foundation.jpg" class="ico-icon">
                        </div>
                        <div class="col-sm-10">
                            <h1>BCharity (CHAR)</h1>
                            <div class="badges">
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                | <i class="fa fa-money"></i> <span class="font-uppercase">{{ t.gettext('Estimated value') }}:</span>
                                $0.5 |
                                <i class="fa fa-street-view"></i> <span class="font-uppercase">{{ t.gettext('Views') }}:</span> 432
                            </div>
                        </div>
                    </div>
                    <div class="row mt10">
                        <div class="col-sm-12">
                            <p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;">
                                BCharity has been developing the first and unique international charity exchange based on the blockchain technology with implementation of a cryptocurrencies.
                                <br style="box-sizing: inherit;">
                            </p>
                            <p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;">
                                💸&nbsp; From $10 to $25 for ALL participants! (which will be exchanged to CHAR Tokens at the end of sales)
                            </p>
                            <p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;">
                                <span style="box-sizing: inherit;">FOLLOW THESE STEPS</span></p><p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;">1. <a href="https://bcharity.io/register/1euQT3yz9ojzi18F2d" target="_blank">Register here</a>&nbsp;then <a href="https://bcharity.io/login" target="_blank">login</a></p><p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;">2. Fill out the form after login</p><p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;">3. Follow carefully the steps : </p><p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;">-Join the <a href="https://t.me/bcharity" target="_blank">Telegram channel</a>, </p><p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;">-Join <a href="https://t.me/bcharity_chat" target="_blank">Telegram Chat</a>, </p><p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;">-Follow on <a href="Twitterhttps://twitter.com/bcharity_io" target="_blank">Twitter</a>, </p><p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;">-Rate them and subscribe to <a href="https://www.facebook.com/bcharity.io" target="_blank">Facebook page</a></p><p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;"><br></p><p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;">📌WANT MORE?📌</p><p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;"><span style="color: rgb(206, 0, 0);"><span style="text-align: center;">Change your avatar in chosen social networks and Telegram with BCharity logo, and also add to your name "ICO BCharity.io", will get 1000 bonus Stakes, after the Main Sale finished.</span><span style="text-align: center;">&nbsp;</span></span></p><p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;">Read Airdrop <a href="https://bcharity.io/promoair" target="_blank" style="text-align: center; background-color: rgb(255, 255, 255);">details here</a><br></p><p style="box-sizing: inherit; margin-bottom: 1rem; line-height: 1.5;"><br></p>
                        </div>
                    </div>

                    <div class="row mt10">
                        <div class="col-sm-12">
                            <a href="" class="btn btn-success text-capitalize" style="font-size: 28px;"><i class="fa fa-check-circle"></i> {{ t.gettext('Activate offer') }}</a>
                        </div>
                    </div>
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