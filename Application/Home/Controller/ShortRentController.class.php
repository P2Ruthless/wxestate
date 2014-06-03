<?php
namespace Home\Controller;

class ShortRentController extends HouseController{
	public function lists($area = '0', $type = '0', $price = '0', $pn = '1'){
		$category = $this->category();
		$city = get_current_city();

		$map = array();

		if($area != '0'){
			$map['_string'] = "sr.area=$area OR sr.busi_area=$area";
		}

		if($type != '0'){
			$map['sr.type'] = (int)$type;
		}

		if($price != '0'){
			$priceRange = explode('-', $price);
			if(is_numeric($priceRange[0]) && is_numeric($priceRange[1])){
				$map['sr.price'] = array('BETWEEN', array_map(function($v){return (int)$v;}, $priceRange));
			}elseif(is_numeric($priceRange[0])){
				$map['sr.price'] = array('GT', (int)$priceRange[0]);
			}elseif(is_numeric($priceRange[1])){
				$map['sr.price'] = array('LT', (int)$priceRange[1]);
			}
		}

		$model = D('ShortRent', 'Logic');

		$totalCount = $model->alias('sr')
			->join('to_document doc on sr.id=doc.id')
			->where($map)
			->count(1);

		$dataList = $model->field('sr.id,doc.title,doc.create_time,area.name area_name,busi_area.name busi_area_name,sr.price,sr.price_unit,sr.type,sr.min_limit,sr.loc_txt,sr.loc_nearby,sr.thumbnail')->alias('sr')
			->join('to_document doc on sr.id=doc.id')
			->join('to_district area on sr.area=area.id', 'LEFT')
			->join('to_district busi_area on sr.busi_area=busi_area.id', 'LEFT')
			->where($map)
			->page($pn, $category['list_row'])
			->select();

		$this->assign('currentNav', 10003);
		$this->assign('dataList', $dataList);
		$this->assign('area', $area);
		$this->assign('type', $type);
		$this->assign('price', $price);
		$this->assign('totalCount', $totalCount);
		$this->assign('pn', $pn);
		$this->assign('scope', $category['list_row']);

		$this->display();
	}

	/* 文档分类检测 */
	private function category(){
		/* 标识正确性检测 */
		//$id = $id ? $id : I('get.category', 0);
		$id = 'short_rent';
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