<?php
namespace Home\Widget;

use Think\Controller;

class SortWidget extends Controller{
	public function showSort($field, $url, $sortString = ''){
		if(empty($field)){
			return;
		}

		if(empty($sortString)){
			$sortString = I('sort');
		}
		
		$sortField = '';
		$sortDir = '';
		$sortInfo = explode('-', $sortString);
		if(isset($sortInfo[0])){
			$sortField = $sortInfo[0];
		}
		if(isset($sortInfo[1])){
			$sortDir = $sortInfo[1];
		}

		$html = '';
		foreach ($field as $key=>$value) {
			$html .= '<a';
			$thisDir = 'desc';
			if($sortField == $key){
				$html .= ' class="active';
				if($sortDir == 'desc'){
					$html .= ' sort-desc';
					$thisDir = 'asc';
				}else{
					$html .= ' sort-asc';
					$thisDir = 'desc';
				}
				$html .= '"';
			}

			$html .= " href=\"".$this->createUrl($url, array('sort'=>"$key-$thisDir"))."\">{$value}</a>";
		}

		return $html;
	}

	private function createUrl($url, $params){
		if(is_array($params)){
			$paramStr = '';
			$i = 0;
			foreach ($params as $key=>$value) {
				if($i++ > 0){
					$paramStr .= '&';
				}
				$paramStr .= "{$key}={$value}";
			}
			$params = $paramStr;
		}
		return $url . (strpos($url, '?') === false ? '?' : '&') . $params;
	}
}