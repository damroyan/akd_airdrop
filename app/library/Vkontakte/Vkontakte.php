<?php
namespace Vkontakte;

use Vkontakte\Exceptions\VkontakteSDKException;
use Vkontakte\Exceptions\VkontakteResponseException;

class Vkontakte {
    const API_VERSION   = '5.50';

    const URL_AUTHORIZE = 'https://oauth.vk.com/authorize';
    const URL_TOKEN = 'https://oauth.vk.com/access_token';
    const URL_API   = 'https://api.vk.com/method/';

    private $_appId = null;
    private $_appSecret = null;
    private $_redirectUri = null;

    private $_accessTokenMeta = null;

    public function __construct($params = []) {
        if($params['app_id']) {
            $this->setAppId($params['app_id']);
        }

        if($params['app_secret']) {
            $this->setAppSecret($params['app_secret']);
        }
    }

    public function setAppId($appId) {
        $this->_appId = $appId;
    }

    public function getAppId() {
        return $this->_appId;
    }

    public function setAppSecret($appSecret) {
        $this->_appSecret = $appSecret;
    }

    public function getAppSecret() {
        return $this->_appSecret;
    }

    public function setRedirectUri($redirectUri) {
        $this->_redirectUri = $redirectUri;
    }

    public function getRedirectUri() {
        return $this->_redirectUri;
    }

    public function getLoginUrl($callbackUrl, $scope = []) {
        $query = http_build_query([
            'client_id'     => $this->getAppId(),
            'redirect_uri'  => $callbackUrl,
            'display'       => 'page',
            'scope'         => join(',', $scope),
            'response_type' => 'code',
            'v'             => self::API_VERSION,
            'state'         => null,
        ]);

        return self::URL_AUTHORIZE . "?{$query}";
    }

    public function getCode() {
        return $_REQUEST['code'];
    }

    public function get($method, $params = [], $accessToken = null) {
        $query = http_build_query(
            array_merge(
                [
                    'access_token' => $accessToken ? $accessToken : $this->getUserAccessToken()
                ],
                $params
            )
        );
        
        return $this->_httpRequest(
            'GET',
            self::URL_API . $method . ($query ? "?{$query}" : '')
        );
    }

    private function _httpRequest($method = 'GET', $url, $query = []) {
        $client = new \GuzzleHttp\Client();

        switch($method) {
            case 'POST':
            case 'GET':
                $options = [
                    'query' => $query,
                ];
                break;

            default:
                throw new VkontakteSDKException("HTTP request method error");
                break;
        }

        try {
            $response = $client->send($client->createRequest($method, $url, $options));
        }
        catch(\GuzzleHttp\Exception\ClientException $e) {
            $code = $e->getCode();
            $result = $e->getResponse()->json();

            if($result['error']) {
                throw new VkontakteResponseException("[{$code}] HTTP response error: {$result['error']}. {$result['error_description']}");
            }
            else {
                throw new VkontakteSDKException("[{$code}] HTTP response error.");
            }
        }
        catch (\Exception $e) {
            $code = $e->getCode();

            throw new VkontakteSDKException("[{{$code}}] HTTP response error");
        }

        return $response->json();
    }

    public function getAccessToken() {
        if(!$this->getCode()) {
            throw new VkontakteSDKException("Invalid code");
        }

        if($_REQUEST['error']) {
            throw new VkontakteSDKException("{$_REQUEST['error']}: {$_REQUEST['error_description']}");
        }

        $result = $this->_httpRequest(
            'GET',
            self::URL_TOKEN, [
                'client_id'     => $this->getAppId(),
                'client_secret' => $this->getAppSecret(),
                'redirect_uri'  => $this->getRedirectUri(),
                'code'          => $this->getCode(),
            ]
        );

        if($result['access_token']) {
            $this->_accessTokenMeta = $result;
        }
        else {
            $this->_accessTokenMeta = null;
        }

        return $this->getUserAccessToken();
    }

    public function getUserId() {
        if($this->_accessTokenMeta['user_id']) {
            return $this->_accessTokenMeta['user_id'];
        }

        return null;
    }

    public function getEmail() {
        if($this->_accessTokenMeta['email']) {
            return $this->_accessTokenMeta['email'];
        }

        return null;
    }

    public function getUserAccessToken() {
        if($this->_accessTokenMeta['access_token']) {
            return $this->_accessTokenMeta['access_token'];
        }

        return null;
    }

    public function getUserAccessTokenExpires() {
        if($this->_accessTokenMeta['expires_in']) {
            return time() + $this->_accessTokenMeta['expires_in'];
        }

        return null;
    }
}
