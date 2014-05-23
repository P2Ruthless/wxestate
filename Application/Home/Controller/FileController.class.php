<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */

class FileController extends HomeController {
	/* 文件上传 */
	public function upload(){
		$return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		$info = $File->upload(
			$_FILES,
			C('DOWNLOAD_UPLOAD'),
			C('DOWNLOAD_UPLOAD_DRIVER'),
			C("UPLOAD_{$file_driver}_CONFIG")
		);

		/* 记录附件信息 */
		if($info){
			$return['data'] = think_encrypt(json_encode($info['download']));
		} else {
			$return['status'] = 0;
			$return['info']   = $File->getError();
		}

		/* 返回JSON数据 */
		$this->ajaxReturn($return);
	}

	/* 下载文件 */
	public function download($id = null){
		if(empty($id) || !is_numeric($id)){
			$this->error('参数错误！');
		}

		$logic = D('Download', 'Logic');
		if(!$logic->download($id)){
			$this->error($logic->getError());
		}
		
	}

	/**
	 * 上传图片
	 * @author huajie <banhuajie@163.com>
	 */
	public function uploadHousePics(){
		//TODO: 用户登录检测
		/* 返回标准数据 */
		$return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

		if(!is_login()){
			$return['status'] = 0;
			$return['info'] = '请先登录';
			$this->ajaxReturn($return);
		}

		/* 调用文件上传组件上传文件 */
		$Picture = new \Admin\Model\PictureModel;
		$pic_driver = C('PICTURE_UPLOAD_DRIVER');
		$info = $Picture->upload(
			$_FILES,
			C('PICTURE_UPLOAD'),
			C('PICTURE_UPLOAD_DRIVER'),
			C("UPLOAD_{$pic_driver}_CONFIG")
		);

		/* 记录图片信息 */
		if($info){
			//图片水印
			$picPath = '.'.$info['fileUpload']['path'];
			$image = new \Think\Image();
			$image->open($picPath)->water(C('PICTURE_WARTER.warterPic'), C('PICTURE_WARTER.position'), C('PICTURE_WARTER.alpha'))
				->save($picPath);

			$return['status'] = 1;
			$return = array_merge($info['fileUpload'], $return);
		} else {
			$return['status'] = 0;
			$return['info']   = $Picture->getError();
		}

		/* 返回JSON数据 */
		$this->ajaxReturn($return);
    }

    public function deleteHousePic($id, $uid){
    	$return = array('status'=>1, 'info'=>'删除成功', 'id'=>$id);
		$model = M('Picture');
		$pic = $model->find($id);
		if(empty($pic)){
			//图片不存在
			$return['info'] = '图片不存在';
			$this->ajaxReturn($return);
		}
		if($pic['uid'] != $uid){
			//不允许删除
			$return['info'] = '不允许删除';
			$this->ajaxReturn($return);
		}

		$model->delete($id);

		$this->ajaxReturn($return);
    }
}
