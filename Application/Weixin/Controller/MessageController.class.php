<?php
namespace Weixin\Controller;

use Think\Controller;

class MessageController extends Controller{
	public function execute($wxid, $signature=null, $timestamp=null, $nonce=null, $echostr=null){
		$WxAccount = M('WxAccount');
		$account = $WxAccount->field('valid_token')
			->where(array('id'=>$wxid, 'status'=>1))
			->find();

		if(empty($account)
			|| !$this->checkSignature($account['valid_token'], $signature, $timestamp, $nonce)){
			exit();
		}

		if(!IS_POST){
			echo $echostr;
			exit();
		}
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

		if(empty($postStr)){
			exit();
		}

		$msgObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

		if($this->checkRepeat($msgObj)){
			exit();
		}


		$EventMaterial = M('WxEventMaterial');
		$executor = $EventMaterial->field('m.id,m.type,m.content,m.title,m.media_id,m.music_url,m.hq_music_url')
				->alias('em')
				->join('__WX_MATERIAL__ m on em.material_id=m.id')
				->where(array('em.account_id'=>$wxid, 'em.event_type'=>(string)$msgObj->MsgType))
				->find();

		if(empty($executor)){
			exit();
		}

		$executorMethodName = 'execute'.ucfirst($executor['type']);

		if(!method_exists($this, $executorMethodName)){
			exit();
		}

		$result = call_user_func_array(array($this, $executorMethodName), array($wxid, $msgObj, $executor));

		if($result){
			echo $result;
		}
	}

	protected function checkSignature($myToken, $signature, $timestamp, $nonce){
		$tmpArr = array($myToken, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);

		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

	protected function checkRepeat($msgObj, $save = true){
		if($msgObj->MsgType == 'event'){
			$model = M('WxUserEvent');

			$data = $model->field('id')
				->where(array('from_user'=>$msgObj->FromUserName, 'created'=>(int)$msgObj->CreateTime))
				->find();

			if(!empty($data) || !$save){
				return true;
			}

			$data['from_user'] = (string)$msgObj->FromUserName;
			$data['to_user'] = (string)$msgObj->ToUserName;
			$data['created'] = (int)$msgObj->CreateTime;
			$data['event'] = (string)$msgObj->Event;
			$data['event_key'] = (string)$msgObj->EventKey;
			if($msgObj->Event == 'subscribe' || $msgObj->Event == 'SCAN'){
				$data['ticket'] = (string)$msgObj->Ticket;
			}elseif($msgObj->Event == 'LOCATION'){
				$data['loc_x'] = (double)$msgObj->Longitude;
				$data['loc_y'] = (double)$msgObj->Latitude;
				$data['prec'] = (double)$msgObj->Precision;
			}

			$model->add($data);

			return false;
		}else{
			$model = M('WxUserMsg');
			$data = $model->field('id')->find((float)$msgObj->MsgId);

			if(!empty($data) || !$save){
				return true;
			}

			$data['id'] = (float)$msgObj->MsgId;
			$data['from_user'] = (string)$msgObj->FromUserName;
			$data['to_user'] = (string)$msgObj->ToUserName;
			$data['created'] = (int)$msgObj->CreateTime;
			$data['msg_type'] = (string)$msgObj->MsgType;

			if($msgObj->MsgType == 'text'){
				$data['content'] = (string)$msgObj->Content;
			}elseif($msgObj->MsgType == 'image'){
				$data['pic_url'] = (string)$msgObj->PicUrl;
				$data['media_id'] = (string)$msgObj->MediaId;
			}elseif($msgObj->MsgType == 'voice'){
				$data['media_id'] = (string)$msgObj->MediaId;
				$data['format'] = (string)$msgObj->Format;
			}elseif($msgObj->MsgType == 'video'){
				$data['media_id'] = (string)$msgObj->MediaId;
				$data['thumb_media_id'] = (string)$msgObj->ThumbMediaId;
			}elseif($msgObj->MsgType == 'location'){
				$data['loc_x'] = (double)$msgObj->Location_X;
				$data['loc_y'] = (double)$msgObj->Location_Y;
				$data['scale'] = (int)$msgObj->Scale;
				$data['content'] = (string)$msgObj->Label;
			}elseif($msgObj->MsgType == 'link'){
				$data['title'] = (string)$msgObj->Title;
				$data['content'] = (string)$msgObj->Description;
				$data['url'] = (string)$msgObj->Url;
			}

			$model->add($data);
			return false;
		}

	}

	protected function executeText($wxid, $msgObj, $executor){
		$now = time();

		$result = "<xml>
<ToUserName><![CDATA[{$msgObj->FromUserName}]]></ToUserName>
<FromUserName><![CDATA[{$msgObj->ToUserName}]]></FromUserName>
<CreateTime>$now</CreateTime>
<MsgType><![CDATA[".$executor['type']."]]></MsgType>
<Content><![CDATA[".$executor['content']."]]></Content>
<FuncFlag>0</FuncFlag>
</xml>";
		return $result;
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

		$now = time();

		$result = "<xml>
<ToUserName><![CDATA[{$msgObj->FromUserName}]]></ToUserName>
<FromUserName><![CDATA[{$msgObj->ToUserName}]]></FromUserName>
<CreateTime>$now</CreateTime>
<MsgType><![CDATA[".$executor['type']."]]></MsgType>
<ArticleCount>".count($items)."</ArticleCount>
<Articles>";

		foreach($items as $item){
			$result .= "<item>
<Title><![CDATA[".$item['title']."]]></Title>
<Description><![CDATA[".$item['desc_txt']."]]></Description>
<PicUrl><![CDATA[".$item['pic_url']."]]></PicUrl>
<Url><![CDATA[".$item['url']."]]></Url>
</item>";
		}

		$result.="</Articles></xml>";

		return $result;
	}

	public function tttttt(){
		$Model = M('WxUserMsg');
		$data = $Model->field('id')->find(11);
	}
}