<?php
/**
 * Yii2 cURL wrapper
 * With RESTful support.
 *
 * @category  Web-yii2
 * @package   yii2-curl
 * @author    Nils Gajsek <info@linslin.org>
 * @copyright 2013-2015 Nils Gajsek<info@linslin.org>
 * @license   http://opensource.org/licenses/MIT MIT Public
 * @version   1.0.10
 * @link      http://www.linslin.org
 *
 */

namespace linslin\yii2\curl;

use Yii;
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
     * @var string
     * Holds response data right after sending a request.
     */
    public $response = null;
    /**
     * @var null|integer
     * Error code holder: https://curl.haxx.se/libcurl/c/libcurl-errors.html
     */
    public $errorCode = null;

    /**
     * @var integer HTTP-Status Code
     * This value will hold HTTP-Status Code. False if request was not successful.
     */
    public $responseCode = null;

    /**
     * @var string|null HTTP Response Charset
     * (taken from Content-type header)
     */
    public $responseCharset = null;

    /**
     * @var int HTTP Response Length
     * (taken from Content-length header, or strlen() of downloaded content)
     */
    public $responseLength = -1;

    /**
     * @var string|null HTTP Response Content Type
     * (taken from Content-type header)
     */
    public $responseType = null;

    /**
     * @var array HTTP-Status Code
     * Custom options holder
     */
    private $_options = array();

    /**
     * @var resource|null
     * Holds cURL-Handler
     */
    private $_curl = null;

    /**
     * @var array default curl options
     * Default curl options
     */
    private $_defaultOptions = array(
        CURLOPT_USERAGENT      => 'Yii2-Curl-Agent',
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
    );


    // ############################################### class methods // ##############################################

    /**
     * Start performing GET-HTTP-Request
     *
     * @param string  $url
     * @param boolean $raw if response body contains JSON and should be decoded
     *
     * @return mixed response
     */
    public function get($url, $raw = true)
    {
        return $this->_httpRequest('GET', $url, $raw);
    }


    /**
     * Start performing HEAD-HTTP-Request
     *
     * @param string $url
     *
     * @return mixed response
     */
    public function head($url)
    {
        return $this->_httpRequest('HEAD', $url);
    }


    /**
     * Start performing POST-HTTP-Request
     *
     * @param string  $url
     * @param boolean $raw if response body contains JSON and should be decoded
     *
     * @return mixed response
     */
    public function post($url, $raw = true)
    {
        return $this->_httpRequest('POST', $url, $raw);
    }


    /**
     * Start performing PUT-HTTP-Request
     *
     * @param string  $url
     * @param boolean $raw if response body contains JSON and should be decoded
     *
     * @return mixed response
     */
    public function put($url, $raw = true)
    {
        return $this->_httpRequest('PUT', $url, $raw);
    }


    /**
     * Start performing PATCH-HTTP-Request
     *
     * @param string  $url
     * @param boolean $raw if response body contains JSON and should be decoded
     *
     * @return mixed response
     */
    public function patch($url, $raw = true)
    {
        return $this->_httpRequest('PATCH', $url, $raw);
    }


    /**
     * Start performing DELETE-HTTP-Request
     *
     * @param string  $url
     * @param boolean $raw if response body contains JSON and should be decoded
     *
     * @return mixed response
     */
    public function delete($url, $raw = true)
    {
        return $this->_httpRequest('DELETE', $url, $raw);
    }


    /**
     * Set curl option
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption($key, $value)
    {
        //set value
        if (array_key_exists($key, $this->_defaultOptions) && $key !== CURLOPT_WRITEFUNCTION) {
            $this->_defaultOptions[$key] = $value;
        } else {
            $this->_options[$key] = $value;
        }

        //return self
        return $this;
    }


    /**
     * Set curl options
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->_options = $options + $this->_options;

        return $this;
    }


    /**
     * Unset a single curl option
     *
     * @param string $key
     *
     * @return $this
     */
    public function unsetOption($key)
    {
        //reset a single option if its set already
        if (isset($this->_options[$key])) {
            unset($this->_options[$key]);
        }

        return $this;
    }


    /**
     * Unset all curl option, excluding default options.
     *
     * @return $this
     */
    public function unsetOptions()
    {
        //reset all options
        if (isset($this->_options)) {
            $this->_options = array();
        }

        return $this;
    }


    /**
     * Total reset of options, responses, etc.
     *
     * @return $this
     */
    public function reset()
    {
        if ($this->_curl !== null) {
            curl_close($this->_curl); //stop curl
        }

        //reset all options
        if (isset($this->_options)) {
            $this->_options = array();
        }

        //reset response & status params
        $this->_curl = null;
        $this->errorCode = null;
        $this->response = null;
        $this->responseCode = null;
        $this->responseCharset = null;
        $this->responseLength = -1;
        $this->responseType = null;

        return $this;
    }


    /**
     * Return a single option
     *
     * @param string|integer $key
     * @return mixed|boolean
     */
    public function getOption($key)
    {
        //get merged options depends on default and user options
        $mergesOptions = $this->getOptions();

        //return value or false if key is not set.
        return isset($mergesOptions[$key]) ? $mergesOptions[$key] : false;
    }


    /**
     * Return merged curl options and keep keys!
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options + $this->_defaultOptions;
    }


    /**
     * Get curl info according to http://php.net/manual/de/function.curl-getinfo.php
     *
     * @param null $opt
     * @return array|mixed
     */
    public function getInfo($opt = null)
    {
        if ($this->_curl !== null && $opt === null) {
            return curl_getinfo($this->_curl);
        } elseif ($this->_curl !== null && $opt !== null) {
            return curl_getinfo($this->_curl, $opt);
        } else {
            return [];
        }
    }


    /**
     * Performs HTTP request
     *
     * @param string  $method
     * @param string  $url
     * @param boolean $raw if response body contains JSON and should be decoded -> helper.
     *
     * @throws Exception if request failed
     *
     * @return mixed
     */
    private function _httpRequest($method, $url, $raw = false)
    {
        //set request type and writer function
        $this->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($method));

        //check if method is head and set no body
        if ($method === 'HEAD') {
            $this->setOption(CURLOPT_NOBODY, true);
            $this->unsetOption(CURLOPT_WRITEFUNCTION);
        }

        //setup error reporting and profiling
        Yii::trace('Start sending cURL-Request: '.$url.'\n', __METHOD__);
        Yii::beginProfile($method.' '.$url.'#'.md5(serialize($this->getOption(CURLOPT_POSTFIELDS))), __METHOD__);

        /**
         * proceed curl
         */
        $this->_curl = curl_init($url);
        curl_setopt_array($this->_curl, $this->getOptions());
        $this->response = curl_exec($this->_curl);

        //check if curl was successful
        if ($this->response === false) {

            //set error code
            $this->errorCode = curl_errno($this->_curl);

            switch ($this->errorCode) {
                // 7, 28 = timeout
                case 7:
                case 28:
                    $this->responseCode = 'timeout';
                    return false;
                    break;

                default:
                    return false;
                    break;
            }
        }

        // Extract additional curl params
        $this->_extractAdditionalCurlParameter();

        //end yii debug profile
        Yii::endProfile($method.' '.$url .'#'.md5(serialize($this->getOption(CURLOPT_POSTFIELDS))), __METHOD__);

        //check responseCode and return data/status
        if ($this->getOption(CURLOPT_CUSTOMREQUEST) === 'HEAD') {
            return true;
        } else {
            $this->response = $raw ? $this->response : Json::decode($this->response);
            return $this->response;
        }
    }


    /**
     * Extract additional curl params private class helper
     */
    private function _extractAdditionalCurlParameter () {

        /**
         * retrieve response code
         */
        $this->responseCode = curl_getinfo($this->_curl, CURLINFO_HTTP_CODE);


        /**
         * try extract response type & charset.
         */
        $this->responseType = curl_getinfo($this->_curl, CURLINFO_CONTENT_TYPE);

        if (!is_null($this->responseType) && count(explode(';', $this->responseType)) > 1) {

            list($this->responseType, $possibleCharset) = explode(';', $this->responseType);

            //extract charset
            if (preg_match('~^charset=(.+?)$~', trim($possibleCharset), $matches) && isset($matches[1])) {
                $this->responseCharset = strtolower($matches[1]);
            }
        }


        /**
         * try extract response length
         */
        $this->responseLength = curl_getinfo($this->_curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        if((int)$this->responseLength == -1)             {
            $this->responseLength = strlen($this->response);
        }
    }
}