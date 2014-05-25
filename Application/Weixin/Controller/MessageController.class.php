<?php
namespace Weixin\Controller;

use Think\Controller;
use Weixin\Api\Wechat;

class MessageController extends Controller{
	private $_wechat;

	public function execute($wxid){
		$this->_wechat = Wechat::create($wxid);
		if($this->wechat == false){
			die('账户不存在或已失效');
		}
/*
		$validResult = $this->_wechat->valid(true);

		if(!IS_POST){
			die($validResult);
		}

		if($validResult === false){
			die('no access');
		}
*/
		$type = $this->_wechat->getRev()->getRevType();

		if($this->checkRepeat()){
			die('repeat messages');
		}

		$EventMaterial = M('WxEventMaterial');
		$executor = $EventMaterial->field('m.id,m.type,m.content,m.title,m.media_id,m.music_url,m.hq_music_url')
				->alias('em')
				->join('__WX_MATERIAL__ m on em.material_id=m.id')
				->where(array('em.account_id'=>$wxid, 'em.event_type'=>$type))
				->find();

		if(empty($executor)){
			exit();
		}

		$executorMethodName = 'execute'.ucfirst($executor['type']);

		if(!method_exists($this, $executorMethodName)){
			exit();
		}

		$result = call_user_func_array(array($this, $executorMethodName), array($wxid, $executor));

		if($result){
			echo $result;
		}
	}

	protected function checkRepeat($save = true){
		$type = $this->_wechat->getRevType();
		if($type == Wechat::MSGTYPE_EVENT){
			$model = M('WxUserEvent');

			$data = $model->field('id')
				->where(array('from_user'=>$this->_wechat->getRevFrom(), 'created'=>(int)$this->_wechat->getRevCtime()))
				->find();

			if(!empty($data) || !$save){
				return true;
			}

			$data['from_user'] = $this->_wechat->getRevFrom();
			$data['to_user'] = $this->_wechat->getRevTo();
			$data['created'] = (int)$this->_wechat->getRevCtime();

			$temp = $this->_wechat->getRevEvent();

			$data['event'] = $temp['event'];
			$data['event_key'] = $temp['key'];
			if($temp['event'] == 'subscribe' || $temp['event'] == 'SCAN'){
				$data['ticket'] = $this->_wechat->getRevTicket();
			}elseif($temp['event'] == 'LOCATION'){
				$temp = $this->_wechat->getRevEventGeo();
				$data['loc_x'] = (double)$temp['x'];
				$data['loc_y'] = (double)$temp['y'];
				$data['prec'] = (double)$temp['precision'];
			}

			$model->add($data);

			return false;
		}else{
			$model = M('WxUserMsg');
			$data = $model->field('id')->find($this->_wechat->getRevID());

			if(!empty($data) || !$save){
				return true;
			}

			$data['id'] = (float)$this->_wechat->getRevID();
			$data['from_user'] = $this->_wechat->getRevFrom();
			$data['to_user'] = $this->_wechat->getRevTo();
			$data['created'] = (int)$this->_wechat->getRevCtime();
			$data['msg_type'] = $this->_wechat->getRevType();

			if($data['msg_type'] == Wechat::MSGTYPE_TEXT){
				$data['content'] = $this->_wechat->getRevContent();
			}elseif($data['msg_type'] == Wechat::MSGTYPE_IMAGE){
				$data['pic_url'] = $this->_wechat->getRevPic();
				$data['media_id'] = $this->_wechat->getDataByKey('MediaId');
			}elseif($data['msg_type'] == Wechat::MSGTYPE_VOICE){
				$data['media_id'] = $this->_wechat->getDataByKey('MediaId');
				$data['format'] = $this->_wechat->getDataByKey('Format');
			}elseif($data['msg_type'] == Wechat::MSGTYPE_VIDEO){
				$data['media_id'] = $this->_wechat->getDataByKey('MediaId');
				$data['thumb_media_id'] = $this->_wechat->getDataByKey('ThumbMediaId');
			}elseif($data['msg_type'] == Wechat::MSGTYPE_LOCATION){
				$data['loc_x'] = (double)$this->_wechat->getDataByKey('Location_X');
				$data['loc_y'] = (double)$this->_wechat->getDataByKey('Location_Y');
				$data['scale'] = (int)$this->_wechat->getDataByKey('Scale');;
				$data['content'] = $this->_wechat->getDataByKey('Label');;
			}elseif($data['msg_type'] == Wechat::MSGTYPE_LINK){
				$data['title'] = $this->_wechat->getDataByKey('Title');;
				$data['content'] = $this->_wechat->getDataByKey('Description');
				$data['url'] = $this->_wechat->getDataByKey('Url');
			}

			$model->add($data);
			return false;
		}

	}

	protected function executeText($wxid, $executor){
		return $this->_wechat->text($executor['content'])->reply(true);
	}

	protected function executeNews($wxid, $msgObj, $executor){
		$MaterialItem = M('wx_material_item');

		$items = $MaterialItem->field('title,desc_txt,pic_url,url,seq_num')
			->where(array('mater_id'=>(int)$executor['id']))
			->order('seq_num')
			->select();

		if(empty($items)){
			return false;
		}

		$articles = array();
		foreach ($items as $key => $value) {
			if($key == 'title'){
				$articles['Title'] = $value;
			}elseif($key == 'desc_txt'){
				$articles['Description'] = $value;
			}elseif($key == 'pic_url'){
				$articles['PicUrl'] = $value;
			}elseif($key == 'url'){
				$articles['Url'] = $value;
			}
		}

		return $this->_wechat->news($articles)->replay(true);
	}
}