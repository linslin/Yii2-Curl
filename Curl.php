<?php
/**
 * Yii2 cURL wrapper
 *
 * @category  Web-yii2
 * @package   yii2-curl
 * @author    Nils Gajsek <info@linslin.org>
 * @copyright 2013-2014 Nils Gajsek<info@linslin.org>
 * @license   http://opensource.org/licenses/MIT MIT Public
 * @version   1.0
 * @link      http://www.linslin.com
 *
 */

namespace linslin\yii2\curl;

/**
 * cURL class
 */
class Curl extends \yii\base\Widget
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
     * This value will be hold HTTP-Status Code. False if request was not successfull.
     */
    public $responseCode = null;



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
}
