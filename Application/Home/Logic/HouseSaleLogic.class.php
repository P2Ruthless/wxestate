<?php
namespace Home\Logic;

class HouseSaleLogic extends BaseLogic{
	protected $_validate = array(
		array('contact', 'require', '请填写联系人姓名', self::MUST_VALIDATE),
		array('contact_type', '1,2', '请选择联系人身份', self::MUST_VALIDATE, 'in'),
		array('contact_tel', '/^\d{11}|\d{8}|\d{4}[\s\-]{1}\d{8}$/', '请正确填写联系方式', self::MUST_VALIDATE, 'regex'),
		array('price', 'integer', '请填写价格，只能是整数', self::MUST_VALIDATE, 'regex'),
		array('city', 'require', '请选择城市', self::MUST_VALIDATE),
		array('area', 'require', '请选择区域', self::MUST_VALIDATE),
		array('busi_area', 'require', '请选择商区', self::MUST_VALIDATE),
		array('community', 'require', '请填写小区名称', self::MUST_VALIDATE),
		array('bed_room', 'integer', '请填写卧室数量', self::MUST_VALIDATE, 'regex'),
		array('live_room', 'integer', '请填写客厅数量', self::MUST_VALIDATE, 'regex'),
		array('toilet', 'integer', '请填写卫生间数量', self::MUST_VALIDATE, 'regex'),
		array('floor', 'integer', '请填写楼层', self::MUST_VALIDATE, 'regex'),
		array('floor_max', 'integer', '请填写最高楼层', self::MUST_VALIDATE, 'regex'),
		array('build_type', 'require', '请填写建筑类型', self::MUST_VALIDATE),
		array('structure', 'require', '请填写建筑结构', self::MUST_VALIDATE),
		array('decorate', 'require', '请填写装修类型', self::MUST_VALIDATE),
		array('face', 'require', '请选择朝向', self::MUST_VALIDATE),
		array('square', 'integer', '请填写建筑面积，只能是整数', self::MUST_VALIDATE, 'regex'),
		array('inner_square', 'integer', '请填写套内面积', self::MUST_VALIDATE, 'regex'),
		array('price', 'integer', '请填写价格，只能是整数', self::MUST_VALIDATE, 'regex'),
		array('desc_txt', 'require', '请填写描述', self::MUST_VALIDATE),
		array('down_payment, loan_enable', 'checkDownPayment', '请填写首付', 'callback'),
		array('monthly, loan_enable', 'checkMonthly', '请填写月供', self::MUST_VALIDATE, 'callback'),
		array('property_right', 'require', '请填写产权', self::MUST_VALIDATE),
		array('property_type', 'require', '请选择产权类型', self::MUST_VALIDATE),
		array('build_year', 'integer', '请填建筑年代', self::MUST_VALIDATE)
	);

	protected function checkDownPayment($data){
		if($data['loan_enable'] == 'Y'){
			return !empty($data['down_payment']);
		}
		return true;
	}

	protected function checkMonthly($data){
		if($data['loan_enable'] == 'Y'){
			return !empty($data['monthly']);
		}

		return true;
	}
}