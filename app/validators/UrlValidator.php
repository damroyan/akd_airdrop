<?php
use Phalcon\Validation\Validator,
    Phalcon\Validation\ValidatorInterface,
    Phalcon\Validation\Message;

class UrlValidator extends Validator implements ValidatorInterface {

    /**
     * Выполнение валидации
     *
     * @param Phalcon\Validation $validator
     * @param string $attribute
     * @return boolean
     */
    public function validate(\Phalcon\Validation $validator, $attribute) {
        $value = $validator->getValue($attribute);

        if($this->getOption('allowEmpty') && !$value) {
            return true;
        }

        if(!$value) {
            $validator->appendMessage(new Message(__('Url are required'), $attribute));
            return false;
        }

        $value = preg_replace_callback('@//([^/]+)@ui', function($a) {
            return "//" . idn_to_ascii($a[1]);
        }, $value);

        if(!filter_var($value, FILTER_VALIDATE_URL)) {
            $validator->appendMessage(new Message(__('Url are incorrect'), $attribute));
            return false;
        }

        return true;
    }

}