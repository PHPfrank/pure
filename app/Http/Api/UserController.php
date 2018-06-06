<?php
/**
 * Created by LI.
 * User: LI
 * Date: 2018/5/30
 * Time: 15:56
 */
namespace App\Http\Api;

use App\Model\User;
use App\Services\Api\UserService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendMsg;

class UserController extends ApiController
{

    public function __construct()
    {
        $this->service = new UserService();
    }



    public function baseInfo()
    {
        $field =  ["token"];
        $data = [];

        foreach ($field as $key){
            if ($this->input($key)){
                $data[$key] = $this->input($key);
            }
        }
        $user = User::where(['token'=>$data['token']])->first();
        $result['info'] = $user ? $user : [];
        //数据返回
        return $this->wantsJson($result);
        //return $this->errorJson(1001);

    }

    public function test()
    {
        $result = $this->service->youZanTest();
        //数据返回
        return $this->wantsJson($result);
    }

    public function queue()
    {
        $email = "xxxxxx@qq.com";
        $job = (new SendMsg($email))->delay(10);
        dispatch($job);
    }


}