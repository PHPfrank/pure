<?php
/**
 * @File: array.php
 * @Author: xiongjinhai
 * @Email:562740366@qq.com
 * @Date: 2017/9/13下午6:22
 * @Version:Version:1.1 2017 by www.dsweixin.com All Rights Reserver
 */

/**
 * 多维数组转换字符串
 * @param array $data
 * @return bool|string
 */
if (! function_exists('arr_str')) {
    function arr_str(array $data,$key = ""){
        if (!empty($key)){
            $str = "";
            foreach ($data as $value){
                $str.="'".$value[$key]."'".",";
            }
            $str = substr($str,0,-1);
        }else{
            foreach ($data as $value){
                $value = join(",",$value);//可以用implode将一维数组转换为用逗号连接的字符串
                $temp[] = $value;
            }
            $str = "";
            foreach ($temp as $value){
                $str.="'".$value."'".",";
            }
            $str = substr($str,0,-1);
        }
        return $str;
    }
}
if (! function_exists('merge_filter')) {
    /**
     * 回调函数过滤数组中的值,并且合并数组
     * @param array $data 初始数据源
     * @param array $array 获取的数据源
     * @param string $delimiter 格式化
     * @return array
     */
    function merge_filter(array $data, array $array,$delimiter='|')
    {

        foreach ($array as $key => $value){

            $filter  = array_merge(array_filter($data,function ($element) use ($value){ return $element['name'] == $value;}));

            if (!empty($filter)){
                if ($filter[0]['type'] == "string"){

                    $array_filter[$value]['value'] = $filter[0]['value'];

                }elseif ($filter[0]['type'] == "array"){

                    $array_filter[$value]['value'] = explode($delimiter,$filter[0]['value']);

                }
                $array_filter[$value]['status'] = $filter[0]['status'];
            }
        }

        return isset($array_filter) ? $array_filter : array();
    }
}
if (!function_exists('array_replacement')){
    /**
     * 数组内容替换
     * @param $data
     * @return mixed
     */
    function array_replacement($data){

        foreach ($data as $key => $value){

            if (gettype($value) == "array" || gettype($value) == "object"){
                if ((empty($value) && is_array($value)) || (empty($value) && is_object($value))){
                    $value = (object)($value);
                }else{
                    $data[$key] =  array_replacement($value);
                }
            }
            if ($value !== 0 && $value !== false && empty($value)) $data[$key] = "";
        }
        return $data;
    }
}

if (! function_exists('array_to_object')) {
    /**
     * 数组 转 对象
     *
     * @param array $arr 数组
     * @return object
     */
    function array_to_object($arr)
    {
        if (gettype($arr) != 'array') {
            return ;
        }

        foreach ($arr as $k => $v) {

            if (gettype($v) == 'array' || getType($v) == 'object') {

                $arr[$k] = (object)array_to_object($v);
            }

            if ($v !== 0 && empty($v)) $arr[$k] = "";
        }
        return (object)$arr;
    }
}
if (! function_exists('object_to_array')) {
    /**
     * 对象 转 数组
     *
     * @param object $obj 对象
     * @return array
     */
    function object_to_array($obj)
    {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)object_to_array($v);
            }
        }

        return $obj;
    }
}
if( ! function_exists('array_random')){
    /**
     * 随机返回数组中的值
     * @param  $array
     * @return mixed
     */
    function array_random($array)
    {
        return $array[ array_rand($array) ];
    }
}
if( ! function_exists('list_to_tree')){
    /**
     * 把返回的数据集转换成Tree
     *
     * @param array  $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param int    $root
     *
     * @return array
     */
    function list_to_tree(array $list, $pk = 'id', $pid = 'parent_id', $child = 'child', $root = 0)
    {
        // 创建Tree
        $tree = [];
        if(is_array($list)){
            // 创建基于主键的数组引用
            $refer = [];
            foreach ($list as $key => $data) {
                $refer[ $data[ $pk ] ] =& $list[ $key ];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[ $pid ];
                if($root == $parentId){
                    $tree[] =& $list[ $key ];
                } else {
                    if(isset($refer[ $parentId ])){
                        $parent =& $refer[ $parentId ];
                        $parent[ $child ][] =& $list[ $key ];
                    }
                }
            }
        }

        return $tree;
    }

    if( ! function_exists('two_dimensional_array_sort')){

        /**
         * 二维数组排序
         *
         * @param  $array
         * @param  $on
         * @param  $order
         *
         * @return array
         */
        function two_dimensional_array_sort($array, $on, $order = SORT_ASC)
        {
            $new_array = [];
            $sortable_array = [];
            $on = (string)$on;
            if(count($array) > 0){
                foreach ($array as $k => $v) {
                    if(is_array($v)){
                        foreach ($v as $k2 => $v2) {
                            if($k2 == $on){
                                $sortable_array[ $k ] = $v2;
                            }
                        }
                    } else {
                        $sortable_array[ $k ] = $v;
                    }
                }

                switch ($order) {
                    case SORT_ASC:
                        asort($sortable_array);
                        break;
                    case SORT_DESC:
                        arsort($sortable_array);
                        break;
                }

                foreach ($sortable_array as $k => $v) {
                    $new_array[ $k ] = $array[ $k ];
                }
            }

            return $new_array;
        }
    }
    if( ! function_exists('create_level_tree')){

        /**
         * 生成一维数组 HTML 层级树
         *
         * @param        $data
         * @param int    $parent_id
         * @param int    $level
         * @param string $html
         *
         * @return array
         */
        function create_level_tree($data, $parent_id = 0, $level = 0, $html = '-')
        {
            $tree = [];
            foreach ($data as $item) {
                $item['html'] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                $item['html'] .= $level === 0 ? "" : '|';
                $item['html'] .= str_repeat($html, $level);

                if($item['parent_id'] == $parent_id){
                    $tree[] = $item;
                    $tree = array_merge($tree, create_level_tree($data, $item['id'], $level + 1));
                }
            }

            return $tree;
        }
    }
}