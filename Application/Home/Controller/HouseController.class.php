<?php
namespace Home\Controller;

abstract class HouseController extends HomeController{
	protected function _initialize(){
		parent::_initialize();
		$this->assign('city', get_current_city());
	}
}