yii2-curl extension
===================
[![Latest Stable Version](https://poser.pugx.org/linslin/yii2-curl/v/stable)](https://packagist.org/packages/linslin/yii2-curl)
[![Total Downloads](https://poser.pugx.org/linslin/yii2-curl/downloads)](https://packagist.org/packages/linslin/yii2-curl)
[![License](https://poser.pugx.org/linslin/yii2-curl/license)](https://packagist.org/packages/linslin/yii2-curl)
                   
Easy working cURL extension for Yii2, including RESTful support:

 - POST
 - GET
 - HEAD
 - PUT
 - PATCH
 - DELETE

Requirements
------------
- Yii2
- PHP 5.4+
- Curl and php-curl installed


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

```bash
php composer.phar require --prefer-dist linslin/yii2-curl "*"
```


Usage
-----

Once the extension is installed, simply use it in your code. The following example shows you how to handling a simple GET Request. 

```php
<?php
/**
 * Yii2 test controller
 *
 * @category  Web-yii2-example
 * @package   yii2-curl-example
 * @author    Nils Gajsek <info@linslin.org>
 * @copyright 2013-2017 Nils Gajsek <info@linslin.org>
 * @license   http://opensource.org/licenses/MIT MIT Public
 * @version   1.1.0
 * @link      http://www.linslin.org
 *
 */

namespace app\controllers;

use yii\web\Controller;
use linslin\yii2\curl;

class TestController extends Controller
{

    /**
     * Yii action controller
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }


    /**
     * cURL GET example and accessing response headers
     */
    public function actionGetExample()
    {
        //Init curl
        $curl = new curl\Curl();

        //get http://example.com/
        $response = $curl->get('http://example.com/');
        $responseHeaders = $curl->responseHeaders;
    }


    /**
     * cURL POST example with post body params.
     */
    public function actionPostExample()
    {
        //Init curl
        $curl = new curl\Curl();

        //post http://example.com/
        $response = $curl->setOption(
                CURLOPT_POSTFIELDS, 
                http_build_query(array(
                    'myPostField' => 'value'
                )
            ))
            ->post('http://example.com/');
    }


    /**
     * cURL multiple POST example one after one
     */
    public function actionMultipleRequest()
    {
        //Init curl
        $curl = new curl\Curl();


        //post http://example.com/
        $response = $curl->setOption(
            CURLOPT_POSTFIELDS, 
            http_build_query(array(
                'myPostField' => 'value'
                )
            ))
            ->post('http://example.com/');


        //post http://example.com/, reset request before
        $response = $curl->reset()
            ->setOption(
                CURLOPT_POSTFIELDS, 
                http_build_query(array(
                    'myPostField' => 'value'
                )
            ))
            ->post('http://example.com/');
    }


    /**
     * cURL advanced GET example with HTTP status codes
     */
    public function actionGetAdvancedExample()
    {
        //Init curl
        $curl = new curl\Curl();

        //get http://example.com/
        $response = $curl->post('http://example.com/');

        // List of status codes here http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
        switch ($curl->responseCode) {

            case 'timeout':
                //timeout error logic here
                break;
                
            case 200:
                //success logic here
                break;

            case 404:
                //404 Error logic here
                break;
        }
    }
    
    
    /**
     * cURL timeout chaining/handling
     */
    public function actionHandleTimeoutExample()
    {
        //Init curl
        $curl = new curl\Curl();

        //get http://www.google.com:81/ -> timeout
        $response = $curl->post('http://www.google.com:81/');

        // List of status codes here http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
        switch ($curl->responseCode) {

            case 'timeout':
                //timeout error logic here
                break;
                
            case 200:
                //success logic here
                break;

            case 404:
                //404 Error logic here
                break;
        }
    }
    
    
    /**
     * cURL error handling
     */
    public function actionHandleHostUknownExample()
    {
        //Init curl
        $curl = new curl\Curl();

        //get http://www.google.com:81/ -> timeout
        $response = $curl->post('http://www.xyz-no-one-set.nope');

        // List of curl error codes here https://curl.haxx.se/libcurl/c/libcurl-errors.html
        switch ($curl->errorCode) {

            case 6:
                //host unknown example
                break;
        }
    }
}
```

 
Changelog
------------
##### Release 1.1.0 - Changelog
- Fixed header explode - https://github.com/linslin/Yii2-Curl/pull/51.
- Allow PHP class goodness - https://github.com/linslin/Yii2-Curl/issues/52.
- Added API attribute `errorText [string|null]` - holds a string describing the given error code - https://github.com/linslin/Yii2-Curl/issues/49.

##### Release 1.0.11 - Changelog
- Added API attribute `responseHeaders [array|null]` which returns an array of all response headers. 
- Changed `_defaultOptions[CURLOPT_HEADER]` to `true`.
- Profile debugging is only active if constant `YII_DEBUG` is `true`.

##### Release 1.0.10 - Changelog
- Fixed PHP notice https://github.com/linslin/Yii2-Curl/issues/39.

##### Release 1.0.9 - Changelog
- Added API attribute `responseCode [string|null]` which holds the HTTP response code.
- Added API attribute `responseCharset [string|null]` which holds the response charset.
- Added API attribute `responseLength [integer|null]` which holds the response length.
- Added API attribute `errorCode` which holds the a integer code error like described here: https://curl.haxx.se/libcurl/c/libcurl-errors.html.
- Fixed Issue https://github.com/linslin/Yii2-Curl/issues//36.
- Fixed Issue https://github.com/linslin/Yii2-Curl/issues//37 and removed exception throwing on curl fail. This allow the user to handle the error while using attribute `errorCode`.

##### Release 1.0.8 - Changelog
- Added API method `setOptions([array])` which allows to setup multiple options at once. 
- Fixed Issue https://github.com/linslin/Yii2-Curl/issues/30.

##### Release 1.0.7 - Changelog
- Fixed `getInfo([, int $opt = 0 ])` exception were cURL wasn't initialized before calling `getInfo($opt)`.

##### Release 1.0.6 - Changelog
- Added `getInfo([, int $opt = 0 ])` method to retrieve http://php.net/manual/de/function.curl-getinfo.php data.

##### Release 1.0.5 - Changelog
- Made `body` callback not depending on HTTP-Status codes anymore. You can retrieve `body` data on any HTTP-Status now. 
- Fixed Issue https://github.com/linslin/Yii2-Curl/issues/19 where override default settings break options.
- Added timeout response handling. `$curl->responseCode = 'timeout'`

##### Release 1.0.4 - Changelog
- `CURLOPT_RETURNTRANSFER` is now set to true on default - https://github.com/linslin/Yii2-Curl/issues/18 
- Readme.md adjustments.

##### Release 1.0.3 - Changelog
- Fixed override of user options. https://github.com/linslin/Yii2-Curl/pull/7 
- Nice formatted PHP-examples. 
- Moved `parent::init();` behavior into unitTest Controller.

##### Release 1.0.2 - Changelog
- Added custom params support
- Added custom status code support
- Added POST-Param support and a readme example
- Removed "body" support at request functions. Please use "CURLOPT_POSTFIELDS" to setup a body now.
- Readme modifications

##### Release 1.0.1 - Changelog
- Removed widget support
- Edited some spellings + added more examples into readme.md

##### Release 1.0 - Changelog
- Official stable release