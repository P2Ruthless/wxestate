<?php
namespace Home\Controller;

class HouseSaleController extends HouseController{

	public function lists($area = '0', $price = '0', $square = '0', $room = '0', $pn = '1', $sort = ''){
		$category = $this->category();
		$city = get_current_city();

		$map = array();

		$map['hs.city'] = (int)$city['id'];

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
		if($room != 0){
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

		$model = D('HouseSale', 'Logic');

		$totalCount = $model->alias('hs')
			->join('to_document doc on hs.id=doc.id')
			->where($map)
			->count(1);
		$dataList = $model->field('hs.id, doc.title,doc.create_time, hs.community,hs.bed_room,hs.live_room,hs.floor,hs.floor_max,hs.decorate,hs.structure,hs.build_year,hs.face,hs.thumbnail, hs.price, hs.square, hs.busi_area, busi_area.name busi_area_name')
			->alias('hs')
			->join('to_document doc on doc.id=hs.id')
			->join('to_district busi_area on hs.busi_area=busi_area.id', 'LEFT')
			->where($map)
			->order("$sortField $sortDir")
			->page($pn, $category['list_row'])
			->select();

		$this->assign('currentNav', 10002);
		$this->assign('dataList', $dataList);
		$this->assign('area', $area);
		$this->assign('price', $price);
		$this->assign('square', $square);
		$this->assign('room', $room);
		$this->assign('totalCount', $totalCount);
		$this->assign('pn', $pn);
		$this->assign('scope', $category['list_row']);

		$this->display();
	}

	public function add(){
		if(!is_login()){
			cookie('__return_url__', U('HouseSale/add'));
			$this->redirect('User/login');
		}
		$cate = $this->category();

		$allowPublish = !empty($cate) && $cate['allow_publish'];
		!$allowPublish && $this->error('该分类不允许发布内容');

		$info = array();
		$info['pid'] = 0;
		$info['model_id'] = 6;
		$info['category_id'] = $cate['id'];
		$info['doc_type'] = 3;
		$info['city'] = get_current_city();

		$this->assign('data', $info);

		$this->display('edit');
	}

	public function update(){
		if(!is_login()){
			$this->error('请先登录');
		}
		if(!IS_POST){
			$this->error('错误的请求类型');
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
			$this->success($res['id']?'更新成功':'新增成功', U('Index/index'));
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

	/* 文档分类检测 */
	private function category(){
		/* 标识正确性检测 */
		//$id = $id ? $id : I('get.category', 0);
		$id = 'house_sale';
		if(empty($id)){
			$this->error('没有指定文档分类！');
		}

		/* 获取分类信息 */
		$category = D('Category')->info($id);
		if($category && 1 == $category['status']){
			switch ($category['display']) {
				case 0:
					$this->error('该分类禁止显示！');
					break;
				//TODO: 更多分类显示状态判断
				default:
					return $category;
			}
		} else {
			$this->error('分类不存在或被禁用！');
		}
	}
}