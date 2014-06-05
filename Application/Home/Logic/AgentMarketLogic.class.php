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
}