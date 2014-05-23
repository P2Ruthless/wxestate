<?php
namespace Common\Model;

use Think\Model;

class LookupModel extends Model{

	private static $_cache = array();

	public static function getList($type){
		$nvList = D('Lookup')
			->field('name,val')
			->where(array('type'=>$type, 'inactive'=>'N'))
			->order('seq_num')
			->select();

		return $nvList;
	}

	public static function getValue($type, $name){
		if(isset(LookupModel::$_cache[$type][$name])){
			return LookupModel::$_cache[$type][$name];
		}

		$nvList = D('Lookup')
			->field('name,val')
			->where(array('type'=>$type))
			->select();

		if(empty($nvList)){
			return null;
		}

		foreach($nvList as $nv){
			LookupModel::$_cache[$type][$nv['name']] = $nv['val'];
		}

		return LookupModel::$_cache[$type][$name];

	}
}