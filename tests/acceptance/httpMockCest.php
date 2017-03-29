<?php
//use namespaces
use Mcustiel\Phiremock\Client\Phiremock;
use Mcustiel\Phiremock\Client\Utils\A;
use Mcustiel\Phiremock\Client\Utils\Respond;
use Mcustiel\Phiremock\Client\Utils\Is;

/**
 * Class httpMockCest
 */
class httpMockCest
{
    // ############################################### private class vars // ###########################################

    /**
     * @type linslin\yii2\curl\Curl
     */
    private $_curl = null;

    /**
     * @type string
     */
    private $_endPoint = 'http://127.0.0.1:18080';


    // ################################################## class methods // #############################################

    /**
     * Cleanup
     * @param \AcceptanceTester $I
     */
    public function _before(\AcceptanceTester $I)
    {
        $I->haveACleanSetupInRemoteService();

        //Init curl
        $this->_curl = new linslin\yii2\curl\Curl();
    }


    /**
     * Simple HTTP ok
     * @param \AcceptanceTester $I
     */
    public function simpleHttpOkTest (\AcceptanceTester $I)
    {
        $I->expectARequestToRemoteServiceWithAResponse(
            Phiremock::on(
                A::getRequest()->andUrl(Is::equalTo('/test/httpStatus/200'))
            )->then(
                Respond::withStatusCode(200)
            )
        );

        $this->_curl->get($this->_endPoint.'/test/httpStatus/200');
        $I->assertEquals($this->_curl->responseCode, 200);
    }


    /**
     * Try set params to send with get request
     * @param \AcceptanceTester $I
     */
    public function setGetParamsTest (\AcceptanceTester $I)
    {
        //Init
        $this->_curl->reset();
        $params = [
            'key' => 'value',
            'secondKey' => 'secondValue'
        ];

        $I->expectARequestToRemoteServiceWithAResponse(
            Phiremock::on(
                A::getRequest()->andUrl(Is::equalTo('/test/params/get?'.http_build_query($params)))
            )->then(
                Respond::withStatusCode(200)
            )
        );

        $this->_curl->setGetParams($params)
            ->get($this->_endPoint.'/test/params/get');

        $I->assertEquals($this->_curl->responseCode, 200);
        $I->assertEquals($this->_curl->getUrl(), $this->_endPoint.'/test/params/get?'.http_build_query($params));
    }


    /**
     * Try set post to send with post request
     * @param \AcceptanceTester $I
     */
    public function setPostParamsTest (\AcceptanceTester $I)
    {
        //Init
        $this->_curl->reset();
        $params = [
            'key' => 'value',
            'secondKey' => 'secondValue'
        ];


        $I->expectARequestToRemoteServiceWithAResponse(
            $expectation = Phiremock::on(
                A::postRequest()->andUrl(Is::equalTo('/test/params/post'))
                    ->andBody(Is::equalTo(http_build_query($params)))
                    ->andHeader('Content-Type', Is::equalTo('application/x-www-form-urlencoded'))
            )->then(
                Respond::withStatusCode(200)
            )
        );

        $this->_curl->setPostParams($params)
            ->post($this->_endPoint.'/test/params/post');
        $I->assertEquals($this->_curl->responseCode, 200);
    }


    /**
     * Try set post to send with post request
     * @param \AcceptanceTester $I
     */
    public function setPostParamsOptionTest (\AcceptanceTester $I)
    {
        //Init
        $this->_curl->reset();
        $params = [
            'key' => 'value',
            'secondKey' => 'secondValue'
        ];


        $I->expectARequestToRemoteServiceWithAResponse(
            $expectation = Phiremock::on(
                A::postRequest()->andUrl(Is::equalTo('/test/params/post'))
                    ->andBody(Is::equalTo(http_build_query($params)))
                    ->andHeader('Content-Type', Is::equalTo('application/x-www-form-urlencoded'))
            )->then(
                Respond::withStatusCode(200)
            )
        );

        $this->_curl->setOption(
            CURLOPT_POSTFIELDS,
            http_build_query($params))
            ->post($this->_endPoint.'/test/params/post');
        $I->assertEquals($this->_curl->responseCode, 200);
    }


