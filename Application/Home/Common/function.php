<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}


/**
 * 生成系统AUTH_KEY
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function build_auth_key(){
    $chars  = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   // $chars .= '`~!@#$%^&*()_+-=[]{};:"|,.<>/?';
    $chars  = str_shuffle($chars);
    return substr($chars, 0, 40);
}
/*
function get_current_city($area = 0){
    static $current_city = 0;
    if(!empty($current_city)){
        return $current_city;
    }
    if(empty($area)){
        $current_city = cookie('city');
    }else{
        $current_city = DistrictModel::getCityByChild($area);
    }

    if(empty($current_city)){
        $current_city = C('DEFAULT_CITY');
    }

    cookie('city', $current_city);

    return $current_city;
}
*/

function get_current_city(){
    static $current_city = 0;
    if(!empty($current_city)){
        return $current_city;
    }

    $domain = $_SERVER['HTTP_HOST'];

    $city = explode('.', $domain);
    $city = $city[0];

    //TODO:
    if($city == 'bj'){
        return array('id'=>3, 'name'=>'北京');
    }else{
        return array('id'=>517, 'name'=>'大连');
    }
}