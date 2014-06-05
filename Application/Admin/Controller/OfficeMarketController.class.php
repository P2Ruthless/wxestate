<?php
namespace Admin\Controller;

class OfficeMarketController extends ArticleController{
	public function _initialize(){
		parent::_initialize();
		$this->cate_id = 10004;
		$_REQUEST['cate_id'] = $this->cate_id;
	}
	public function index($city = 517, $area = '0', $bdType = '0', $contactType = '0', $sdType = '0', $pn = 1, $sort = ''){
		$this->getMenu();

		$map = array();

		$map['a.city'] = (int)$city;
		$map['b.category_id'] = (int)$this->cate_id;
		$map['b.status'] = array('NEQ', -1);

		if($area != '0'){
			$map['_string'] = "a.area=$area OR a.busi_area=$area";
		}
		if($bdType != '0'){
			$map['a.bd_type'] = (int)$bdType;
		}
		if($contactType != '0'){
			$map['a.contact_type'] = (int)$contactType;
		}
		if($sdType != '0'){
			$map['a.sd_type'] = (int)$sdType;
		}

		$sortField = 'a.id';
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

		$model = D('OfficeMarket', 'Logic');

		$totalCount = $model->alias('a')
			->join('to_document b on a.id=b.id')
			->where($map)
			->count(1);
		$dataList = $model->field('a.id, a.area_sector, b.title
			,a.thumb, a.price
			,a.price_unit, a.square, a.busi_area
			,c.name busi_area_name
			,b.status
			,a.bd_type,a.sd_type,a.contact_type
			,FROM_UNIXTIME(b.create_time) ctime')
			->alias('a')
			->join('to_document b on a.id=b.id')
			->join('to_district c on a.busi_area=c.id', 'LEFT')
			->where($map)
			->order("$sortField $sortDir")
			->page($pn, $scope)
			->select();
		//echo json_encode($dataList); exit;
			
		Cookie('__forward__',$_SERVER['REQUEST_URI']);

		$this->meta_title = '写字楼商铺列表';
		$this->assign('dataList', $dataList);
		$this->assign('city', $city);
		$this->assign('area', $area);
		$this->assign('bdType', $bdType);
		$this->assign('contactType', $contactType);
		$this->assign('sdType', $sdType);
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
		$info['category_id'] = 10004;
		$info['model_id'] = 4;

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

		$officeMarket = M('DocumentOfficemarket')
			->field('*')->alias('om')
			->join('to_document doc on doc.id=om.id')
			->where(array('om.id'=>(int)$id))
			->find();
		//echo json_encode($officeMarket); exit;

		$housePics = M('Picture')->field('id,path')
			->where(array('pid'=>$officeMarket['id']))
			->select();

		if(!empty($housePics)){
			$officeMarket['house_pic'] = json_encode($housePics);
		}else{
			$officeMarket['house_pic'] = '[]';
		}


		if(empty($officeMarket)){
			$this->error('数据不存在');
		}

		$this->meta_title = '商铺写字楼编辑';

		$this->assign('city', 517);

		$this->assign('info', $officeMarket);

		$this->display();
	}

	public function update(){
		if(!IS_POST){
			$this->error('错误的请求类型');
		}

		if(!isset($_POST['comp_register'])){
			$_POST['comp_register'] = 'Y';
		}

		$res = D('Document')->update();
		if(!$res){
			$this->error(D('Document')->getError());
		}else{
			$this->success($res['id']?'更新成功':'新增成功', Cookie('__forward__'));
		}
	}

}