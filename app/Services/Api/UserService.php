<?php
/**
 * Created by LI.
 * User: LI
 * Date: 2018/5/31
 * Time: 11:40
 */

namespace App\Services\Api;

use App\Model\User;

class UserService
{
    protected $sms;

    public function getUserExtend()
    {
        $result = [];
        $result['user_info'] = User::where('name','!=','')->limit(10)->get();
        return $result;
    }

}