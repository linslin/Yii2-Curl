<?php
/**
 * Yii2 cURL wrapper
 * With RESTful support. Inspired by wenbin1989/yii2-curl.
 *
 * @category  Web-yii2
 * @package   yii2-curl
 * @author    Nils Gajsek <info@linslin.org>
 * @copyright 2013-2014 Nils Gajsek<info@linslin.org>
 * @license   http://opensource.org/licenses/MIT MIT Public
 * @version   1.0.1
 * @link      http://www.linslin.com
 *
 */

namespace linslin\yii2\curl;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\Json;
use yii\web\HttpException;

/**
 * cURL class
 */
class Curl
{

    // ################################################ class vars // ################################################

    /**
     * @var float timeout for request
     * This value will be mapped to native curl `CURLOPT_CONNECTTIMEOUT` option.
     */
    public $connectionTimeout = null;


    /**
     * @var float timeout
     * This value will be mapped to curl `CURLOPT_TIMEOUT` option.
     */
    public $dataTimeout = null;


    /**
     * @var string
     * Holds response data right after sending a request.
     */
    public $response = null;


    /**
     * @var integer HTTP-Status Code
     * This value will hold HTTP-Status Code. False if request was not successful.
     */
    public $responseCode = null;


    /**
     * @var array HTTP-Status Code
     * This value will hold errors which came up while curl. Empty means no error
     */
    public $errors = array();


    /**
     * @var string User-Agent
     * This value will be mapped to curl `CURLOPT_USERAGENT`. Default is:
     */
    public $userAgent = 'Yii2-Curl-Agent';



    // ############################################### class methods // ##############################################

    /**
     * Init function
     *
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }


    /**
     * Start performing GET-HTTP-Request
     *
     * @param string  $url
     * @param string  $body
     * @param boolean $raw if response body contains JSON and should be decoded
     *
     * @return mixed response
     */
    public function get($url, $body = null, $raw = true)
    {
        return $this->httpRequest('GET', $url, $body, $raw);
    }


    /**
     * Start performing HEAD-HTTP-Request
     *
     * @param string $url
     * @param string $body
     *
     * @return mixed response
     */
    public function head($url, $body = null)
    {
        return $this->httpRequest('HEAD', $url, $body);
    }


    /**
     * Start performing POST-HTTP-Request
     *
     * @param string  $url
     * @param string  $body
     * @param boolean $raw if response body contains JSON and should be decoded
     *
     * @return mixed response
     */
    public function post($url, $body = null, $raw = true)
    {
        return $this->httpRequest('POST', $url, $body, $raw);
    }


    /**
     * Start performing PUT-HTTP-Request
     *
     * @param string  $url
     * @param string  $body
     * @param boolean $raw if response body contains JSON and should be decoded
     *
     * @return mixed response
     */
    public function put($url, $body = null, $raw = true)
    {
        return $this->httpRequest('PUT', $url, $body, $raw);
    }


    /**
     * Start performing DELETE-HTTP-Request
     *
     * @param string  $url
     * @param string  $body
     * @param boolean $raw if response body contains JSON and should be decoded
     *
     * @return mixed response
     */
    public function delete($url, $body = null, $raw = true)
    {
        return $this->httpRequest('DELETE', $url, $body, $raw);
    }


    /**
     * Performs HTTP request
     *
     * @param string  $method
     * @param string  $url
     * @param string  $requestBody
     * @param boolean $raw if response body contains JSON and should be decoded
     *
     * @throws Exception if request failed
     * @throws HttpException
     *
     * @return mixed
     */
    protected function httpRequest($method, $url, $requestBody = null, $raw = false)
    {
        //Init
        $profile = $method.' '.$url .'#'.md5(serialize($requestBody));
        $method = strtoupper($method);
        $options = array();
        $body = '';

        //setup default curl options
        $options = [
            CURLOPT_USERAGENT      => $this->userAgent,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Expect:'],
            CURLOPT_WRITEFUNCTION  => function ($curl, $data) use (&$body) {
                $body .= $data;
                return mb_strlen($data, '8bit');
            },
            CURLOPT_CUSTOMREQUEST  => $method,
        ];

        //setup connection timeout
        if ($this->connectionTimeout !== null) {
            $options[CURLOPT_CONNECTTIMEOUT] = $this->connectionTimeout;
        }

        //setup data parsing timeout
        if ($this->dataTimeout !== null) {
            $options[CURLOPT_TIMEOUT] = $this->dataTimeout;
        }

        //setup request body
        if ($requestBody !== null) {
            $options[CURLOPT_POSTFIELDS] = $requestBody;
        }

        //check if method is head and set no body
        if ($method === 'HEAD') {
            $options[CURLOPT_NOBODY] = true;
            unset($options[CURLOPT_WRITEFUNCTION]);
        }

        //setup error reporting and profiling
        Yii::trace("Start sending cURL-Request: $url\n" . Json::encode($requestBody), __METHOD__);
        Yii::beginProfile($profile, __METHOD__);

        //start curl
        $curl = curl_init($url);
        curl_setopt_array($curl, $options);

        if (curl_exec($curl) === false) {
            throw new Exception('curl request failed: ' . curl_error($curl) , curl_errno($curl));
        }

        //retrieve response code
        $this->responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        //stop curl
        curl_close($curl);
        Yii::endProfile($profile, __METHOD__);

        if ($this->responseCode >= 200 && $this->responseCode < 300) {
            if ($method === 'HEAD') {
                return true;
            } else {
                return $raw ? $body : Json::decode($body);
            }
        } elseif ($this->responseCode === 404) {
            return false;
        } else {
            throw new HttpException($this->responseCode, $body);
        }
    }
}
