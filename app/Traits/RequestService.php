<?php

namespace App\Traits;


use App\Models\Config;
use App\Models\Token;
use App\Services\TokenService;
use Exception;
use GuzzleHttp\Client;
use http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait RequestService
{


    /**
     * @param $method
     * @param $requestUrl
     * @param array $formParams
     * @param array $headers
     * @return mixed
     * @throws Exception
     */
    public function request($method, $requestUrl, $formParams = [], $headers = [])
    {
        try {
            $headers = [
                "Accept" => "application/json",
                "Content-Type" => "application/json"
            ];

            if (!empty ($formParams)) {
                $formParams['form_params'] = $formParams;
            }

            $token = 'Bearer ' . Self::getToken();

            $response = Http::withOptions(["verify" => false])->withToken($token)->withHeaders($headers)->$method($requestUrl, $formParams);
            if ($response->getStatusCode() == 404) {
                throw new Exception($response->json()['message']);
            }
            Log::info('RCMS API -request- service = ' . json_encode($response->getStatusCode()));
            return $response->json();
            //            return json_decode($response,true);
        } catch (Exception $exception) {
            Log::info('rcms-request-service = ' . json_encode($exception->getMessage()));
            dd($exception->getMessage());
            return $exception->getMessage();
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getToken()
    {
        $this->checkValidToken();
        $token = Token::where('today', date('Y-m-d'))->first();
        if (!empty($token)) {
            return $token->secret;
        } else {
            $response = TokenService::getAccessToken();
            Log::info('Application access token (getAccessToken) response  = ' . json_encode($response));
            if (isset ($response['success'])) {
                if ($response['success']) {
                    Token::truncate();
                    Token::create([
                        'name' => 'CHT_TOKEN',
                        'key' => 'cht_token',
                        'secret' => $response['data']['access_token'],
                        'today' => date('Y-m-d')
                    ]);
                    $this->secret = $response['data']['access_token'];
                    return $response['data']['access_token'];
                } else {
                    return $response->json();
                }
            } else {
                Log::info('Token generation error');
            }
        }
    }

    public function checkValidToken()
    {
        $token = Token::where('today', date('Y-m-d'))->first();
        if (empty($token)) {
            return true;
        }
        $requestUrl = self::getHost() . '/api/v1/user/users/1';
        $token = 'Bearer ' . $token->secret;
        $headers = [
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        ];
        $response = Http::withOptions(["verify" => false])->withToken($token)->withHeaders($headers)->get($requestUrl);
        if ($response->getStatusCode() == 404) {
            Token::truncate();
        }

        return true;


    }

    public static function getHost()
    {
        return env('CHT_HOST');
    }

}
