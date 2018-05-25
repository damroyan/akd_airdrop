define([
    'jquery',
    'app/core',
    'ckeditor'
], function($, core, ckeditor) {
    console.log('[app/offer-edit]', 'ckeditor');

    // Добавление еще одного поля со ссылкой на оффер
    $('.js_add_button').on('click', function(){

        var field_number = $("#add_field_area .form-group div").length + 1;

        $("div#add_field_area").append("<div class=\"form-group\">\n" +
            "                    <label for=\"label-for-user_lastname\" class=\"col-sm-2 control-label\">\n" +
            "                        Ссылка на оффер " + field_number + "\n" +
            "                    </label>\n" +
            "                    <div class=\"col-sm-10\">\n" +
            "                        <input type=\"text\" name=\"offer_url[]\" class=\"form-control\" placeholder=\"ОБЕРНУТАЯ В byt.ly!\">\n" +
            "                    </div>\n" +
            "                </div>");

    });

    var e = CKEDITOR.replace( 'offer_description' );
   // console.log(e.on);

});