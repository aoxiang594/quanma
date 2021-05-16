<?php


namespace Aoxiang\QuanMa;


use GuzzleHttp\Psr7\Response;
use mysql_xdevapi\Exception;

trait RequestTrait
{
    protected function post($uri, $data = [], $options = [])
    {
        $timestamp = $this->getMillisecond();
        $headers   = [
            'content-type' => 'application/json',
            'channelid'    => $this->channelid,
            'txntime'      => $timestamp,
        ];
        if( !empty($this->accessToken) ){
            $headers['token']   = $this->accessToken;
            $headers['devCode'] = $this->devCode;
            $headers['sign']    = $this->genSignature($data, $timestamp);
        }
        /** @var Response $response */
        $this->response = $this->request->post($this->domain . $uri, array_merge([
            'headers' => $headers,
            'json'    => $data,
        ], $options));

        return $this->parseResponse($this->response);
    }

    protected function parseResponse(Response $response = null)
    {
//        try {
        if( is_null($response) ){
            $response = $this->request;
        }

        $body = json_decode($response->getBody()->getContents(), true);

        if( is_array($body) ){
            if( $body['rtnCode'] == '000000' ){
                return $body['rtnData'];
            } else {
                throw new \Exception($body['rtnMsg']);
            }
        } else {

            throw new \Exception("解析 Response 失败,非数组");

        }

//        } catch (\Exception $e) {
//            throw new \Exception("解析 Response 失败");
//        }
    }

    protected function genSignature($data, $timestamp)
    {
//        $data = empty($data) ? '{}' : json_encode($data);
        $s = json_encode($data) . $this->key . $timestamp;

        return md5($s);
    }
}