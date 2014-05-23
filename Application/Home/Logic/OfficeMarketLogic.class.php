<?php

namespace Home\Logic;

class OfficeMarketLogic extends BaseLogic{
	public $tableName = 'officemarket';

	protected $_validate = array(
		array('sd_type', '1,2,3,4', '供求类型不正确', self::MUST_VALIDATE, 'in'),
		array('real_estate,sd_type', 'checkRealEstate', '请填写楼盘名称', self::MUST_VALIDATE, 'callback'),
		array('city', 'require', '请选择城市', self::MUST_VALIDATE),
		array('area', 'require', '请选择区域', self::MUST_VALIDATE),
		array('area_sector', 'require', '请填写地段', self::MUST_VALIDATE),
		array('area_sector_nearby', 'require', '请填写地段附近', self::MUST_VALIDATE),
		array('bd_type', '1,2,3', '请正确选择类型', self::MUST_VALIDATE, 'in'),
		array('square', 'integer', '请填写面积，只能是整数', self::MUST_VALIDATE, 'regex'),
		array('square_max,sd_type', 'checkSquareMax', '面积上限只能是整数', self::VALUE_VALIDATE, 'callback'),
		array('price', 'integer', '请填写价格，只能是整数', self::MUST_VALIDATE, 'regex'),
		array('price_max, sd_type', 'checkPriceMax', '价格上限只能是整数', self::VALUE_VALIDATE, 'callback'),
		array('price_unit, sd_type', 'checkPriceUnit', '请选择正确的价格单位', self::MUST_VALIDATE, 'callback'),
		array('contact', 'require', '请填写联系人姓名', self::MUST_VALIDATE),
		array('contact_type', '1,2', '请选择联系人身份', self::MUST_VALIDATE, 'in'),
		array('contact_tel', '/^\d{11}|\d{8}|\d{4}[\s\-]{1}\d{8}$/', '请正确填写联系方式', self::MUST_VALIDATE, 'regex'),
		array('desc_txt', 'require', '请填写描述', self::MUST_VALIDATE)
	);

	public function lists($area=0, $bdType=0, $contactType=0, $sdType=1){
		$model = D('OfficeMarker', 'Logic');

	}

	public function update($id = 0){
		/* 获取文章数据 */
		$data = $this->create();
		if($data === false){
			return false;
		}

		/* 添加或更新数据 */
		if(empty($data['id'])){//新增数据
			$data['id'] = $id;
			$id = $this->add($data);
			if(!$id){
				$this->error = '新增写字楼商铺失败！';
				return false;
			}
		} else { //更新数据
			$status = $this->save($data);
			if(false === $status){
				$this->error = '更新写字楼商铺失败！';
				return false;
			}
		}

		return true;
	}

	public function detail($id){
		$data = parent::detail($id);
		if($data == false){
			return false;
		}

		$PicModel = M('Picture');
		$housePicList = $PicModel->field('path')->where(array('pid'=>$id))->select();
		$data['picList'] = $housePicList;

		return $data;
	}

	/**
	 * 设置where查询条件
	 * @param  number  $category 分类ID
	 * @param  number  $pos      推荐位
	 * @param  integer $status   状态
	 * @return array             查询条件
	 */
	private function listMap($category, $status = 1, $pos = null){
		/* 设置状态 */
		$map = array('b.status' => $status, 'b.pid' => 0);

		/* 设置分类 */
		if(!is_null($category)){
			if(is_numeric($category)){
				$map['b.category_id'] = $category;
			} else {
				$map['b.category_id'] = array('in', str2arr($category));
			}
		}

		$map['b.create_time'] = array('lt', NOW_TIME);
		$map['_string']     = 'deadline = 0 OR deadline > ' . NOW_TIME;

		/* 设置推荐位 */
		if(is_numeric($pos)){
			$map[] = "position & {$pos} = {$pos}";
		}

		return $map;
	}

	protected function checkRealEstate($info){
		if(isset($info['sd_type']) && ($info['sd_type'] == 3 || $info['sd_type'] == 4)){
			return !empty($info['real_estate']);
		}

		return true;
	}

	protected function checkSquareMax($info){
		if(isset($info['sd_type']) && ($info['sd_type'] == 1 || $info['sd_type'] == 2)){
			if(empty($info['square_max'])){
				return true;
			}
			return preg_match('/^[-\+]?\d+$/', $info['square_max']);
		}

		return true;
	}

	protected function checkPriceMax($info){
		if(isset($info['sd_type']) && ($info['sd_type'] == 1 || $info['sd_type'] == 2)){
			if(empty($info['price_max'])){
				return true;
			}
			return preg_match('/^[-\+]?\d+$/', $info['price_max']);
		}

		return true;
	}

	protected function checkPriceUnit($info){
		if($info['sd_type'] == 2 || $info['sd_type'] == 4){
			return $info['price_unit'] == 3;
		}

		return $info['price_unit'] == 1 || $info['price_unit'] == 2;
	}
}