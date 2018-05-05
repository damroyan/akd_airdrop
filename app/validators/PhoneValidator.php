<?php
use Phalcon\Validation\Validator,
    Phalcon\Validation\ValidatorInterface,
    Phalcon\Validation\Message;

class PhoneValidator extends Validator implements ValidatorInterface {

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
                $validator->appendMessage(new Message(__('Phone is required'), $attribute));
                return false;
            }

            preg_match_all('@\d+@', $value, $matches);
            $value = (string)implode("", $matches[0]);

            if(!preg_match('@^\d{11}$@ui', $value)) {
                $validator->appendMessage(new Message(__('Phone are incorrect'), $attribute));
                return false;
            }

            return true;
        }
}