<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use OT\DataDictionary;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends HomeController {

	//系统首页
    public function index(){
        //$city = get_current_city();
        //redirect(__ROOT__."/home/$city/", 0, '正在跳转...');
        //redirect(U('Weibo/Index/index'));
        /*
        $category = D('Category')->getTree();
        $lists    = D('Document')->lists(null);
        $this->assign('category',$category);//栏目
        $this->assign('lists',$lists);//列表
        $this->assign('page',D('Document')->page);//分页
        $this->display();
        */
        $city = get_current_city();

        //获取city下房屋买卖首页推荐 最多4条
        $houseSaleList = D('HouseSale', 'Logic')->listsForIndex($city, 4);

        //获取city下房屋租赁首页推荐 最多4条
        $houseRentList = D('HouseRent', 'Logic')->listsForIndex($city, 4);

        //热门楼盘 6条
        $agentMarketList = D('AgentMarket', 'Logic')->listsForIndex($city, 6);

        cookie('city', $city['id'], 30 * 24 * 60 * 60);

        $this->assign('houseRentList', $houseRentList);
        $this->assign('houseSaleList', $houseSaleList);
        $this->assign('agentMarketList', $agentMarketList);
        $this->assign('currentNav', 1);
        $this->assign('city', $city);
        
        $this->display();
    }

    public function cityIndex($city){
        $District = D('District');

        $city = $District->field('id,name')->find($city);
        if(empty($city)){
            $city = $District->field('id,name')->find(C('DEFAULT_CITY'));
        }
        
        cookie('city', $city['id'], 30 * 24 * 60 * 60);

        $this->assign('city', $city);

        $this->display();
    }

}