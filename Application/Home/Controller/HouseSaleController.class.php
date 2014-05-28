<?php
namespace Home\Controller;

class HouseSaleController extends HomeController{
	public function add(){
		if(!is_login()){
			cookie('__return_url__', U('HouseSale/add'));
			$this->redirect('User/login');
		}

		if(IS_POST){

		}else{
			$this->display('edit');
		}
	}
}