<?php

namespace Admin\Controller;

class WeixinController extends AdminController{
	public function account(){
		$WxAccount = D('WxAccount');
		$accountList = $WxAccount->field(true)->select();

		$this->meta_title = '微信账户管理';
		$this->assign('accountList', $accountList);
		$this->display();
	}

	public function addAccount(){
		if(IS_POST){
			$WxAccount = D('WxAccount');
			$account = $WxAccount->create();
			if($account){
				$WxAccount->add();
				$this->success('添加成功', U('Weixin/account'));
			}else{
				$this->error($WxAccount->getError());
			}
		}else{
			$this->display('editAccount');
		}
	}

	public function editAccount($id){
		$WxAccount = D('WxAccount');

		if(IS_POST){
			$account = $WxAccount->create();
			if($account){
				$WxAccount->save();
				$this->success('更新成功', U('Weixin/account'));
			}else{
				$this->error($WxAccount->getError());
			}
		}else{
			$account = $WxAccount->find($id);

			if(empty($account)){
				$this->error('记录不存在');
			}

			$this->assign('account', $account);

			$this->display();
		}
	}

	public function inactiveAccount($id){
		$WxAccount = D('WxAccount');
		$account = $WxAccount->find($id);
		if(empty($account)){
			$this->error('记录不存在');
		}

		$WxAccount->where(array('id'=>$id))->setField('status', 0);

		$this->success('操作成功', U('Weixin/account'));
	}

	public function enableAccount($id){
		$WxAccount = D('WxAccount');
		$account = $WxAccount->find($id);
		if(empty($account)){
			$this->error('记录不存在');
		}

		$WxAccount->where(array('id'=>$id))->setField('status', 1);

		$this->success('操作成功', U('Weixin/account'));
	}

	public function deleteAccount($id){
		$WxAccount = D('WxAccount');
		$WxAccount->delete($id);

		$this->success('删除成功', U('Weixin/account'));
	}

	public function accountMenu($account = '0'){
		$WxAccount = D('WxAccount');
		$accountList = $WxAccount->field('id,desc_txt')->select();
		if(empty($accountList)){
			$this->error('请先添加公众号', U('Weixin/account'));
		}

		if(empty($account)){
			$account = $accountList[0];
		}else{
			foreach($accountList as $wa){
				if($wa['id'] == $account){
					$account = $wa;
					break;
				}
			}
		}

		$WxMenu = D('WxMenu');
		$dataList = $WxMenu->field(true)->where(array('account_id'=>$account['id']))->select();

		$dataList = list_to_tree($dataList, 'id', 'pid', '_child', '-1');

		$this->assign('account', $account);
		$this->assign('accountList', $accountList);
		$this->assign('dataList', $dataList);

		$this->display();
	}

	//Weixin menu add
	public function addMenu($account = '0'){
		if(IS_POST){
			if($_POST['pid'] == -1){
				$_POST['type'] = 'parent';
			}
			if ($_POST['type'] == 'parent') {
				$_POST['level'] = 1;
			}else{
				$_POST['level'] = 2;
			}
			$WxMenu = D('WxMenu');
			$menu = $WxMenu->create();
			if($menu){
				$WxMenu->add();
				$this->success('添加成功', U('Weixin/accountMenu'));
			}else{
				$this->error($WxMenu->getError());
			}
		}else{
			$this->assign('account_id',$account);
			$wxmenus = M('WxMenu')->field(true)->where('pid = -1')->select();
            $this->assign('WxMenus', $wxmenus);
			$this->display('editMenu');
		}	
	}
	//weixin menu edit
	public function editMenu($id,$account = '0'){
		$WxMenu = D('WxMenu');

		if(IS_POST){
			$menu = $WxMenu->create();
			if($menu){
				$WxMenu->save();
				$this->success('更新成功', U('Weixin/accountMenu'));
			}else{
				$this->error($WxMenu->getError());
			}
		}else{
			$menu = $WxMenu->find($id);

			if(empty($menu)){
				$this->error('记录不存在');
			}
			$this->assign('wxmenu', $menu);
			$this->assign('account_id',$account);
			$wxmenus = M('WxMenu')->field(true)->where('pid = -1')->select();
            $this->assign('WxMenus', $wxmenus);
			$this->display();
		}
	}
	//weixin menu delete
	public function deleteMenu($id,$level='2'){
		$WxMenu = D('WxMenu');
		if($level == 1){
			$WxMenu->where('pid = '.$id)->delete();
			$WxMenu->delete($id);
		}else{
			$WxMenu->delete($id);
		}
		$this->success('删除成功', U('Weixin/accountMenu'));
	}
}