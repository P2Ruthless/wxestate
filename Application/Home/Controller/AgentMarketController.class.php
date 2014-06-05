<?php
namespace Home\Controller;

class AgentMarketController extends HouseController{
	public function lists(){
		$AgentMarket = M('DocumentAgentmarket');
		$amList = $AgentMarket->alias('am')
			->field('doc.id,doc.title,area.name area_name, busi_area.name busi_area_name,am.price_avg,am.contact_tel,am.loc_txt,am.thumbnail')
			->join('to_document doc on doc.id=am.id')
			->join('to_district area on area.id=am.area', 'LEFT')
			->join('to_district busi_area on busi_area.id=am.busi_area', 'LEFT')
			->order('id desc')
			->select();

		$this->assign('amList', $amList);

		$this->display();
	}
}