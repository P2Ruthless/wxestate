<?php
namespace Home\Controller;

class HouseNewsController extends HomeController{
	public function index(){
		$Document = D('Document');
		$CATEGORY_RECOMMEND = '10009';
		$CATEGORY_BUYWAY = '10010';
		$CATEGORY_NEWSTODAY = '10008';
		$CATEGORY_INTERVIEW = '10011';
		$Categories['recommend'] = $CATEGORY_RECOMMEND;
		$Categories['buyway'] = $CATEGORY_BUYWAY;
		$Categories['newstoday'] = $CATEGORY_NEWSTODAY;
		$Categories['interview'] = $CATEGORY_INTERVIEW;
		$CATEGORY_NEWS_NUM = 7;
		$map['category_id'] = array('eq',$CATEGORY_RECOMMEND);
		$map['level'] = array('gt',0); 
		//专题推荐
		$recommend_topline = $Document->where($map)->order('update_time desc')->find();
		$recommend_news = $Document->where('category_id = '.$CATEGORY_RECOMMEND.' and level = 0')->limit($CATEGORY_NEWS_NUM)->select();
		$this->assign('recommend_topline',$recommend_topline);
		$this->assign('recommend_news',$recommend_news);
		//买房小道
		$buyway_topline = $Document->where('category_id = '.$CATEGORY_BUYWAY.' and level > 0')->order('update_time desc')->find();
		$buyway_news = $Document->where('category_id = '.$CATEGORY_BUYWAY.' and level = 0')->limit($CATEGORY_NEWS_NUM)->select();
		$this->assign('buyway_topline',$buyway_topline);
		$this->assign('buyway_news',$buyway_news);
		//今日新闻
		$newstoday_topline = $Document->where('category_id = '.$CATEGORY_NEWSTODAY.' and level > 0')->order('update_time desc')->find();
		$newstoday_news = $Document->where('category_id = '.$CATEGORY_NEWSTODAY.' and level = 0')->limit($CATEGORY_NEWS_NUM)->select();
		$this->assign('newstoday_topline',$newstoday_topline);
		$this->assign('newstoday_news',$newstoday_news);
		//访谈活动
		$interview_topline = $Document->where('category_id = '.$CATEGORY_INTERVIEW.' and level > 0')->order('update_time desc')->find();
		$interview_news = $Document->where('category_id = '.$CATEGORY_INTERVIEW.' and level = 0')->limit($CATEGORY_NEWS_NUM)->select();
		$this->assign('interview_topline',$interview_topline);
		$this->assign('interview_news',$interview_news);
		$this->assign('cates',$Categories);
		$this->display();
	}

	public function lists($category_id){
		$Document = D('Document');
		$Category = D('Category');
		$cate = $Category->find($category_id);
		$lists = $Document->where('category_id = '.$category_id)->order('create_time desc')->select();
		
		$this->assign('cate',$cate);
		$this->assign('lists',$lists);
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