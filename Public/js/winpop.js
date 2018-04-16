//浏览器的宽高
function wh() {
	tq_width=$(window).width(); 
	tq_height=$(window).height(); 
}
wh();
function wintq_q() {
	$('#wintq').css({'left':(tq_width/2)-$('#wintq').width()/2+'px','top':(tq_height/2)-($('#wintq').height()/2)+'px'});
	$('#pop').css({'left':(tq_width/2)-$('#pop').width()/2+'px','top':(tq_height/2)-($('#pop').height()/2)+'px'});
	$('#zbody').css({'width':tq_width+'px','height':tq_height+'px'});
	$('#tcimg').css({'left':(tq_width/2)-$('#tcimg').width()/2+'px','top':'10px'});
	if ((tq_height/2) - ($('#iframe_pop').height()/2) < 1) {
		$('#iframe_pop').css({'top':'0px'});
	}
}
function zbody() {
	$('body').append('<div id="zbody"></div>');
	wintq_q();
}
//弹窗函数 1、内容 2、状态 3、时间 4、遮蔽 5、跳转
function wintq(text,status,setOut,z,url) {
	$('#wintq').remove();
	$('#zbody').remove();
	if (z==0) {
		zbody();
	}
	//1代表成功,2代表提示,3代表失败
	if (status==1) {
		tqhtml='<div id="wintq"><div class="tqLeft"></div><div class="tqCenter">'+text+'</div><div class="tqRight"></div></div>';
	}else if (status==2) {
		tqhtml='<div id="wintq"><div class="tqLeft2"></div><div class="tqCenter">'+text+'</div><div class="tqRight"></div></div>';
	}else if (status==3) {
		tqhtml='<div id="wintq"><div class="tqLeft3"></div><div class="tqCenter">'+text+'</div><div class="tqRight"></div></div>';
	}else if (status==4) {
		tqhtml='<div id="wintq"><div class="tqLeft4"></div><div class="tqCenter"><div class="loading">'+text+'</div></div><div class="tqRight"></div></div>';
	}
	$('body').append(tqhtml);
	wintq_q();
	setTimeout(function() {
		//判断是否有地址跳转
		if (url!='' && url!='undefined') {
			if (url=='?') {
				window.location.reload();
			}else {
				location.href=url;
			}
		}else {
			$('#wintq').remove();
			$('#zbody').remove();
			return false;
		}
	},setOut);
}
//加载Iframe框架弹窗
function popload(title,lw,lh,loadurl,z) {
	$('#iframe_pop').remove();
	$('#zbody').remove();
	pophtml='<div id="iframe_pop"><div class="pop_h1">'+title+'<div class="pop_close" title="关闭"></div></div><iframe scrolling="0" width="'+lw+'" height="'+lh+'" src="'+loadurl+'" frameborder="0" name="iframe_pop"></iframe></div>';
	$('body').append(pophtml);
	$('#iframe_pop').css({'left':(tq_width/2)-$('#iframe_pop').width()/2-10+'px','top':(tq_height/2)-($('#iframe_pop').height()/2)+'px'});
	zbody();
	$('#iframe_pop .pop_close').click(function() {
		$('#iframe_pop').remove();
		$('#zbody').remove();
	});
}
//加载父Iframe框架弹窗
function fpopload(title,lw,lh,loadurl) {
	pophtml='<div id="f_pop"><div class="pop_h1">'+title+'<div class="pop_close" title="关闭"></div></div><iframe scrolling="0" width="'+lw+'" height="'+lh+'" src="'+loadurl+'" frameborder="0" name="f_pop"></iframe></div>';
	$('body').append(pophtml);
	$('#f_pop').css({'left':(tq_width/2)-$('#f_pop').width()/2+'px','top':(tq_height/2)-($('#f_pop').height()/2)+'px'});
	$('body').append('<div id="sbody"></div>');
	$('#f_pop .pop_close').click(function() {
		$('#f_pop').remove();
		$('#sbody').remove();
	});
}
//弹窗显示图片
function tcimg(src) {
	zbody();
	html='<div id="tcimg"><div class="close" title="关闭"></div><img src="'+src+'" border="0" /></div>';
	$('body').append(html);
	wintq_q();
	$('#tcimg .close').click(function() {
		$('#tcimg').remove();
		$('#zbody').remove();
	});
}
//拖动组件
function addDiv(obj) {
	//给当前的div的dtitle元素绑定鼠标按下事件
	obj.mousedown(function(event) {
		event.preventDefault();
		obj.css({'width':obj.width()+'px','height':obj.height()+'px','background':'#fff'});
		//$('html,body').animate({scrollTop:0},0);
		obj.find('iframe').hide();
		var offset = $(this).offset();
		var x = event.clientX - offset.left;
		var y = event.clientY - offset.top;
		$(document).mousemove(function(event) {
			event.preventDefault();
			var _left = event.clientX - x;
			var _top = event.clientY - y;
			if (_left < 0) {
				obj.css('left','0px');
			}else {
				obj.css('left',_left+'px');
			}
			
			if (_left > tq_width-obj.width()) {
				obj.css('left',(tq_width-obj.width())+'px');
			}
			if (_top < 0) {
				obj.css('top','0px');
			}else {
				obj.css('top',_top+'px');
			}
			if (_top > tq_height-obj.height()) {
				obj.css('top',(tq_height-obj.height())+'px');
			}
			return false;
		});
		obj.mouseup(function() {
			$(document).unbind()					//解除
			obj.find('iframe').show();
			$('body').css('overflow','auto');
			return false;
		});
	});
}
$(window).resize(function() {
	wh();
	wintq_q();
});
//关闭重新加载
function popclose() {
	$('#iframe_pop .pop_close').click(function() {
		window.location.reload();
	});
}
//重载页面
function htmlload() {
	window.location.reload();
}