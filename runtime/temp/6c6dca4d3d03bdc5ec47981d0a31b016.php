<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"D:\wamp\www\thinkblog\thinkBlog\public/../application/index\view\index\index.html";i:1515980775;}*/ ?>
<!DOCTYPE html>
<html style="font-size:50px">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
	<meta name="wap-font-scale" content="no">
	<meta name="format-detection" content="telephone=no">
	<title>Hcm-Blog</title>
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="/static/index/css/Base.css">
	<link rel="stylesheet" href="<?php echo $css; ?>">
	<script src="http://apps.bdimg.com/libs/jquery/1.8.0/jquery.min.js"></script>
	<script type="text/javascript">
		function isPhone() {
		    var userAgentInfo = navigator.userAgent;
		    var Agents = ["Android", "iPhone",
		                "SymbianOS", "Windows Phone",
		                "iPad", "iPod"];
		    var flag = false;
		    for (var v = 0; v < Agents.length; v++) {
		        if (userAgentInfo.indexOf(Agents[v]) > 0) {
		            flag = true;
		            break;
		        }
		    }
		    return flag;
		}
		if(isPhone()){
			$('head link[href="<?php echo $css; ?>"]').eq(0).attr('href','<?php echo $Pcss; ?>');
		}
	</script>
	<link rel="stylesheet" href="/static/index/css/flatpickr.min.css">
</head>
<body>
	<div class="index wapper">
		<div class="person top">
			<div class="container">
				<div class="personInfo">
					<?php switch($name=$system['work_status']): case "1": ?><a href="/index/person.html" onclick="event.stopPropagation();"><?php break; case "0": ?><a href="#" onclick="alert('已找到工作，简历不开放');event.stopPropagation();"><?php break; endswitch; ?><img class="head" src="/static/common/images/logo.jpg" alt="Hcm-blog"></a>
					<div class="me">
						<h1 class="name"><?php echo $person['nickname']; ?></h1>
						<p><?php echo $person['intro']; ?></p>
					</div>
					</div>
					<div class="menu"></div>
					<ul class="nav">
						<li><a href="/index/1.html">CSS3</a></li>
						<li><a href="/index/2.html">HTML5</a></li>
						<li><a href="/index/3.html">JavaScript</a></li>
						<li><a href="javascript:;" onclick="alert('私人页面不做开放');event.stopPropagation();">日记</a></li>
					</ul>
				</div>
				<ul id="banner">
					<?php if(is_array($Hlist) || $Hlist instanceof \think\Collection || $Hlist instanceof \think\Paginator): $i = 0; $__LIST__ = $Hlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$flash): $mod = ($i % 2 );++$i;?>
				        <li style="background-image:url('/static/common/images/upload/flash/<?php echo $flash['path']; ?>')"></li>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
		<div class="container">
			<div class="news">
				<ul class="list">
				<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$List): $mod = ($i % 2 );++$i;?>
					<li><figure><a href="/index/article/<?php echo $List['id']; ?>.html" class="title"><h2><?php echo $List['title']; ?></h2></a><p class="text"><?php echo $List['intro']; ?></p></figure><p class="info"><span class="type">分类：<?php switch($name=$List['type']): case "1": ?>
							<a href="/index/1.html">HTML&CSS</a>
						<?php break; case "2": ?>
							<a href="/index/2.html">CSS3</a>
						<?php break; case "3": ?>
							<a href="/index/3.html">Javascript</a>
						<?php break; endswitch; ?></span><span class="time fr"><?php echo $List['create_time']; ?></span></p></li>
				<?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
				<?php echo $list->render(); ?>
			</div>
			<div class="record">
				<div class="r-list">
					<div class="date">
						<input id="flatpickr-tryme" type = "hidden">
					</div>
					<div class="today slist">
						<h2>今日小记</h2>
						<p><?php echo $diary['content']; ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="/static/index/js/flatpickr.min.js" type="text/javascript"></script>
	<script src="<?php echo $js; ?>" type="text/javascript"></script>
	<script>
		//开场动画
		$(window).load(function(){
			page.banner();
			$('.person .head').animate({borderRadius:"50%"},900,function(){
				$('.me h1').fadeIn(function(){
					$('.me p').fadeIn(function(){
					})
				});
			})
			//日期插件
			$("#flatpickr-tryme").flatpickr({
				inline:true
			});
		})
	</script>
</body>
</html>