<?php
namespace Home\Widget;

use Think\Controller;

class TagWidget extends Controller{
	public function showInput($name, $value, $count, $split = ','){
		$html = '';
		$valueList = explode(',', $value);
		for($i = 0; $i < $count; $i++){
			$html .= "<input type=\"text\" name=\"{$name}[$i]\" value=\"";
			if(isset($valueList[$i])){
				$html .= $valueList[$i];
			}else{
				$html .= '';
			}
			$html .= '"/>';
		}

		return $html;
	}

	public function showTag($value, $cssClass = ''){
		if(empty($value)){
			return '';
		}

		$colorClass = array('a', 'b', 'c', 'd', 'e');

		$tagList = explode(',', $value);

		$html = '';
		for($i = 0, $count = count($tagList); $i < $count; $i++){
			$html .= '<span class="';
			if(!empty($cssClass)){
				$html .= $cssClass . ' ';
			}
			$color = $colorClass[$i % count($colorClass)];
			$html .= $color;
			$html .= "\">{$tagList[$i]}</span>";
		}
		
		return $html;
	}
}