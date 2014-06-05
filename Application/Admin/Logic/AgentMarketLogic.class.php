<?php
namespace Admin\Logic;

class AgentMarketLogic extends BaseLogic{

	protected $tableName = 'agentmarket';

	protected $_validate = array(
		array('price_avg', 'require', '请填写均价', self::MUST_VALIDATE),
		array('price_avg', 'integer', '均价只能是整数', self::MUST_VALIDATE, 'regex'),
		array('price_avg2', 'require', '请填写公建均价', self::MUST_VALIDATE),
		array('price_avg2', 'integer', '公建均价只能是整数', self::MUST_VALIDATE, 'regex'),
		array('down_payment', 'require', '请填写首付', self::MUST_VALIDATE),
		array('down_payment', 'integer', '首付只能是整数', self::MUST_VALIDATE, 'regex'),
		array('down_payment_max', 'integer', '首付上限只能是整数', self::VALUE_VALIDATE, 'regex'),
		array('monthly', 'require', '请填写月供', self::MUST_VALIDATE),
		array('monthly', 'integer', '月供只能是整数', self::MUST_VALIDATE, 'regex'),
		array('monthly_max', 'integer', '月供上限只能是整数', self::MUST_VALIDATE, 'regex'),
		array('contact_tel', 'require', '请填写电话', self::MUST_VALIDATE),
		array('city', 'require', '请选择城市', self::MUST_VALIDATE),
		array('area', 'require', '请选择区域', self::MUST_VALIDATE),
		array('traffic', 'require', '请填写交通状况', self::MUST_VALIDATE),
		array('property_type', 'require', '请选择物业类型', self::MUST_VALIDATE),
		array('build_type', 'require', '请选择建筑类型', self::MUST_VALIDATE),
		array('open_time', 'require', '请填写开盘时间', self::MUST_VALIDATE),
		array('in_time', 'require', '请填写入住时间', self::MUST_VALIDATE),
		array('property_price', 'double', '请填写物业费', self::MUST_VALIDATE, 'regex'),
		array('room_count', 'require', '请填写户数', self::MUST_VALIDATE),
		array('room_count', 'integer', '请填写户数', self::MUST_VALIDATE, 'regex')
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
				$this->error = '新增房屋买卖信息失败！';
				return false;
			}
		} else { //更新数据
			$status = $this->save($data);
			if(false === $status){
				$this->error = '更新房屋买卖信息失败！';
				return false;
			}
		}

		if($data['id']){
			//处理缩略图
			$this->updateThumbnail($data['id'], I('house_pic', '[]'));
		}

		return true;
	}

	private function updateThumbnail($houseId, $pics){
        $housePicList = json_decode($pics);
        if(empty($housePicList)){
            return;
        }
        
        $picIds = array();
        foreach($housePicList as $pic){
        	if(is_numeric($pic->id)){
        		$picIds[] = $pic->id;
        	}
        }
        if(empty($picIds)){
        	return;
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
        if($thumbPic){
            D('AgentMarket', 'Logic')->where(array('id'=>(int)$houseId))->setField('thumbnail', $thumbPic['path']);
        }
    }
}