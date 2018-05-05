<?php
use Phalcon\Validation\Validator,
    Phalcon\Validation\ValidatorInterface,
    Phalcon\Validation\Message;

class UserIdValidator extends Validator implements ValidatorInterface {

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
            $validator->appendMessage(new Message(__('User id are required'), $attribute));
            return false;
        }

        if(!preg_match('@^\d+$@', $value)) {
            $validator->appendMessage(new Message(__('User id are incorrect'), $attribute));
            return false;
        }

        $user = \Model\User::findFirst(array(
            "user_id = :user_id:",
            "columns" => array('user_id', ),
            'bind' => array(
                'user_id' => $value,
            )
        ));

        if(!$user) {
            $validator->appendMessage(new Message(__('User id are incorrect'), $attribute));
            return false;
        }

        return true;
    }

}