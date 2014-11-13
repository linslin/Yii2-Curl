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

### Release 1.0.1 - Changelog

- Removed widget support
- Edited some spellings + added more examples into readme.md

### Release 1.0 - Changelog

- Official stable release


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist linslin/yii2-curl "*"
```

or add

```
"linslin/yii2-curl": "*"
```

or add
   
```
"linslin/yii2-curl": "dev-master"
```

to the require section of your `composer.json` file. Next example is a full output of a `composer.json` file including linslin/yii2-curl. This maybe will help you if you new in using composer.

```
{
    "name": "yiisoft/yii2-app-basic",
    "description": "Yii 2 Basic Application Template",
    "keywords": ["yii2", "framework", "basic", "application template"],
    "homepage": "http://www.yiiframework.com/",
    "type": "project",
    "license": "BSD-3-Clause",
    "support": {
        "issues": "https://github.com/yiisoft/yii2/issues?state=open",
        "forum": "http://www.yiiframework.com/forum/",
        "wiki": "http://www.yiiframework.com/wiki/",
        "irc": "irc://irc.freenode.net/yii",
        "source": "https://github.com/yiisoft/yii2"
    },
    "minimum-stability": "stable",
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/linslin/Yii2-Curl.git"
        }
    ],
    "require": {
        "php": ">=5.4.0",
        "yiisoft/yii2": "*",
        "yiisoft/yii2-bootstrap": "*",
        "yiisoft/yii2-swiftmailer": "*",
        "linslin/yii2-curl": "dev-master"
    },
    "require-dev": {
        "yiisoft/yii2-codeception": "*",
        "yiisoft/yii2-debug": "*",
        "yiisoft/yii2-gii": "*",
        "yiisoft/yii2-faker": "*"
    },
    "config": {
        "process-timeout": 1800
    },
    "scripts": {
        "post-create-project-cmd": [
            "yii\\composer\\Installer::postCreateProject"
        ]
    },
    "extra": {
        "yii\\composer\\Installer::postCreateProject": {
            "setPermission": [
                {
                    "runtime": "0777",
                    "web/assets": "0777",
                    "yii": "0755"
                }
            ],
            "generateCookieValidationKey": [
                "config/web.php"
            ]
        },
        "asset-installer-paths": {
            "npm-asset-library": "vendor/npm",
            "bower-asset-library": "vendor/bower"
        }
    }
}
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
 * @category  Web-yii2
 * @package   yii2-curl
 * @author    Nils Gajsek <info@linslin.org>
 * @copyright 2013-2014 Nils Gajsek<info@linslin.org>
 * @license   http://opensource.org/licenses/MIT MIT Public
 * @version   1.0
 * @link      http://www.linslin.com
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
    public function actionGetExample()
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

