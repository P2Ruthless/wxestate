<?php

namespace Home\Controller;

use Home\Model\DistrictModel;

class HouseRentController extends HouseController{

	/**
	 * 商铺写字楼
	 * area 区域id
	 * bdType 建筑类型
	 * contnactType 联系人身份
	 * sdType 供求类型
	 * pn 分页
	 */
	public function lists($area='0', $price='0', $room='0', $rentType='0', $pn='1', $sort = ''){
		$category = $this->category();
		$city = get_current_city();

		$map = array();

		$map['hr.city'] = (int)$city['id'];

		if($area != 0){
			$map['_string'] = "hr.area=$area OR hr.busi_area=$area";
		}
		if($price != '0'){
			$priceRange = explode('-', $price);
			if(is_numeric($priceRange[0]) && is_numeric($priceRange[1])){
				$map['hr.price'] = array('BETWEEN', array_map(function($v){return (int)$v;}, $priceRange));
			}elseif(is_numeric($priceRange[0])){
				$map['hr.price'] = array('GT', (int)$priceRange[0]);
			}elseif(is_numeric($priceRange[1])){
				$map['hr.price'] = array('LT', (int)$priceRange[1]);
			}
		}
		if($room != '0'){
			$map['hr.bed_room'] = (int)$room;
		}
		if($rentType != '0'){
			$map['hr.rent_type'] = (int)$rentType;
		}

		$sortField = 'hr.id';
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

		$model = D('HouseRent', 'Logic');

		$totalCount = $model->alias('hr')
			->join('to_document doc on hr.id=doc.id')
			->where($map)
			->count(1);
		$dataList = $model->field('hr.id, doc.title, doc.create_time, hr.thumbnail, hr.price, hr.square, hr.bed_room, hr.live_room, hr.floor, hr.floor_max, hr.decorate, hr.busi_area, area.name area_name, busi_area.name busi_area_name')->alias('hr')
			->join('to_document doc on hr.id=doc.id')
			->join('to_district area on area.id=hr.area', 'LEFT')
			->join('to_district busi_area on hr.busi_area=busi_area.id', 'LEFT')
			->where($map)
			->order("$sortField $sortDir")
			->page($pn, $category['list_row'])
			->select();

		$this->assign('currentNav', 10001);
		$this->assign('dataList', $dataList);
		$this->assign('area', $area);
		$this->assign('price', $price);
		$this->assign('contactType', $contactType);
		$this->assign('room', $room);
		$this->assign('rentType', $rentType);
		$this->assign('totalCount', $totalCount);
		$this->assign('pn', $pn);
		$this->assign('scope', $category['list_row']);

		$this->display();
	}

	public function add(){
		if(IS_POST){
			$res = D('Document')->update();
			if(!$res){
				$this->error(D('Document')->getError());
			}else{
				if($res['id']){//更新房源图片,第一张为缩略图
					$this->updatePictures($res['id'], I('house_pic', '[]'));
				}
				$this->success($res['id']?'更新成功':'新增成功', Cookie('__forward__'));
			}
		}else{
			$cate = $this->category();

			$allowPublish = !empty($cate) && $cate['allow_publish'];
			!$allowPublish && $this->error('该分类不允许发布内容');

			$info = array();
			$info['pid'] = 0;
			$info['model_id'] = 5;
			$info['category_id'] = $cate['id'];
			$info['doc_type'] = 3;
			$info['city'] = get_current_city();

			$this->assign('info', $info);

			$this->display('edit');
		}
	}

	public function update(){

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
			D('HouseRent', 'Logic')->where('id=%d', $houseId)->setField('thumbnail', '/'.$thumbInfo['src']);
		}
	}

	public function view($id){
		
	}

	/* 文档分类检测 */
	private function category(){
		/* 标识正确性检测 */
		//$id = $id ? $id : I('get.category', 0);
		$id = 'house_rent';
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