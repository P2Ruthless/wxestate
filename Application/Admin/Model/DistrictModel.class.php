<?php

namespace Admin\Model;

use Think\Model;

class DistrictModel extends Model{
    protected $_validate = array(
        array('pid', 'require', '上级区域不能为空', self::MUST_VALIDATE),
        array('name', 'require', '请填写区域名称', self::MUST_VALIDATE),
        array('inactive', 'Y,N', '是否失效不正确', self::MUST_VALIDATE, 'in'),
        array('type', 'require', '类型不能为空', self::MUST_VALIDATE)
    );
}