    /**
     * Try set post param with header modification
     * @param \AcceptanceTester $I
     */
    public function setPostParamsWithHeaderTest (\AcceptanceTester $I)
    {
        //Init
        $this->_curl->reset();
        $params = [
            'key' => 'value',
            'secondKey' => 'secondValue'
        ];

        $I->expectARequestToRemoteServiceWithAResponse(
            $expectation = Phiremock::on(
                A::postRequest()->andUrl(Is::equalTo('/test/params/post'))
                    ->andBody(Is::equalTo(http_build_query($params)))
                    ->andHeader('Content-Type', Is::equalTo('application/json'))
            )->then(
                Respond::withStatusCode(200)
            )
        );

        $this->_curl->setPostParams($params)
            ->setHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post($this->_endPoint.'/test/params/post');
        $I->assertEquals($this->_curl->responseCode, 200);
    }


    /**
     * Post JSON data test
     * @param \AcceptanceTester $I
     */
    public function postJsonTest (\AcceptanceTester $I)
    {
        //Init
        $this->_curl->reset();
        $params = [
            'key' => 'value',
            'secondKey' => 'secondValue'
        ];

        $I->expectARequestToRemoteServiceWithAResponse(
            $expectation = Phiremock::on(
                A::postRequest()->andUrl(Is::equalTo('/test/params/post'))
                    ->andBody(Is::equalTo(json_encode($params)))
                    ->andHeader('Content-Type', Is::equalTo('application/json'))
                    ->andHeader('Content-Length', Is::equalTo(strlen(json_encode($params))))
            )->then(
                Respond::withStatusCode(200)
            )
        );

        $this->_curl->setRequestBody(json_encode($params))
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Content-Length' => strlen(json_encode($params))
            ])
            ->post($this->_endPoint.'/test/params/post');
        $I->assertEquals($this->_curl->responseCode, 200);
    }


    /**
     * Get JSON response test
     * @param \AcceptanceTester $I
     */
    public function getWithDecodedJsonResponseTest(\AcceptanceTester $I)
    {
        //Init
        $this->_curl->reset();

        $I->expectARequestToRemoteServiceWithAResponse(
            $expectation = Phiremock::on(
                A::getRequest()->andUrl(Is::equalTo('/test/params/get/json'))
            )->then(
                Respond::withStatusCode(200)
                    ->andBody('{"id": 1, "description": "I am a resource"}')
            )
        );

        $jsonResponse = $this->_curl->get($this->_endPoint . '/test/params/get/json', false);
        $I->assertEquals($this->_curl->responseCode, 200);
        $I->assertArrayHasKey('id', $jsonResponse);
        $I->assertArrayHasKey('description', $jsonResponse);
        $I->assertEquals($jsonResponse['id'], 1);
        $I->assertEquals($jsonResponse['description'], 'I am a resource');
    }


    /**
     * Get JSON response test
     * @param \AcceptanceTester $I
     */
    public function getWithRawJsonResponseTest(\AcceptanceTester $I)
    {
        //Init
        $this->_curl->reset();

        $I->expectARequestToRemoteServiceWithAResponse(
            $expectation = Phiremock::on(
                A::getRequest()->andUrl(Is::equalTo('/test/params/get/json'))
            )->then(
                Respond::withStatusCode(200)
                    ->andBody('{"id": 1, "description": "I am a resource"}')
            )
        );

        $rawResponse = $this->_curl->get($this->_endPoint . '/test/params/get/json', true);
        $I->assertEquals($this->_curl->responseCode, 200);
        $I->assertEquals($rawResponse, '{"id": 1, "description": "I am a resource"}');
    }


    /**
     * Get header params with special header separators in values
     *
     * @issue https://github.com/linslin/Yii2-Curl/issues/59
     * @param \AcceptanceTester $I
     */
    public function getHeaderParamWithSpecialHeaderSeparatorInValue (\AcceptanceTester $I)
    {
        //Init
        $this->_curl->reset();

        $I->expectARequestToRemoteServiceWithAResponse(

            Phiremock::on(
                A::getRequest()->andUrl(Is::equalTo('/test/header'))
            )->then(
                Respond::withStatusCode(200)
                    ->andHeader('param', 'value')
                    ->andHeader('location', 'http://somelocation/')
            )
        );

        $this->_curl->get($this->_endPoint.'/test/header');

        $I->assertEquals($this->_curl->responseCode, 200);
        $I->assertEquals($this->_curl->responseHeaders['location'], 'http://somelocation/');
        $I->assertEquals($this->_curl->responseHeaders['param'], 'value');
    }
}