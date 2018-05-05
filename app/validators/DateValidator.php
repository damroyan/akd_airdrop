<?php
use Phalcon\Validation\Validator,
    Phalcon\Validation\ValidatorInterface,
    Phalcon\Validation\Message;

class DateValidator extends Validator implements ValidatorInterface {

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
                $validator->appendMessage(new Message(__('Date is required'), $attribute));
                return false;
            }

            if(!preg_match('@^\d{4}\-\d{2}\-\d{2}$@ui', $value)) {
                $validator->appendMessage(new Message(__('Date have not correct format'), $attribute));
                return false;
            }

            return true;
        }
}