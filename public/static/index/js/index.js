$(function(){
	page={
		inital:function(){
			page.Phone();//手机端的高清适配方案
		},
		banner:function(){
				$('#banner li').eq(0).show();
				var banner_index=1;
				var timer =setInterval(function(){
					if(banner_index>=$('#banner li').length-1){
						banner_index=0;
					}
					$('#banner li').fadeOut();
					$('#banner li').eq(banner_index).fadeIn();
					banner_index++;
					console.log(banner_index);
				},5000)
		},
		isPhone:function() {
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
		},
		Phone:function(){
			if(page.isPhone()){
				document.documentElement.style.fontSize = document.documentElement.clientWidth / 6.4 + 'px';
				if($('title').text()=='Hcm-intro'){
					//手机端只显示最新的三个履历
					page.works_height($('.works').length)
				}
				$('.top .container').click(function(){
					if($('.nav').css('display')!='block'){
						$('.menu').fadeIn(function(){
							setTimeout(function(){
								$('.menu').fadeOut();
							},5000)
						});
					}
				})
				$('.menu').click(function(){
					$(this).fadeOut('speed');
					$('.nav').show().animate({
						'marginRight':'-.2rem'
					});;
					event.stopPropagation();
				})
				$(document).click(function(){
					$('.nav').click(function(){
						event.stopPropagation();
					})
					$('.nav').animate({'marginRight':'-1.7rem'}).fadeOut();
				})
			}
		},
		works_height:function(works_count){
			if(works_count<3){
				works_count=3;
			}
			for(i=works_count-3;i>0;i--){
				$('.works').eq(i-1).hide();
			}
			//存储所有履历文字的高度
			var contentHeight=[];
			//初始化履历文字省略
			$('.works .content p').addClass('text-ellipsis');
			$('.works .content p').eq(works_count-3).addClass('active');
			//手机端简历点击显示全部内容
			$('.works').click(function(){
				$(this).siblings().find('p').removeClass('active');
				$(this).find('p').addClass('active')
			})
		}
	}
	page.inital();
	person ={
		inital:function(){
			person.RandomText(500);
			person.Works_Top();
		},
		//随机文字函数
		RandomText:function(delay){
			var content = $('#random').text()
			var len=content.length;
			var arr=[];
			var j =0;
			var textarr = content.split('');
			$('#random').empty();
			for(var i =0;i<len;i++){
				arr[i] =i;
				$('#random').append("<span style='opacity:0;'>"+content[i]+"</span>")
			}

			arr.sort(function(){
				return 0.5-Math.random();
			})
			setTimeout(function(){
				var timer =setInterval(function(){
					$('#random span').eq(arr[j]).animate({'opacity':1});
					if(j<len-1){
						j++;
					}else{
						clearInterval(timer)
					}
				},200)
			},delay)
		},
		Works_Top:function(){
			var len=$('.works').length;
			var last=$('.works').eq(len-1);
			var height=0;
			for(var i =0;i<len;i++){
				var works =$('.works').eq(i);
				height +=works.height()+10;
			}
			if(height+100 >= $('.work').height()){
				for(var i =0;i<$('.l-works').length;i++){
					$('.l-works').eq(i+1).css({'top':$('.l-works').eq(i).height()+$('.l-works').eq(i).position().top+10})
				}
				for(var i =0;i<$('.r-works').length;i++){
					$('.r-works').eq(i+1).css({'top':$('.r-works').eq(i).height()+$('.r-works').eq(i).position().top+10})
				}
			}else{
				for(var i =0;i<len;i++){
					var works =$('.works').eq(i);
					$('.works').eq(i+1).css({'top':works.height()+works.position().top+10})
				}
			}
			$('.work .line').height($('.work').height()-150)
		}
	}
	person.inital();
})