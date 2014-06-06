<?php
namespace Home\Logic;

class AgentMarketLogic extends BaseLogic{
	protected $tableName = 'agentmarket';

	public function detail($id){
		$data = parent::detail($id);
		if($data == false){
			return false;
		}

		//户型图
		$AgentMarketPic = M('AgentmarketPics');

		$hxPicList = $AgentMarketPic->alias('amp')
			->field('amp.id,amp.desc_txt,amp.busi_type,pic.path')
			->join('to_picture pic on amp.id=pic.id')
			->where(array('pic.pid'=>$id))
			->limit(4)
			->select();

		$data['hxPicList'] = $hxPicList;

		return $data;

	}

	public function listsForIndex($city, $count){
		$AgentMarket = M('DocumentAgentmarket');
		return $AgentMarket->alias('am')
			->field('doc.id,doc.title,am.loc_txt,am.price_avg,am.contact_tel,am.thumbnail,area.name area_name,busi_area.name busi_area_name')
			->join('to_document doc on doc.id=am.id')
			->join('to_district area on area.id=am.area', 'LEFT')
			->join('to_district busi_area on busi_area.id=am.busi_area', 'LEFT')
			->where(array(
				'doc.status'=>array('NEQ', -1),
				'doc.category_id'=>10005,
				'doc.create_time'=>array('lt', NOW_TIME),
				'doc.deadline=0 OR doc.deadline>'.NOW_TIME,
				'doc.position & 4 = 4',
				'am.city'=> is_array($city) ? $city['id'] : $city
			))
			->order('doc.id desc')
			->limit($count)
			->select();
	}
}