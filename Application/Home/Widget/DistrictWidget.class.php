<?php
namespace Home\Widget;
use Think\Controller;
use Home\Model\DistrictModel;

class DistrictWidget extends Controller{
	public function showFilter($url, $city, $curArea = 0){
		if($curArea === 0){
			$curArea = I('area', 0);
		}

		$areaList = DistrictModel::getAreaOfCity($city['id']);
		
		array_unshift($areaList, array('name'=>'全部','id'=>'0'));

		for($i = 0, $size = count($areaList); $i < $size; $i++){
			$area = &$areaList[$i];
			$area['url'] = str_replace('[area]', $area['id'], $url);
			if($curArea == $area['id']){
				$area['active'] = true;
			}
		}

		$this->assign('areaList', $areaList);

		$this->display('Widget:districtFilter');
	}

	public function showSelect($city){
		$areaList = DistrictModel::getChild($city['id']);
		if(empty($areaList)){
			return '';
		}
		$areaIdList = array();
		$html = '<select id="area" name="area"><option value="">区域</option>';
		foreach($areaList as $area){
			$areaIdList[] = $area['id'];
			$html .= "<option value=\"{$area['id']}\">{$area['name']}</option>";
		}
		$html .= '</select>';

		$busiAreaList = DistrictModel::getChild($areaIdList);
		if(!empty($busiAreaList)){
			$html .= '<select id="busi_area" name="busi_area"><option value="">商区</option>';
			foreach($busiAreaList as $busiArea){
				$html .= "<option value=\"{$busiArea['id']}\" class=\"{$busiArea['pid']}\">{$busiArea['name']}</option>";
			}
			$html .= '</select>';
		}

		return $html;
	}
}