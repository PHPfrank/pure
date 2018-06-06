<?php
namespace App\Libraries\YouZan;

use App\Libraries\YouZan\Lib\YZTokenClient;
use App\Libraries\YouZan\Lib\YZGetTokenClient;


// 加载区域结点配置


/**
 * Created on 17/6/7.
 * 号码隐私保护API产品的DEMO程序,工程中包含了一个PlsDemo类，直接通过
 * 执行此文件即可体验号码隐私保护产品API功能(只需要将AK替换成开通了云通信-号码隐私保护产品功能的AK即可)
 * 备注:Demo工程编码采用UTF-8
 */
class UseYouZan
{

    private $appId;

    private $client_id;

    private $client_secret;

    private $token;

    private $client;

    public function __construct() {
        $this->appId = config('app.wx.appId');
        $this->client_id = "Test client";//请填入有赞云控制台的应用client_id
        $this->client_secret = "Testclientsecret";//请填入有赞云控制台的应用client_secret
        $this->token = self::getToken($this->client_id,$this->client_secret);
        $this->client = new YZTokenClient($this->token);
    }


    //获取access_token
    protected static function getToken($client_id , $client_secret)
    {
        $token = new YZGetTokenClient($client_id,$client_secret);
        $type = 'self';
        $keys['kdt_id'] = '164932';
        $access_token = $token->get_token( $type , $keys );
        return $access_token;

    }


    public function ticket()
    {

        $method = 'youzan.ump.promocode.add'; //要调用的api名称
        $api_version = '3.0.0'; //要调用的api版本号

        $my_params = [
            'value' => '200',
            'total' => '200',
            'title' => '优惠码',
            'start_at' => '2018-04-28 00:00:00',
            'range_type' => 'ALL',
            'quota' => '5',
            'is_at_least' => '0',
            'expire_notice' => '0',
            'end_at' => '2018-04-30 00:00:00',
            'code_type' => 'UNIQUE',
            'at_least' => '0',
        ];

        $my_files = [
        ];


        $res = $this->client->post($method, $api_version, $my_params, $my_files);
        return $res;
    }

}