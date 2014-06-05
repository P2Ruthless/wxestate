<?php

namespace Home\Controller;

use Home\Model\DistrictModel;

class OfficeMarketController extends HouseController{

	/**
	 * 商铺写字楼
	 * area 区域id
	 * bdType 建筑类型
	 * contnactType 联系人身份
	 * sdType 供求类型
	 * pn 分页
	 */
	public function lists($area=0, $bdType=0, $contactType=0, $sdType=0, $pn=1){
		$category = $this->category();
		$city = get_current_city();

		$map = array();

		$map['a.city'] = (int)$city['id'];

		if($bdType != 0){
			$map['a.bd_type'] = (int)$bdType;
		}
		if($contactType != 0){
			$map['a.contact_type'] = (int)$contactType;
		}
		if($sdType != 0){
			$map['a.sd_type'] = (int)$sdType;
		}

		$model = D('OfficeMarket', 'Logic');

		$totalCount = $model->alias('a')
			->join('to_document b on a.id=b.id')
			->where($map)
			->count(1);
		$dataList = $model->field('a.id, b.title, a.thumb, a.price, a.price_unit, a.square, a.busi_area, c.name busi_area_name')->alias('a')
			->join('to_document b on a.id=b.id')
			->join('to_district c on a.busi_area=c.id', 'LEFT')
			->where($map)
			->page($pn, $category['list_row'])
			->select();

		$this->assign('dataList', $dataList);
		$this->assign('area', $area);
		$this->assign('bdType', $bdType);
		$this->assign('contactType', $contactType);
		$this->assign('sdType', $sdType);
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
			$info['model_id'] = 4;
			$info['category_id'] = $cate['id'];
			$info['doc_type'] = 3;
			$info['city'] = get_current_city();

			$this->assign('info', $info);

			$this->display();
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
			->setField('pid', $res['id']);
		$thumbPic = $picModel->find($picIds[0]);
		if(empty($thumbPic)){
			return;
		}
		$thumbInfo = getThumbImage($thumbPic['path'], 100, 100, true);
		if($thumbInfo){
			D('OfficeMarket', 'Logic')->where('id=%d', $res['id'])->setField('thumb', '/'.$thumbInfo['src']);
		}
	}

	public function view($id){
		
	}

	/* 文档分类检测 */
	private function category(){
		/* 标识正确性检测 */
		//$id = $id ? $id : I('get.category', 0);
		$id = 'office_market';
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