<?php
namespace Admin\Model;

use Think\Model;

class WxAccountModel extends Model{
	protected $_validate = array(
		array('id', 'require', '请填写微信号', self::MUST_VALIDATE),
		array('desc_txt', 'require', '请填写备注', self::MUST_VALIDATE),
		array('app_id', 'require', '请填写AppID', self::MUST_VALIDATE),
		array('app_secret', 'require', '请填写AppSecret', self::MUST_VALIDATE),
		array('valid_token', 'require', '请填写接入token', self::MUST_VALIDATE)
	);


}