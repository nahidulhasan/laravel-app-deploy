<?php
namespace App\Services;

use Exception;
use phpDocumentor\Reflection\Types\This;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class TokenService
 * @package App\Service
 */
class TokenService
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var
     */
    protected $clientSecret;

    /** @var int */
    protected $clientId;



    /**
     * Get Host from env file
     *
     * @return string
     */
    public static function getHost()
    {
        return env('CHT_HOST');
    }




    /**
     * Get Host from env file
     *
     * @return string
     */
    public static function getProxy()
    {
        $proxy = \Cake\Core\Configure::read('Org.Core.proxy');
        return $proxy;
    }


    /**
     * @param $msisdn
     * @return array
     * @throws Exception
     */
    public static function getAccessToken()
    {
        $data = [
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
            'username'=> env('USERNAME'),
            'password'=> env('PASSWORD')
        ];
        Log::info('Application access token create param  = '.json_encode($data));
        return static::post('/api/v1/token', $data);
    }

    /**
     * Make the header array with authentication.
     *
     * @param null $extra
     * @return array
     */
    private static function makeHeader($extra = null)
    {
        $header = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        if($extra != null) {
            $header = [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer'." ".$extra,
            ];
        }
        return $header;
    }


    /**
     * Make CURL request for GET request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @param null $extra
     * @return array
     * @throws Exception
     */
    public static function get($url, $body = [], $headers = null, $extra = null)
    {
        return static::makeMethod('get', $url, $body, $headers, $extra);
    }

    /**
     * Make CURL request for POST request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return array
     * @throws IdpException
     * @throws Exception
     */
    public static function post($url, $body = [], $headers = null)
    {
        return static::makeMethod('post', $url, $body, $headers);
    }

    /**
     * Make CURL request for PUT request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return array
     * @throws Exception
     */
    public static function put($url, $body = [], $headers = [])
    {
        return static::makeMethod('put', $url, $body, $headers);
    }

    /**
     * Make CURL request for DELETE request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return array
     * @throws \Exception
     */
    public static function delete($url, $body = [], $headers = [])
    {
        return static::makeMethod('delete', $url, $body, $headers);
    }

    /**
     * Make CURL request for a HTTP request.
     *
     * @param string $method
     * @param string $url
     * @param array $body
     * @param array $headers
     * @param $extra
     * @return array
     */
    private static function makeMethod($method, $url, $body = [], $headers = null, $extra = null)
    {
        $host = static::getHost();
        $baseUrl = $host.$url;
        $headers = $headers ?: static::makeHeader($extra);
        $response = Http::withHeaders($headers)->withOptions(["verify"=>false])->$method($baseUrl, $body);
        Log::info('Application access token api create response  = '.json_encode($response));

        if($response->ok()){
            Log::info('Application access token api create response  success = '.json_encode($response->getBody()->getContents()));
            return $response->json();
        }else{
            return $response->json();
        }

    }


    /**
     * Make CURL object for HTTP request verbs.
     *
     * @param $ch
     * @param string $url
     * @param array $body
     * @param array $headers
     * @param $extra
     * @return string
     */
    private static function makeRequest($ch, $url, $body, $headers, $extra)
    {

        $host = static::getHost();
        //if(empty($host)) $host = $extra['host'];

        //$proxy = static::getProxy();
        // if(empty($proxy)) $proxy = $extra['proxy'];

        $url = $host . $url;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        /*if (env('IS_STAGING')==='false'){
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }*/

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

    }
}
