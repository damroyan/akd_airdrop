define(function() {
    console.log('[app/lang]', 'Init');

    return function(str) {
        var _t = {
            'method_post': 'Необходимо передавать данные методом POST',
            'api_method_not_found': 'Метод API не найден',
            'api_method_forbidden': 'Доступ к методу API ограничен',
            'api_method_bad_request': 'Сервер обнаружил в запросе клиента синтаксическую ошибку',

            'error_system': 'Системная ошибка',

            'code_required': 'Требуется заполнить поле url кода',
            'code_incorrect': 'Поле url кода не корректно',
            'code_used': 'Данное значение url кода уже занято',

            'date_required': 'Требуется указать дату',
            'date_incorrect': 'Формат даты указан не верно (yyyy-mm-dd)',

            'time_required': 'Требуется указать время',
            'time_incorrect': 'Формат для времени указан не верно (hh:ii)',

            'url_required': 'URL обязателен',
            'url_incorrect': 'URL не корректный',

            'busy_wait': 'Подождите&hellip;',

            'popup_header_error': 'Ошибка',
            'popup_header_alert': 'Внимание',
            'action_complete': 'Действие выполнено',

            'action_token_not_found': 'Ошибка при выполнении действия. Действие не найдено.',
            'action_token_used': 'Действие было выполнено ранее',
            'action_token_expired': 'Срок для выполнения действия истек',

            'recaptcha_server_not_response': 'reCAPTCHA: Сервер не доступен',
            'recaptcha_missing_input_secret': 'reCAPTCHA: The secret parameter is missing',
            'recaptcha_invalid_input_secret': 'reCAPTCHA: The secret parameter is invalid or malformed',
            'recaptcha_missing_input_response': 'reCAPTCHA: Необходимо разгадать',
            'recaptcha_invalid_input_response': 'reCAPTCHA: Ответ не верен',
            'recaptcha_unknow_error': 'reCAPTCHA: Сервер выдал неизвестную ошибку',

            'db_error_insert': 'Системная ошибка записи данных в базу данных',
            'db_error_update': 'Системная ошибка обновления данных в базе данных',
            'db_error_delete': 'Системная ошибка удаления данных из базы данных',
            'transaction_error': 'Произошла системная ошибка при сохранении данных. Если ошибка повторяется, пожалуйста сообщите нам о ней!',

            'password_not_match': 'Пароли не совпадают',

            'string_required': 'Необходимо заполнить поле',
            'string_incorrect': 'Значение не корректно',
            'string_delete': 'Данное значение удалено',
            'string_inactive': 'Данное значение не активно',
            'string_dublicate': 'Данное значение уже занято',
            'string_length_min': 'Данное значение слишком короткое',
            'string_length_max': 'Данное значение слишком длинное',

            'email_incorrect': 'Введен некорректный e-mail',

            'user_registration_send': 'На указанный вами адрес электронной почты выслана ссылка для активации учетной записи.',
            'user_id_required': 'Требуется указать Id пользователя',
            'user_id_incorrect': 'Id пользователя некорректно',
            'user_auth_required': 'Для выполнения данного действия, необходимо авторизоваться на сайте',
            'user_permission_denied': 'У данного пользователя не хватает прав, для выполнения данного действия',
            'user_blocked': 'Пользователь заблокирован модератором',
            'user_delete': 'Пользователь удален модератором',
            'user_login_used': 'Пользователь с таким логин уже существует',
            'user_email_incorrect': 'Введен некорректный e-mail пользователя',
            'user_password_incorrect': 'Пароль пользователя некорректен',
            'user_auth_success': 'Пользователь авторизован',
            'user_logout_success': 'Пользователь вышел',
            'user_inactive': 'Данный пользователь еще не подтвердил свою регистрацию',

            'error_image_file_open': 'Ошибка открытия временного файла с изображением',
            'error_image_file_size': 'Ошибка в размере файлы изображения',
            'error_image_file_type': 'Ошибка в типе файла изображения',
            'error_image_file_aspect': 'Ошибка в соотношение сторон изображения',
            'error_image_file_width_height': 'Ошибка в ширине или высоте изображения',

            'error_upload': 'Неизвестная ошибка при загрузке файла',
            'error_upload_blank': 'Файл для загрузки не передан',
            'error_upload_isfile': 'Загруженный файл не найден на сервере',
            'error_upload_move': 'Ошибка перемещения загруженного файла',
            'error_upload_multiply': 'Передано слишком много файлов для загрузки',

            'system_error': 'Произошла системная ошибка. Если ошибка повторяется, пожалуйста сообщите нам о ней!',

            '': ''
        };

        if (_t.hasOwnProperty(str)) {
            return _t[str];
        }
        else {
            return str;
        }
    }
});