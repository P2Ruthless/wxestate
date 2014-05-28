<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo ($site_title); ?></title>
	<meta name="keywords" content="<?php echo ($site_keywords); ?>" />
	<meta name="description" content="<?php echo ($site_description); ?>"/>
	<link rel="stylesheet" type="text/css" href="/Public/Home/css/all.css"/>
	<link rel="stylesheet" type="text/css" href="/Public/Home/css/site.css"/>
	
</head>
<body>
	<div class="login_head">
		<div class="login_text"><a href="#"class="loginimg">登录</a>&nbsp;/&nbsp;<a href="#">注册</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;客服：400&nbsp;&nbsp;000&nbsp;&nbsp;0000
		</div>
	</div>
	<div class="header_box2">
		<div class="logo">
			<img src="/Public/Home/images/logo.jpg" width="232" height="82" />
		</div>
		<div class="search_box">
			<table width="100%" height="34" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="82%"><input type="text" class="search_input" /></td>
					<td width="18%"><a href="#" class="search_btn">搜索楼盘</a></td>
				</tr>
			</table>
		</div>
		<div class="clear"></div>
		<div class="nav_box">
			<?php $__NAV__ = M('Channel')->field('id,pid,title,url,target')->where("status=1")->order("sort")->select(); if(is_array($__NAV__)): $i = 0; $__LIST__ = $__NAV__;$voCount = count($__LIST__);if( $voCount==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$nav): $mod = ($i % 2 );++$i; if(($nav["pid"]) == "0"): ?><li>
				<a <?php if(($currentNav) == $nav["id"]): ?>class="navnow"<?php endif; ?> href="<?php echo (get_nav_url($nav["url"])); ?>" target="<?php if(($nav["target"]) == "1"): ?>_blank<?php else: ?>_self<?php endif; ?>"><?php echo ($nav["title"]); ?></a>
				<?php if(($voCount) != $i): ?><span class="spaceitem"></span><?php endif; ?>
			</li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
			<div class="clear"></div>
		</div>
	</div>
	<div class="content_box">
	
