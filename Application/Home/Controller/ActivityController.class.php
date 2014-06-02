<?php
namespace Home\Controller;

class ActivityController extends HomeController{
	public function index($category_id = 10007){
		$Document = D('Document');
		$Category = D('Category');
		$cate = $Category->find($category_id);
		$count = $Document->where('category_id = '.$category_id)->count();
		$Page = new \Think\Page($count,25);
		$lists = $Document
			     ->where('category_id = '.$category_id)
				 ->order('create_time desc')
				 ->limit($Page->firstRow.','.$Page->listRows)
				 ->select();
		$show = $Page->show();
		$this->assign('cate',$cate);
		$this->assign('lists',$lists);
		$this->assign('page',$show);
		$this->display();
	}

	//详细页面
	public function detail($document_id,$category_title){
		$Document = D('Document');
		$news = $Document->detail($document_id);
		$this->assign('news',$news);
		$this->assign('category_title',$category_title);
		$this->display();
	}
}