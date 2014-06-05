<?php
namespace Admin\Controller;

class AgentMarketController extends ArticleController{
	protected $cate_id = 10005;
	public function _initialize(){
		parent::_initialize();
		$_REQUEST['cate_id'] = $this->cate_id;
	}

	public function add(){
		$this->getMenu();

		$info = array();
		$info['pid'] = 0;
		$info['category_id'] = $this->cate_id;
		$info['model_id'] = 8;

		$this->meta_title = '代理楼盘添加';

		$this->assign('info', $info);
		$this->assign('city', 517);

		$this->display('edit');
	}

	public function edit($id){
		$id = I('id', '0');
		if(empty($id)){
			$this->error('参数不正确');
		}

		$this->getMenu();

		$agentMarket = M('DocumentAgentmarket')->alias('am')
			->field('*')
			->join('to_document doc on doc.id=am.id')
			->where(array('am.id'=>(int)$id))
			->find();

		if(!empty($agentMarket['thumbnail'])){
			$agentMarket['house_pic'] = json_encode(array(array('id'=>'unknown', 'path'=>$agentMarket['thumbnail'])));
		}else{
			$agentMarket['house_pic'] = '[]';
		}


		if(empty($agentMarket)){
			$this->error('数据不存在');
		}

		$this->meta_title = '代理楼盘编辑';

		$this->assign('city', 517);

		$this->assign('info', $agentMarket);

		$this->display();
	}

	public function update(){
		if(!IS_POST){
			$this->error('错误的请求类型');
		}

		if(isset($_POST['feature']) && is_array($_POST['feature'])){
			$_POST['feature'] = array2string($_POST['feature']);
		}

		$res = D('Document')->update();
		if(!$res){
			$this->error(D('Document')->getError());
		}else{
			$this->success($res['id']?'更新成功':'新增成功', Cookie('__forward__'));
		}
	}

	public function index(){
		$this->getMenu();

		$AgentMarket = D('AgentMarket', 'Logic');
		$dataList = $AgentMarket->alias('am')
			->field('doc.id,doc.title,doc.status')
			->join('to_document doc on doc.id=am.id')
			->join('to_district area on area.id=am.area', 'LEFT')
			->join('to_district busi_area on busi_area.id=am.busi_area', 'LEFT')
			->where(array('status'=>array('NEQ', -1)))
			->select();

		$this->assign('dataList', $dataList);

		$allow = get_category($this->cate_id, 'allow_publish');

		Cookie('__forward__',$_SERVER['REQUEST_URI']);

		$this->meta_title = '代理楼盘列表';
		$this->assign('allow', $allow);

		$this->display();
	}

	public function editPics($id, $type){
		$this->getMenu();

		$agentMarket = M('DocumentAgentmarket')->alias('am')
			->field('doc.id,doc.title')
			->join('to_document doc on doc.id=am.id')
			->where(array('am.id'=>$id))
			->find();

		if(empty($agentMarket)){
			$this->error('记录不存在');
		}

		$AmPics = M('AgentmarketPics');

		$amPicList = $AmPics->alias('ampics')
			->field('ampics.desc_txt,ampics.id,pic.path')
			->join('__PICTURE__ pic on ampics.id=pic.id')
			->where(array(
				'ampics.busi_type'=>(int)$type,
				'pic.pid'=>(int)$id
			))
			->select();


		$this->meta_title = $agentMarket['title'];

		$this->assign('agentMarker', $agentMarket);
		$this->assign('type', $type);
		$this->assign('amPicList', $amPicList);

		$this->display();
	}

	public function updatePics(){
		if(!IS_POST){
			$this->error('错误的请求类型');
		}

		//代理楼盘的ID
		$amId = I('post.amid');
		//图片类型 户型图 规划图 ...
		$type = I('post.type');

		$picIds = I('post.picId');
		$picDesc = I('post.picDesc');

		$Pic = M('Picture');

		$AgentMarketPics = M('AgentmarketPics');

		$oldPics = $AgentMarketPics->alias('amp')
			->field('amp.id')
			->join('__PICTURE__ pic on amp.id=pic.id')
			->where(array('pic.pid'=>(int)$amId, 'amp.busi_type'=>(int)$type))
			->select();

		if(!empty($oldPics)){
			$AgentMarketPics
				->where(array('id'=>array('in', array_map(function($v){return (int)$v['id'];}, $oldPics))))
				->delete();
		}

		if(!empty($picIds)){
			for($i = 0, $count = count($picIds); $i < $count; $i++){
				$itemData = array();
				$itemData['id'] = (int)$picIds[$i];
				$itemData['busi_type'] = (int)$type;
				if(isset($picDesc[$i])){
					$itemData['desc_txt'] = $picDesc[$i];
				}
				//添加图片
				$AgentMarketPics->add($itemData);
				//更新Picture表
				$Pic->where(array('id'=>(int)$picIds[$i]))->setField('pid', $amId);
			}
		}

		$this->success('保存成功', Cookie('__forward__'));
	}
}