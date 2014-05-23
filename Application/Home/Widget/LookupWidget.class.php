<?php
namespace Home\Widget;

use Think\Controller;
use Common\Model\LookupModel;

class LookupWidget extends Controller{
	public function showFilter($url, $replace, $type, $curName){
		$curName = $curName.'';
		$lookList = LookupModel::getList($type);
		array_unshift($lookList, array('val'=>'全部','name'=>'0'));

		foreach($lookList as &$lookup){
			if($curName == $lookup['name']){
				$lookup['active'] = true;
			}
			$lookup['url'] = str_replace($replace, $lookup['name'], $url);
		}

		$this->assign('lookList', $lookList);

		$this->display('Widget:lookupFilter');
	}

	public function showTab($url, $replace, $type, $curName){
		$lookList = LookupModel::getList($type);

		foreach($lookList as &$lookup){
			if($curName == $lookup['name']){
				$lookup['active'] = true;
			}
			$lookup['url'] = str_replace($replace, $lookup['name'], $url);
		}

		$this->assign('lookList', $lookList);

		$this->display('Widget:lookupTab');
	}

	public function showSelect($name, $value, $type, $first=array('name'=>'0','val'=>"==请选择=="), $htmlOptions=array()){
		$html = "<select name='$name'";

		foreach($htmlOptions as $key=>$value){
			$html .= " $key=\"$value\"";
		}

		$html .= '>';

		if($first){
			$html .= "<option value=\"{$first['name']}\">{$first['val']}</option>";
		}

		$lookList = LookupModel::getList($type);
		if(!empty($lookList)){
			foreach($lookList as $nv){
				$html .= "<option value=\"{$nv['name']}\"";
				if($value == $nv['name']){
					$html .= ' selected';
				}
				$html .= ">{$nv['val']}</option>";
			}
		}

		$html .= "</select>";

		return $html;
	}

	public function showRadio($name, $value, $type){
		$html = '';
		$lookList = LookupModel::getList($type);
		if(!empty($lookList)){
			foreach($lookList as $nv){
				$html .= "<input type='radio' name='$name' value=\"{$nv['name']}\"";
				if($value == $nv['name']){
					$html .= " checked";
				}
				$html .= ">{$nv['val']}";
			}
		}

		return $html;
	}
}