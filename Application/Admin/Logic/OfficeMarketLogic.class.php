<?php
namespace Admin\Logic;

class OfficeMarketLogic extends BaseLogic{
	protected $tableName = 'officemarket';

	public function detail($id){
		$data = $this->field(true)->find($id);
		if(!$data){
			$this->error = '获取详细信息失败';
			return false;
		}
		return $data;
	}

	public function autoSave($id = 0){

	}

	public function update($id = 0){

	}
}