<?php
namespace Admin\Controller;

class HouseSaleController extends ArticleController{
	public function _initialize(){
		parent::_initialize();
		$this->cate_id = 10002;
		$_REQUEST['cate_id'] = $this->cate_id;
	}
	public function index($city = 517, $area = '0', $price = '0', $square = '0', $room = '0', $pn = 1, $sort = ''){
		$this->getMenu();

		$map = array();

		$map['hs.city'] = (int)$city;
		$map['doc.category_id'] = (int)$this->cate_id;
		$map['doc.status'] = array('NEQ', -1);

		if($area != '0'){
			$map['_string'] = "hs.area=$area OR hs.busi_area=$area";
		}
		if($price != '0'){
			$priceRange = explode('-', $price);
			if(is_numeric($priceRange[0]) && is_numeric($priceRange[1])){
				$map['hs.price'] = array('BETWEEN', array_map(function($v){return (int)$v;}, $priceRange));
			}elseif(is_numeric($priceRange[0])){
				$map['hs.price'] = array('GT', (int)$priceRange[0]);
			}elseif(is_numeric($priceRange[1])){
				$map['hs.price'] = array('LT', (int)$priceRange[1]);
			}
		}
		if($square != '0'){
			$squareRange = explode('-', $square);
			if(is_numeric($squareRange[0]) && is_numeric($squareRange[1])){
				$map['hs.square'] = array('BETWEEN', array_map(function($v){return (int)$v;}, $squareRange));
			}elseif(is_numeric($squareRange[0])){
				$map['hs.square'] = array('GT', (int)$squareRange[0]);
			}elseif(is_numeric($squareRange[1])){
				$map['hs.square'] = array('LT', (int)$squareRange[1]);
			}
		}
		if($room != '0'){
			$map['hs.bed_room'] = (int)$room;
		}

		$sortField = 'hs.id';
		$sortDir = 'desc';
		if(!empty($sort)){
			$sortInfo = explode('-', $sort);
			if(isset($sortInfo[0])){
				$sortField = $sortInfo[0];
			}
			if(isset($sortInfo[1])){
				$sortDir = $sortInfo[1];
			}
		}

		$scope = get_category($this->cate_id, 'list_row');

		$model = D('HouseSale', 'Logic');

		$totalCount = $model->alias('hs')
			->join('to_document doc on hs.id=doc.id')
			->where($map)
			->count(1);
		$dataList = $model->field('hs.id,doc.title,doc.create_time,doc.status,hs.community,hs.bed_room,hs.live_room, hs.price, hs.square, area.name area_name, busi_area.name busi_area_name')
			->alias('hs')
			->join('to_document doc on doc.id=hs.id')
			->join('to_district busi_area on hs.busi_area=busi_area.id', 'LEFT')
			->join('to_district area on hs.area=area.id', 'LEFT')
			->where($map)
			->order("$sortField $sortDir")
			->page($pn, $scope)
			->select();

		Cookie('__forward__',$_SERVER['REQUEST_URI']);

		$this->meta_title = '房屋买卖列表';
		$this->assign('dataList', $dataList);
		$this->assign('city', $city);
		$this->assign('area', $area);
		$this->assign('price', $price);
		$this->assign('square', $square);
		$this->assign('room', $room);
		$this->assign('totalCount', $totalCount);
		$this->assign('pn', $pn);
		$this->assign('scope', $scope);
		$this->assign('allow', get_category($this->cate_id, 'allow_publish'));

		$this->display();
	}

	public function add(){
		$this->getMenu();

		$info = array();
		$info['pid'] = 0;
		$info['category_id'] = 10002;
		$info['model_id'] = 6;

		$this->assign('info', $info);
		$this->assign('city', 517);

		$this->display('edit');
	}

	public function edit(){
		$id = I('id', '0');
		if(empty($id)){
			$this->error('参数不正确');
		}

		$this->getMenu();

		$houseSale = M('DocumentHousesale')->field('*')->alias('hs')
			->join('to_document doc on doc.id=hs.id')
			->where(array('hs.id'=>(int)$id))
			->find();

		$housePics = M('Picture')->field('id,path')
			->where(array('pid'=>$houseSale['id']))
			->select();

		if(!empty($housePics)){
			$houseSale['house_pic'] = json_encode($housePics);
		}else{
			$houseSale['house_pic'] = '[]';
		}


		if(empty($houseSale)){
			$this->error('数据不存在');
		}

		$this->meta_title = '房屋买卖编辑';

		$this->assign('city', 517);

		$this->assign('info', $houseSale);

		$this->display();
	}

	public function update(){
		if(!IS_POST){
			$this->error('错误的请求类型');
		}

		if(isset($_POST['feature']) && is_array($_POST['feature'])){
			$_POST['feature'] = array2string($_POST['feature']);
		}
		if(isset($_POST['add_on']) && is_array($_POST['add_on'])){
			$_POST['add_on'] = array2string($_POST['add_on']);
		}

		if(!isset($_POST['loan_enable'])){
			$_POST['loan_enable'] = 'Y';
		}

		$res = D('Document')->update();
		if(!$res){
			$this->error(D('Document')->getError());
		}else{
			if($res['id']){//更新房源图片,第一张为缩略图
				$this->updatePictures($res['id'], I('house_pic', '[]'));
			}
			$this->success($res['id']?'更新成功':'新增成功', Cookie('__forward__'));
		}
	}

	private function updatePictures($houseId, $pics){
		$housePicList = json_decode($pics);
		if(empty($housePicList)){
			return;
		}
		
		$picIds = array();
		foreach($housePicList as $pic){
			$picIds[] = $pic->id;
		}
		$picModel = M('Picture');
		$picModel->where(array(
			'id'=>array('IN', $picIds),
			'uid'=>array('EQ', is_login())
			))
			->setField('pid', $houseId);
		$thumbPic = $picModel->find($picIds[0]);
		if(empty($thumbPic)){
			return;
		}
		$thumbInfo = getThumbImage($thumbPic['path'], 100, 100, true);
		if($thumbInfo){
			D('HouseSale', 'Logic')->where('id=%d', $houseId)->setField('thumbnail', '/'.$thumbInfo['src']);
		}
	}
}