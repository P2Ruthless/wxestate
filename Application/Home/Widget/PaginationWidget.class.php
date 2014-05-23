<?php
namespace Home\Widget;

use Think\Controller;

class PaginationWidget extends Controller{
	public function showPagination($url, $pn=1, $totalCount=-1, $scope=20, $maxShow=10){
		if($totalCount == 0){
			return '';
		}
		$html = '<ul class="';
		if($totalCount < 0){
			//无具体页数的分页
			$html .= 'pager">';
			$html .= '<li><a href="#">上一页</a></li>';
			$html .= '<li><a href="#">下一页</a></li>';
		}else{
			//具体页数的分页
			$html .='pagination">';
			$pageCount = $totalCount % $scope == 0 ? ($totalCount / $scope) : ($totalCount / $scope + 1);
			for($i = 1; $i <= $pageCount; $i++){
				$href = str_replace('[pn]', $pn, $url);
				$html .= "<li><a href='$href'>$i</a></li>";
			}
		}
		$html .= '</ul>';

		return $html;
	}
}