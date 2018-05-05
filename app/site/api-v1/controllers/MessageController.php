<?php
namespace Site\ApiV1\Controllers;

use Phalcon\Exception;

class MessageController extends Controller
{

    public function initialize()
    {
        parent::initialize();
    }

    /**
     * Закрытие сообщения пользователем.
     *
     * @Role({"allow": ['user','admin']})
     */
    public function closeAction() {
        $message_id = $this->request->getQuery('message_id','int');

        if ($message_id) {
            $user_message = \Model\UserMessage::findFirst([
                'conditions'    => 'user_id = :user_id: AND message_id = :message_id:',
                'bind'          => [
                    'user_id'       => $this->user->user_id,
                    'message_id'    => $message_id,
                ]
            ]);

            if (!$user_message) {
                $user_message = new \Model\UserMessage();

                $user_message->assign([
                    'message_id'    => $message_id,
                    'user_id'       => $this->user->user_id,
                ]);
            } else {
                $user_message->assign([
                    'user_message_date' => date('Y-m-d H:i:s'),
                ]);
            }

            $user_message->save();

            return \Response::Ok();

        } else {
            return \Response::Error(404);
        }
    }
}