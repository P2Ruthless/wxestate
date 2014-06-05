<?php
namespace Admin\Logic;

class OfficeMarketLogic extends BaseLogic{
	
	public $tableName = 'officemarket';

	protected $_validate = array(
        array('contact', 'require', '请填写联系人姓名', self::MUST_VALIDATE),
        array('contact_type', '1,2', '请选择联系人身份', self::MUST_VALIDATE, 'in'),
        array('contact_tel', '/^\d{11}|\d{8}|\d{4}[\s\-]{1}\d{8}$/', '请正确填写联系方式', self::MUST_VALIDATE, 'regex'),
        array('city', 'require', '请选择城市', self::MUST_VALIDATE),
        array('area', 'require', '请选择区域', self::MUST_VALIDATE),
        array('busi_area', 'require', '请选择商区', self::MUST_VALIDATE),
        array('real_estate', 'require', '请填写楼盘名称', self::MUST_VALIDATE),
        array('area_sector', 'require', '请填写完整地段名称', self::MUST_VALIDATE),
        array('area_sector_nearby', 'require', '请填写完整地段名称', self::MUST_VALIDATE),
        array('square', 'integer', '请填写建筑面积，只能是整数', self::MUST_VALIDATE, 'regex'),
        array('price', 'integer', '请填写价格，只能是整数', self::MUST_VALIDATE, 'regex')
    );

	public function autoSave($id = 0){

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
                $this->error = '新增写字楼商铺信息失败！';
                return false;
            }
        } else { //更新数据
            $status = $this->save($data);
            if(false === $status){
                $this->error = '更新写字楼商铺信息失败！';
                return false;
            }
        }

        if($data['id']){
            $this->updatePictures($data['id'], I('house_pic', '[]'));
        }

        return true;
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
            D('OfficeMarket', 'Logic')->where('id=%d', $houseId)->setField('thumb', '/'.$thumbInfo['src']);
        }
    }
}