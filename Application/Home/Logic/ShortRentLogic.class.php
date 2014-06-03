<?php
namespace Home\Logic;

class ShortRentLogic extends BaseLogic{
	public $tableName = 'shortrent';

	protected $_validate = array(
		array('contact', 'require', '请填写联系人', self::MUST_VALIDATE),
		array('contact_type', '1,2', '请选择联系人身份', self::MUST_VALIDATE),
		array('contact_tel', '/^\d{11}|\d{8}|\d{4}[\s\-]{1}\d{8}$/', '请正确填写联系方式', self::MUST_VALIDATE, 'regex'),
		array('price', 'integer', '请填写价格，只能是整数', self::MUST_VALIDATE, 'regex'),
		array('city', 'require', '请选择城市', self::MUST_VALIDATE),
		array('area', 'require', '请选择区域', self::MUST_VALIDATE),
		array('busi_area', 'require', '请选择商区', self::MUST_VALIDATE),
		array('loc_txt', 'require', '请填写位置', self::MUST_VALIDATE),
		array('loc_nearby', 'require', '请填写附近', self::MUST_VALIDATE),
		array('type', 'require', '请选择类型', self::MUST_VALIDATE),
		array('price_unit', '请选择价格单位', self::MUST_VALIDATE),
		array('desc_txt', '请填写描述信息', self::MUST_VALIDATE)
	);
}