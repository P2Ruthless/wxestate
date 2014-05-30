<?php
namespace Home\Model;

use Think\Model;

class DistrictModel extends Model{
	
	const TYPE_PROVINCE = 1;
	const TYPE_CITY = 2;
	const TYPE_PCITY = 12; //直辖市
	const TYPE_PAREA = 21; //市辖区
	const TYPE_PAREA2 = 22; //县
	const TYPE_AREA = 3;
	const TYPE_BUSI_AREA = 4; //商区

	public static function getChild($pid){
		$model = D('District')->field('id,pid,name,type');
		$map['inactive'] = 'N';
		if(is_array($pid)){
			$map['pid'] = array('in', $pid);
		}else{
			$map['pid'] = $pid;
		}
		return $model->where($map)->select();
	}

	public static function getAreaOfCity($city){

		$areaList = array();

		if(empty($city)){
			return $areaList;
		}

		$districtList = DistrictModel::getChild($city);

		if(empty($districtList)){
			return $areaList;
		}

		foreach($districtList as $district){
			if($district['type'] == self::TYPE_PAREA || $district['type'] == self::TYPE_PAREA2){
				continue;
			}
			if($district['type'] == self::TYPE_AREA){
				$areaList[] = $district;
			}elseif($districtList == self::TYPE_PCITY){
				$areaList = array_combine($areaList, DistrictModel::getChild($district['id']));
			}
		}

		return $areaList;
	}

	public static function getCityByChild($area){
		$district = D('District')
			->field('id,pid,name,type')
			->find($area);

		if(empty($district) || $district['type'] <= self::TYPE_PROVINCE){
			return C('DEFAULT_CITY');
		}

		if($district['type'] == self::TYPE_CITY || $district['type'] == self::TYPE_PCITY){
			return $district;
		}

		return DistrictModel::getCitiByChild($district['pid']);
		
	}

	public static function getTree($root, $leafType){
		return D('District')->where(array('type'=>array('in', '2,12')))->select();

	}
}