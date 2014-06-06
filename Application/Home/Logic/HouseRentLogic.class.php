<?php
namespace Home\Logic;

class HouseRentLogic extends BaseLogic{
	public $tableName = 'houserent';

	protected $_validate = array(
		array('rent_type', '1,2,3', '出租类型不正确', self::MUST_VALIDATE, 'in'),
		array('contact', 'require', '请填写联系人姓名', self::MUST_VALIDATE),
		array('contact_type', '1,2', '请选择联系人身份', self::MUST_VALIDATE, 'in'),
		array('contact_tel', '/^\d{11}|\d{8}|\d{4}[\s\-]{1}\d{8}$/', '请正确填写联系方式', self::MUST_VALIDATE, 'regex'),
		array('price', 'integer', '请填写价格，只能是整数', self::MUST_VALIDATE, 'regex'),
		array('city', 'require', '请选择城市', self::MUST_VALIDATE),
		array('area', 'require', '请选择区域', self::MUST_VALIDATE),
		array('community', 'require', '请填写小区名称', self::MUST_VALIDATE),
		array('bed_room, rent_type', 'checkBedRoom', '请填写卧室数量', self::MUST_VALIDATE, 'callback'),
		array('live_room, rent_type', 'checkLiveRoom', '请填写客厅数量', self::MUST_VALIDATE, 'callback'),
		array('toilet, rent_type', 'checkToilet', '请填写卫生间数量', self::MUST_VALIDATE, 'callback'),
		array('square, rent_type', 'checkSquare', '请填写面积，只能是整数', self::MUST_VALIDATE, 'callback'),
		array('price', 'integer', '请填写价格，只能是整数', self::MUST_VALIDATE, 'regex'),
		array('desc_txt', 'require', '请填写描述', self::MUST_VALIDATE)
	);

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
				$this->error = '新增房屋租赁信息失败！';
				return false;
			}
		} else { //更新数据
			$status = $this->save($data);
			if(false === $status){
				$this->error = '更新房屋租赁信息失败！';
				return false;
			}
		}

		return true;
	}

	/**
	 * 获取首页的
	 */
	public function listsForIndex($city, $count = 4){
		return $this->field('hr.id,hr.thumbnail,hr.bed_room,hr.live_room,hr.decorate,hr.price,area.name area_name,busi_area.name busi_area_name')
			->alias('hr')
			->join('to_document doc on hr.id=doc.id')
			->join('to_district area on hr.area=area.id', 'LEFT')
			->join('to_district busi_area on hr.busi_area=busi_area.id', 'LEFT')
			->where(array(
				'doc.status'=>1,
				'doc.category_id'=>10001,
				'doc.create_time'=>array('lt', NOW_TIME),
				'deadline=0 OR deadline>'.NOW_TIME,
				'doc.position & 4 = 4',
				'hr.city'=> is_array($city) ? $city['id'] : $city
			))
			->order('hr.id desc')
			->limit($count)
			->select();
	}

	protected function checkBedRoom($data){
		if($data['rent_type'] == 1){
			return !empty($data['bed_room']);
		}
		return true;
	}

	protected function checkLiveRoom($data){
		if($data['rent_type'] == 1){
			return !empty($data['live_room']);
		}
		return true;
	}

	protected function checkToilet($data){
		if($data['rent_type'] == 1){
			return !empty($data['toilet']);
		}
		return true;
	}

	protected function checkSquare($data){
		if($data['rent_type'] == 3){
			return true;
		}
		return preg_match('/^\d+$/', $data['square']);
	}

	public function detail($id){
		$data = parent::detail($id);
		if($data == false){
			return false;
		}

		//图片
		$PicModel = M('Picture');
		$housePicList = $PicModel->field('path')->where(array('pid'=>$id))->select();
		$data['picList'] = $housePicList;

		//附近推荐
		$HouseRent = M('DocumentHouserent');
		$nearbyList = $HouseRent->alias('hr')
			->field('doc.id,doc.title,hr.price,hr.contact_tel,hr.thumbnail,area.name area_name,busi_area.name busi_area_name')
			->join('__DOCUMENT__ doc on doc.id=hr.id')
			->join('__DISTRICT__ area on area.id=hr.area', 'LEFT')
			->join('__DISTRICT__ busi_area on busi_area.id=hr.busi_area', 'LEFT')
			->where(array(
				'doc.status'=>1,
				'doc.category_id'=>10001,
				'doc.create_time'=>array('lt', NOW_TIME),
				'doc.deadline=0 OR doc.deadline>'.NOW_TIME,
				'hr.busi_area'=>(int)$data['busi_area'],
				'doc.id'=>array('NEQ', (int)$data['id'])
			))
			->order('id desc')
			->limit(3)
			->select();

		$data['nearbyList'] = $nearbyList;

		return $data;
	}
}