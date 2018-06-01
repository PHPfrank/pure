<?php
/**
 * @File: time.php
 * @Author: xiongjinhai
 * @Email:562740366@qq.com
 * @Date: 2017/10/27下午4:16
 * @Version:Version:1.1 2017 by www.dsweixin.com All Rights Reserver
 */

/**
 * 时间转换倒计时
 * @param array $data
 * @return bool|string
 */
if (! function_exists("time_second")) {

    function time_second($seconds){

        $seconds = (int)$seconds;

        if ($seconds<3600){

            $format_time = gmstrftime('%M分',$seconds);

        }elseif ($seconds < 86400){

            $format_time = gmstrftime('%H时%M分', $seconds);

        }else{

            $time = explode(' ', gmstrftime('%j %H %M %S', $seconds));//Array ( [0] => 04 [1] => 14 [2] => 14 [3] => 35 )

            $format_time = ($time[0]-1).'天'.$time[1].'时'.$time[2].'分';
        }

        return $format_time;
    }
}
if (!function_exists("tran_time")){

    function tran_time($create_time,$get_time=""){

        if (empty($get_time)) $get_time = time();


        $time = abs($create_time-$get_time);

        switch (true){
            case $time < 60 :

                return  preg_replace ("/^0+/", "", gmstrftime("%S", $time))."秒前";

                break;
            case $time < (60*60) :

                return  preg_replace ("/^0+/","", gmstrftime("%M", $time))."分钟前";

                break;
            case $time < (60*60*24):

                return preg_replace ("/^0+/", "", gmstrftime("%H", $time))."小时前";

                break;
            case $time < (60*60*24*30) :

                return preg_replace ("/^0+/", "", (gmstrftime("%j", $time))-1)."天前";

                break;

            case $time < (60*60*24*30*12):

                return preg_replace ("/^0+/", "", (gmstrftime("%m", $time))-1)."月前";

                break;

            default :

                return intval($time/(60*60*24*30*12))."年前";

                break;

        }
    }
}
