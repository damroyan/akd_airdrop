<?php
class MainTask extends \Phalcon\CLI\Task {
    public function mainAction() {
        Console::WriteLine('<p>Дефолтная проверка работоспособности cli - Базовые настройки ок<p>', Console::COLOR_GREEN);
    }

    /**
     * Останавливаем офферы у которых закончилась дата
     *
     * @return \Phalcon\Http\Response
     * @throws Exception
     */
    public function offerStopAction() {

        $expired_offers = \Model\Offer::find([
            'conditions' => "offer_status = :offer_status: 
                                    AND offer_end_date < :offer_end_date:",
            'bind' => [
                'offer_status' => \Model\Offer::STATUS_ACTIVE,
                'offer_end_date' => date('Y-m-d'),
            ],
        ]);

        $count = count($expired_offers);

        echo "Найдено $count прошедших офферов";

        foreach ($expired_offers as $expired_offer) {
            $expired_offer->assign([
                'offer_status' => \Model\Offer::STATUS_INACTIVE,
            ]);
            if(!$expired_offer->save()) {
                print_r("Ошибка db");
            };
        }
    }

    /**
     * Временная функция
     *
     * @return \Phalcon\Http\Response
     * @throws Exception
     */
    public function tempAction() {

        $offers = \Model\Offer::find();

        foreach ($offers as $offer) {
            if (!is_array(json_decode($offer->offer_url))) {
                $offer->assign([
                    'offer_url' => json_encode([$offer->offer_url]),
                ]);

                if(!$offer->save()) {
                    print_r("Ошибка db");
                };
            }
        }
    }

}