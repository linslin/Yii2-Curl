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
 * @version   1.0.5
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
     * @var integer HTTP-Status Code
     * This value will hold HTTP-Status Code. False if request was not successful.
     */
    public $responseCode = null;


    /**
     * @var array HTTP-Status Code
     * Custom options holder
     */
    private $_options = array();


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
        if (in_array($key, $this->_defaultOptions) && $key !== CURLOPT_WRITEFUNCTION) {
            $this->_defaultOptions[$key] = $value;
        } else {
            $this->_options[$key] = $value;
        }

        //return self
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
        //reset all options
        if (isset($this->_options)) {
            $this->_options = array();
        }

        //reset response & status code
        $this->response = null;
        $this->responseCode = null;

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
        $curl = curl_init($url);
        curl_setopt_array($curl, $this->getOptions());
        $body = curl_exec($curl);

        //check if curl was successful
        if ($body === false) {
            switch (curl_errno($curl)) {

                case 7:
                    $this->responseCode = 'timeout';
                    return false;
                    break;

                default:
                    throw new Exception('curl request failed: ' . curl_error($curl) , curl_errno($curl));
                    break;
            }
        }

        //retrieve response code
        $this->responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->response = $body;

        //stop curl
        curl_close($curl);

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
}