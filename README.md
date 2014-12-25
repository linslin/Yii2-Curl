yii2-curl extension
===================
Cool working curl extension for Yii2, including RESTful support:

 - POST
 - GET
 - HEAD
 - PUT
 - DELETE
 
Changelog
------------

##### Release 1.0.2 - Changelog

- Added custom param support
- Readme modifcations

##### Release 1.0.1 - Changelog

- Removed widget support
- Edited some spellings + added more examples into readme.md

##### Release 1.0 - Changelog

- Official stable release


Requirements
------------
- PHP 5.4+
- Curl and php-curl installed


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

```
php composer.phar require --prefer-dist linslin/yii2-curl "*"
```


Usage
-----

Once the extension is installed, simply use it in your code. The following example shows you how to handling a simple GET Request. 

```
<?php
/**
 * Yii2 test controller
 * With RESTful support. Inspired by wenbin1989/yii2-curl.
 *
 * @category  Web-yii2-example
 * @package   yii2-curl-example
 * @author    Nils Gajsek <info@linslin.org>
 * @copyright 2013-2015 Nils Gajsek<info@linslin.org>
 * @license   http://opensource.org/licenses/MIT MIT Public
 * @version   1.0.2
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
     * cURL Get example
     */
    public function actionGetExample()
    {
        //Init curl
        $curl = new curl\Curl();

        //get http://example.com/
        $response = $curl->get(
            'http://example.com/'
        );
    }
    

    /**
     * cURL Post example
     */
    public function actionGetExample()
    {
        //Init curl
        $curl = new curl\Curl();

        //get http://example.com/
        $response = $curl->post(
            'http://example.com/'
        );
    }
    

    /**
     * cURL advanced GET example with HTTP status codes
     */
    public function actionGetAdvancedExample()
    {
        //Init curl
        $curl = new curl\Curl();

        //get http://example.com/
        $response = $curl->post(
            'http://example.com/'
        );
        
        // List of status codes here http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
        switch ($curl->responseCode) {
        
            case 200:
                //success logic here
                break;
                
            case 404:
                //404 Error logic here
                break;
        }
    }
}
```

