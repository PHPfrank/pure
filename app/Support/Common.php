<?php

namespace App\Support;


/**
 * 通用扩展类
 * @author
 */
use DB;
use Log;
class Common
{

    /**
     * 短信验证码
     * @param $url
     * @param array $params
     * @return mixed
     */
    public static function curl_sms($url,$params=array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    //生成唯一订单编号
    public static function makePaySn($uid)
    {
        return mt_rand(100, 999)
        . sprintf('%010d', time() - 946656000)
        . sprintf('%03d', (float)microtime() * 1000)
        . sprintf('%03d', (int)$uid % 1000);
    }

    /**
     * 格式化金额
     *
     * @param     $str     需要格式化的字符串
     * @param int $ws      保留几位小数
     *
     * @return string  返回格式化的数据
     */
    public static function getRealamount($str, $ws = 2)
    {
        return sprintf("%.{$ws}f", round($str, $ws));
    }

    //经纬度计算距离
    static public function GetInstance($lng1, $lat1, $lng2, $lat2){
        // 将角度转为狐度
        $radLat1 = deg2rad((float)$lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad((float)$lat2);
        $radLng1 = deg2rad((float)$lng1);
        $radLng2 = deg2rad((float)$lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        $res = round($s/1000,3);
        return $res;
    }

    //数组单维度展示排序
    static public function sortArrByOneField(&$array, $field, $desc = false)
    {
        $array    = self::object2array($array);
        $fieldArr = [];
        foreach ($array as $k => $v) {
            $fieldArr[$k] = $v[$field];
        }
        $sort = $desc == false ? SORT_ASC : SORT_DESC;
        array_multisort($fieldArr, $sort, $array);
        return $array;
    }

    //对象转数组方法
    static public function object2array($object)
    {
        $array = [];
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        } else {
            $array = $object;
        }
        return $array;
    }

    //微信curl请求
    public static function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    /**生成短连接
     * @param $url
     * @return string
     */
    public static function dwz($url)
    {
//        $long_url = "http://suo.im/api.php?url=".$url;
//        return self::httpGet($long_url);
        define('SINA_APPKEY', '1681459862');
        $url = 'http://api.t.sina.com.cn/short_url/shorten.json?source=' . SINA_APPKEY . '&url_long=' . $url;
        //获取请求结果
        $result = self::httpGet($url);
        //下面这行注释用于调试，
        //print_r($result);exit();
        //解析json
        $json = json_decode($result);
        //异常情况返回false
        if (isset($json->error) || !isset($json[0]->url_short) || $json[0]->url_short == '')
            return false;
        else
            return $json[0]->url_short;
    }
//        $url = "http://jump.sinaapp.com/api.php?url_long=" . $url;
//
//        $res = json_decode(self::httpGet($url),true);
//        return $res['url_short'];
//    }



    /**
     * 通过出生日期转换年龄
     */
    public static function birthdayToAge($birthday)
    {

        $age = strtotime($birthday);
        if ($age === false) {
            return 0;
        }

        list($y1, $m1, $d1) = explode("-", date("Y-m-d", $age));
        $now = strtotime("now");
        list($y2, $m2, $d2) = explode("-", date("Y-m-d", $now));
        $age = $y2 - $y1;

        return $age;
    }

    /**
     * 通过年龄转换出生日期
     */
    public static function ageToBirthday($age, $isChange = false)
    {
        if ($isChange == false) {
            return date('Y') - $age . date("-m-d");
        } else {
            return (date('Y') - $age + 1) . date("-m-d");
        }
    }


    //根据生日返回用户的属相
    public static function birthdayBornTag($birthday)
    {
        $year = substr($birthday, 0, 4);
        $bornTagarray = ['属猴', '属鸡', '属狗', '属猪', '属鼠', '属牛', '属虎', '属兔', '属龙', '属蛇', '属马', '属羊'];
        $index = $year % 12;
        $bornTag = $bornTagarray[$index];
        return $bornTag;
    }

    //根据生日返回用户的星座
    public static function getConstellation($birthday)
    {
        $month = substr($birthday, 5, 2);
        $day = substr($birthday, -2, 2);
        // 检查参数有效性
        if ($month < 1 || $month > 12 || $day < 1 || $day > 31)
            return (false);
        // 星座名称以及开始日期
        $signs = array(
            array("20" => "水瓶座"),
            array("19" => "双鱼座"),
            array("21" => "白羊座"),
            array("21" => "金牛座"),
            array("21" => "双子座"),
            array("22" => "巨蟹座"),
            array("23" => "狮子座"),
            array("23" => "处女座"),
            array("23" => "天秤座"),
            array("24" => "天蝎座"),
            array("22" => "射手座"),
            array("22" => "摩羯座")
        );
        list($sign_start, $sign_name) = each($signs[(int)$month - 1]);

        if ($day < $sign_start)
            list($sign_start, $sign_name) = each($signs[($month - 2 < 0) ? $month = 11 : $month -= 2]);
        return $sign_name;
    }


    /**
     * 获取客户端IP地址
     */
    public static function getIPaddress()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $IPaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $IPaddress = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $IPaddress = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $IPaddress = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $IPaddress = getenv("HTTP_CLIENT_IP");
            } else {
                $IPaddress = getenv("REMOTE_ADDR");
            }
        }
        return $IPaddress;
    }

    /**
     * 根据ip获取所在的城市
     * @param $ip
     * @param $type 2 返回省份拼接城市,1 只返回城市
     * @return bool
     */
    public static function getIpInfo($ip, $type = 2)
    {
        $path = resource_path('ip2region.db');
        $client = new \Ip2Region($path);
        $result = $client->btreeSearch($ip);
        $data = explode('|', $result['region']);
        if (count($data) > 3) {
            $p = '';
            if ($data[2] != '0') {
                $p = $data[2];
                if (mb_strlen($p, 'utf-8') > 2) {
                    $p = rtrim($p, '省');
                    $p = rtrim($p, '市');
                }
            }
            $c = '';
            if ($data[3] != '0') {
                $c = $data[3];
                if (mb_strlen($c, 'utf-8') > 2) {
                    $c = rtrim($c, '市');
                }
            }
            if ($type == 2) {
                return $p . '-' . $c;
            } else {

                if (in_array($p, ['北京', '天津', '上海', '重庆'])) {
                    return $p;
                }

                return $c;
            }

        }
        return false;
    }

    /**
     * 根据ip获取所在的国家
     * @param $ip
     * @return bool
     */
    public static function getIpCountry($ip)
    {
        $path = resource_path('ip2region.db');
        $client = new \Ip2Region($path);
        $result = $client->btreeSearch($ip);
        $data = explode('|', $result['region']);
        if (count($data) > 0) {
            return $data[0];
        }
        return false;
    }

    /**
     * curl 请求
     * @param $url
     * @param array $data
     * @param array $header
     * @param string $method
     * @return int|mixed
     */
    public static function curlRequest($url, $data = [], $header = [], $method = 'post')
    {

        $timeout = 5000;

        $http_header = [];
        if ($header) {
            foreach ($header as $key => $val) {
                $http_header[] = $key . ':' . $val;
//                    array_push($http_header, $key . ':' . $val);
            }
        }
        $post = false;
        if ($method == 'post') {
//            $postdataArray = array();
//            foreach ($data as $key => $value) {
//                array_push($postdataArray, $key . '=' . $value);
//            }
//            $postdata = join('&', $postdataArray);
            $post = true;
        }


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $post);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //处理http证书问题
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        if (false === $result) {
            $result = curl_errno($ch);
        }
        curl_close($ch);
        return $result;
    }

    /**
     * 处理时间日期格式
     */
    public static function timeToDate($time)
    {
        if (date('Y-m-d') == date('Y-m-d', strtotime($time))) { //当天的返回00:00格式
            $result = date('H:i', strtotime($time));
            return $result;
        } else if (date('Y-m-d', time() - 24 * 3600) == date('Y-m-d', strtotime($time))) {//昨天的格式
            $result = '昨天';
            return $result;
        } else if (date('Y-m-d', time() - 24 * 3600) > date('Y-m-d', strtotime($time))) { //前天及之前的时间格式
            $result = date('m-d', strtotime($time));
            return $result;
        } else if (time() - strtotime($time) > 3600 * 24 * 365) { //超过一年的显示年月日
            $result = date('Y-m-d', strtotime($time));
            return $result;
        }

    }




    /**
     * 文件上传
     * @param $file
     * @return string
     */
    public static function upload($file)
    {
        $data = array(
            'pic' => curl_file_create(realpath($file['tmp_name']), $file['type'], $file['name'])
        );

        $url = env("PIC_URL", 'http://aliyun.com/upload');
        $header = ['channel' => env('FILE_CHANNEL', "")];

        $result = self::curlRequest($url, $data, $header);
        //Log::info($result);
        $return_data = json_decode($result, true);

        if (isset($return_data['items'])) {
            if (count($return_data['items'])) {
                return $return_data['items'][0];
            }
        }
        return "";
    }


}