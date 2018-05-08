<div class="row">
    <div class="col-md-11">


    <div class="hpanel">
        <div class="panel-body text-center">
            <h2>{{ t.gettext("Disclamer") }}</h2>
            {{ config.system.projectName }}
            {{ t.gettext("disclamer text") }}
            <br><br>
            <p class="text-danger">
                {{ t.gettext("Please never share your private key with anyone as they will be able to steal money from you!") }}
            </p>
        </div>
    </div>
    </div>
</div>

<footer class="footer">
        <span class="pull-right">
            <small>{{ t.gettext('cookie_text') }}</small>
        </span>
    {{ config.system.projectName }}, 2018 (с)
</footer>