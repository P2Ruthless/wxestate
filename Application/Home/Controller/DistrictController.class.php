<?php
namespace Home\Controller;

use Home\Model\DistrictModel;

class DistrictController extends HomeController{
	public function switchCity(){

		$cityList = DistrictModel::getTree(1,1);

		$this->assign('cityList', $cityList);

		$this->display('Index:switchCity');
	}
}