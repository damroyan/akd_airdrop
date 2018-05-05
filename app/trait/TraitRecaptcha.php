<?php
trait TraitRecaptcha {
    protected function recaptchaVerification($paramResponse) {
        $client = new GuzzleHttp\Client();

        $response = $client->request(
            'POST',
            $this->config->recaptcha->url,
            [
                'form_params' => [
                    'secret'    => $this->config->recaptcha->secret_key,
                    'response'  => $paramResponse,
                    'remoteip'  => IP,
                ],
            ]
        );

        switch((int)$response->getStatusCode()) {
            case 200:
                break;

            default:
                return [
                    'response'      => \Response::RESPONSE_ERROR,
                    'error_code'    => $response->getStatusCode(),
                    'error_msg'     => __('Technical problem with Re-captcha: Re-captcha server are not respond'),
                ];
                break;
        }

        $array = json_decode($response->getBody(),true);
        if($array['success']) {
            return [
                'response' => \Response::RESPONSE_SUCCESS,
            ];
        }
        else {
            $error = [];
            if(is_array($array['error-codes'])) {
                foreach($array['error-codes'] as $value) {
                    switch($value) {
                        case 'missing-input-secret':
                            $error[] = __('Technical problem with Re-capthcha: Secret key is missing. Please contact with support.');
                            break;

                        case 'invalid-input-secret':
                            $error[] = __('Technical problem with Re-capthcha: Secret key is invalid. Please contact with support.');
                            break;

                        case 'missing-input-response':
                            $error[] = __('Technical problem with Re-capthcha: Input response is missing. Please contact with support.');
                            break;

                        case 'invalid-input-response':
                            $error[] = __('Technical problem with Re-capthcha: Input response is invalid. Please contact with support.');
                            break;

                        default:
                            $error[] = __('Technical problem with Re-capthcha: Undefined Error. Please contact with support.');
                            break;
                    }
                }
            }

            return [
                'response'      => \Response::RESPONSE_ERROR,
                'error_code'    => 400,
                'error_msg'     => $error[0],
            ];
        }
    }
}