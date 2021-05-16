<?php

namespace Aoxiang\QuanMa;

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;

class Client
{

    use RequestTrait;

    protected $request = null;
    protected $domain = 'http://99shou.cn';
    protected $accessToken = '';
    protected $username, $password;
    protected $channelid = 'OP0002';
    protected $devCode, $key;

    public function __construct(string $username, string $password, string $key, string $devCode)
    {
        $this->username = $username;
        $this->password = $password;
        $this->devCode  = $devCode;
        $this->key      = $key;
        $this->request  = new \GuzzleHttp\Client();
        $this->getAccessToken();

    }


    /**
     * @return false|int|mixed
     */
    public function getAccessToken()
    {
        $data = Cache::get('quanma_access_token');
        if( empty($data) || time() > $data['expire_time'] ){
            $data                = $this->post('/api/user-server/user/customer/login', [
                'username' => $this->username,
                'password' => md5($this->password),
            ]);
            $data['expire_time'] = strtotime('+1 hour');
            Cache::put('quanma_access_token', $data, $data['expire_time']);
        }
        $this->accessToken = $data['token'];

        return $this->accessToken;
    }

    public function coupons()
    {
        $data = $this->post('/api/coupons-server/coupons/info/out/devinfos', ['nothing' => 'nothing'], [
            'proxy' => [
//                'http' => '127.0.0.1:8888',
            ],
        ]);

        return $data;
    }

    public function coupon(int $id)
    {

        return $this->post('/api/coupons-server/coupons/info/out/devdetail', [
            'id' => $id,
        ], [
            'proxy' => [
//                'http' => '127.0.0.1:8888',
            ],
        ]);
    }

    public function buy($id, $number = 1, $outId = '', $autoCommitHours = 24)
    {
        //todo 校验购买条件
        if( $number > 10 ){
            throw new \Exception('最多购买 10 件');
        }

        return $this->post('/api/coupons-server/coupons/info/out/pay/applyAndPay', [
            'id'              => $id,
            'buyNum'          => $number,
            'autoCommitHours' => $autoCommitHours,
            'outId'           => $outId,
        ], [
            'proxy' => [
//                'http' => '127.0.0.1:8888',
            ],
        ]);
    }
//    protected function get($uri, $data, $options = [])
//    {
//        $this->request->get($this->domain . $uri, [
//            'headers' => [
//                'content-type' => 'application/json',
//                'channelid'    => 'OP0002',
//                'txntime'      => time(),
//            ],
//        ]);
//    }


    private function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());

        return (float) sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}