<br />
	<img  width="1000" height="110" />
	<br />
	<div class="index_left_tt">房屋买卖<span>最新二手房源</span></div>
	<a href="#" class="index_rslist">
		<img  />
		<span>甘井子 泡崖康居园</span><em>95万</em>
		<div class="pingshu">4室2厅 | 142M2</div>    
	</a>
	<div class="clear"></div>
	<div class="contenmid_dox">
		<div class="fangwuzulin">
			<div class="index_left_tt">房屋租赁</div>
			<?php if(is_array($houseRentList)): $i = 0; $__LIST__ = $houseRentList;$voCount = count($__LIST__);if( $voCount==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$houseRent): $mod = ($i % 2 );++$i;?><a href="#" class="fangwuzulin_list">
				<img src="<?php echo ((isset($houseRent["thumbnail"]) && ($houseRent["thumbnail"] !== ""))?($houseRent["thumbnail"]):''); ?>" />
			<?php echo ($houseRent["area_name"]); ?>-<?php echo ($houseRent["busi_area_name"]); ?><br />
			<?php echo ($houseRent["bed_room"]); ?>室<?php echo ($houseRent["live_room"]); ?>厅，<?php echo (get_lookup_value('HOUSE_DECORATE_TYPE',$houseRent["decorate"])); ?><br />
			<span><?php echo ($houseRent["price"]); ?>元/月</span>
			</a><?php endforeach; endif; else: echo "" ;endif; ?>
			<div class="clear"></div>
		</div>
		<div class="index_guanzhu"></div>
		<div class="clear"></div>
	</div>
	<div class="contenmid_dox">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="69%"><img alt="" width="685" height="87" /></td>
				<td width="16%"><img src="/Public/Home/images/maifang1.gif" width="150" height="87" /></td>
				<td width="15%"><img src="/Public/Home/images/maifang2.gif" width="150" height="87" /></td>
			</tr>
		</table>
	</div>
	<!--left HOTLP-->
	<div class="left_hotlp2">
		<div class="left_hotlp_tt">热门楼盘</div>
		<ul class="hotlp_box2">
			<li><div class="hotlp_box_line">
			<a href="#"><img  /></a>
			<a href="#" class="hotlp_lpname">城市印象在售</a><span>在售</span>
			<div class="clear"></div>
			均价：14000元/平米<br />
			电话：400-606-6969 转 27829<br />
			地址：甘井子区迎客路中段(机场附近)<br />
			<a href="#">实景6张</a>&nbsp;&nbsp;<a href="#"> 户型4张</a>
			<a href="#"></a> <div class="hotlp_num">1</div>
			</div>
		</li>

		<li><div class="hotlp_box_line">
			<a href="#"><img  /></a>
			<a href="#" class="hotlp_lpname">城市印象在售</a><span>在售</span>
			<div class="clear"></div>
			均价：14000元/平米<br />
			电话：400-606-6969 转 27829<br />
			地址：甘井子区迎客路中段(机场附近)<br />
			<a href="#">实景6张</a>&nbsp;&nbsp;<a href="#"> 户型4张</a>
			<a href="#"></a> <div class="hotlp_num">1</div>
			</div></li>
			<li><div class="hotlp_box_line">
			<a href="#"><img  /></a>
			<a href="#" class="hotlp_lpname">城市印象在售</a><span>在售</span>
			<div class="clear"></div>
			均价：14000元/平米<br />
			电话：400-606-6969 转 27829<br />
			地址：甘井子区迎客路中段(机场附近)<br />
			<a href="#">实景6张</a>&nbsp;&nbsp;<a href="#"> 户型4张</a>
			<a href="#"></a> <div class="hotlp_num">1</div>
			</div>
		</li>
		<li><div class="hotlp_box_line">
			<a href="#"><img  /></a>
			<a href="#" class="hotlp_lpname">城市印象在售</a><span>在售</span>
			<div class="clear"></div>
			均价：14000元/平米<br />
			电话：400-606-6969 转 27829<br />
			地址：甘井子区迎客路中段(机场附近)<br />
			<a href="#">实景6张</a>&nbsp;&nbsp;<a href="#"> 户型4张</a>
			<a href="#"></a> <div class="hotlp_num">1</div>
			</div>
		</li>
		<li><div class="hotlp_box_line">
			<a href="#"><img  /></a>
			<a href="#" class="hotlp_lpname">城市印象在售</a><span>在售</span>
			<div class="clear"></div>
			均价：14000元/平米<br />
			电话：400-606-6969 转 27829<br />
			地址：甘井子区迎客路中段(机场附近)<br />
			<a href="#">实景6张</a>&nbsp;&nbsp;<a href="#"> 户型4张</a>
			<a href="#"></a> <div class="hotlp_num">1</div>
			</div>
		</li>
		<li><div class="hotlp_box_line">
			<a href="#"><img  /></a>
			<a href="#" class="hotlp_lpname">城市印象在售</a><span>在售</span>
			<div class="clear"></div>
			均价：14000元/平米<br />
			电话：400-606-6969 转 27829<br />
			地址：甘井子区迎客路中段(机场附近)<br />
			<a href="#">实景6张</a>&nbsp;&nbsp;<a href="#"> 户型4张</a>
			<a href="#"></a> <div class="hotlp_num">1</div>
			</div>
		</li>

		<div class="clear"></div>
	</ul>
	</div>
    <!--left HOTLP-->
	<div class="index_right">
		<div class="index_right_tt">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="10%"><img src="images/indexrightitme.gif" width="17" height="15" /></td>
					<td width="70%">房产动态</td>
					<td width="20%"><a href="#">更多&gt;&gt;</a></td>
				</tr>
			</table>
		</div>
		<div class="index_news_list">
		<ul>
			<li class="top"><span>1</span><a href="#" >近期二手房降价分析</a><em>2014-05-25</em></li>
			<li class="top"><span>2</span><a href="#" >近期二手房降价分析</a><em>2014-05-25</em></li>
			<li class="top"><span>3</span><a href="#" >近期二手房降价分析</a><em>2014-05-25</em></li>
			<li class="ntop"><span>4</span><a href="#" >近期二手房降价分析</a><em>2014-05-25</em></li>
			<li class="ntop"><span>5</span><a href="#" >近期二手房降价分析</a><em>2014-05-25</em></li>
			<li class="ntop"><span>6</span><a href="#" >近期二手房降价分析</a><em>2014-05-25</em></li>
		</ul>
		<div class="clear"></div>
		</div>
		<br />
		<div class="index_right_tt">
		热门商铺出售
		</div>
		<div class="index_spcs_box">
		<ul>
			<li class="top">
			<img />
			亿丰国际汽车城 <span>11000元</span>/平米亿丰大连国际汽车城项目位于大连市甘井子区西 北路872号，向北是大连 
			</li>
			<li class="top">
			<img />
			亿丰国际汽车城 <span>11000元</span>/平米亿丰大连国际汽车城项目位于大连市甘井子区西 北路872号，向北是大连 
			</li>
		</ul>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="51%">城市印象</td>
			<td width="30%" align="center">甘井子区</td>
			<td width="19%" align="right"><span>100</span>万元</td>
		</tr>
		<tr>
			<td>城市印象</td>
			<td align="center">甘井子区</td>
			<td align="right"><span>100</span>万元</td>
		</tr>
		<tr>
			<td>城市印象</td>
			<td align="center">甘井子区</td>
			<td align="right"><span>100</span>万元</td>
		</tr>
		</table>

		</div>
		<div class="index_right_tt">
		商铺快搜
		</div>
		<div class="index_spks">
		[商圈]<a href="#">奥林匹克广场</a><a href="#">金马路</a><a href="#">奥林匹克广场</a><a href="#">金马路</a><br />
		[租金]<a href="#">1000以下</a><a href="#">1000-1500</a><a href="#">1500-2000</a><a href="#">更多</a><br />
		[热点]<a href="#">住宅底商</a><a href="#">临街门面</a><a href="#">写字楼配套底商</a><a href="#">更多</a><br />
		</div>
	</div>
<div class="clear"></div>

	</div>
	<div class="footerbox">
	关于我们| 联系我们 | 加入我们 | 网站地图 | 友情链接<br />
	大连恒润房地产经纪有限公司/ 网络经营许可证 <?php echo C('WEB_SITE_ICP');?><br />
	Copyright©2014 大连恒润房地产经纪有限公司保留全部权利
	</div>
	
</body>
</html>