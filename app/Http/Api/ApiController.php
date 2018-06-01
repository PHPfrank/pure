<?php
/**
 * Created by PhpStorm.
 * API基类
 * User: Li
 * Date: 15/7/3
 * Time: 下午2:40
 */
namespace App\Http\Api;

use App\Http\Controllers\Controller;
use App\Model\User;
use Cache;
use Request;

class ApiController extends Controller
{
    protected $user;       //当前用户信息
    protected $args;
    protected $token;
    protected $appid;
    protected $version;
    protected $channel;
    protected $longitude;
    protected $latitude;
    protected $device;
    protected $device_app;
    protected $header = ["Content-Type"=>"application/json;charset=utf-8"];

    public function __construct()
    {
        parent::__construct();

        //所有请求参数
        $this->_args  = Request::all();
        $this->token = Request::input('token') ?: $this->header('Token');
        //根据access_token获取用户信息
        $this->user      =  $this->_getUser();
        $this->appid      = $this->header('Appid');
        $this->version    = $this->header('Version', '1.0');
        $this->channel    = $this->header('Channel_name', 'develop');
        $this->longitude  = $this->header('Longitude', '');
        $this->latitude   = $this->header('Latitude', '');
        $this->device_app = $this->header('Device', '');
        $this->device     = $this->getRealDevice($this->device_app);
    }

    /**
     * 封装Request方法(取get,post,put值)
     *
     * @param $key
     * @param $defaultValue
     *
     * @return bool
     */
    protected function input($key, $defaultValue = false)
    {
        return isset($this->_args[$key]) ? $this->_args[$key] : $defaultValue;
    }

    protected function header($key)
    {
        return Request::header($key);
    }

    protected function file($key)
    {
        return Request::file($key);
    }

    /**
     * 根据访问的access_token读取对应的用户信息
     */
    public function _getUser()
    {
        $token = $this->token;
        if (!$token) {
            return false;
        }
        return User::getUser($token);
    }

    /**
     * //返回json数据
     *
     * @param array  $items
     * @param int    $err
     * @param string $msg
     *
     * @return array
     */
    function json($item, $s = 0)
    {
        $data = [
            'code'        => isset($item['code']) ? $item['code'] : 0,
            'msg'         => isset($item['msg']) ? $item['msg'] : '',
            'data'        => isset($item['data']) && $item['data'] ? $item['data'] : (object)[],
            'server_time' => time()

        ];
        return $data;
    }

    /**
     * 输出
     * @param string $data
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function wantsJson($data = ""){
        if (is_array($data)){
            return $this->returnJson($data);
        }elseif ($data == 1){
            return $this->stringJson();
        }
        else{
            return $this->errorJson($data);
        }
    }

    /**
     * 控制层数据返回json消息
     * @param string $msg
     * @param int $type
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function stringJson($msg = "成功",$type = 0,$status = 200){

        $data  = array(
            "msg"   => $msg,
            "error" => $type,
            "data"  => new \stdClass(),
        );

        return response()->json($data,$status,$this->header);
    }
    /**
     * 控制层数据返回json消息
     * @param string $content
     * @param int $type
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function returnJson($content= "",$type = 0,$status =200){

        $content = array_replacement($content);

        if ($content === "") $content = new \stdClass();

        $data = array(
            "msg"   => "成功",
            "error" => $type,
            "data"  => $content,
        );

        return response()->json($data,$status,$this->header);
    }
    /**
     * 控制层错误返回json消息
     * @param int $type
     * @param string $msg
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorJson($type=-1000,$msg="",$status=200){

        $data = array(
            "msg" => $msg ? $msg : $this->error_msg($type),
            "error"  => $type,
            "data" => new  \stdClass(),
        );

        return response()->json($data,$status,$this->header);
    }


    function getRealDevice($device)
    {
        if (substr($device, 0, 7) == 'android') {
            return 2;
        } elseif (substr($device, 0, 3) == 'ios') {
            return 1;
        } else {
            return 3;
        }
    }

    protected  function error_msg($type)
    {
        $lang = array(
            "0" => "成功",
            "1001" => "参数错误",
        );
        return isset($lang[$type]) ? $lang[$type] : "未知错误，请联系客服人员";
    }

}
