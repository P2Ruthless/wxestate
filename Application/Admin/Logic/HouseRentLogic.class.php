<?php
namespace Admin\Logic;

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

		if($data['id']){
			$this->updatePictures($data['id'], I('house_pic', '[]'));
		}

		return true;
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

	public function autoSave($id = 0){

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
        $thumbInfo = getThumbImage($thumbPic['path'], C('HOUSE_PIC_DIMEN.THUMB_WIDTH'), C('HOUSE_PIC_DIMEN.THUMB_HEIGHT'), true);
        if($thumbInfo){
            D('HouseRent', 'Logic')->where('id=%d', $houseId)->setField('thumbnail', '/'.$thumbInfo['src']);
        }
    }
}