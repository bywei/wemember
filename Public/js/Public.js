// JavaScript Document
var w_width,
	w_height;
//浏览器的宽高
function win() {
	w_width=$(window).width(); 
	w_height=$(window).height();
	$('#left').css({'width':'160px','height':(w_height-71)+'px'});
	$('#right').css({'width':(w_width-176)+'px','height':(w_height-71)+'px'});
}
//刷新框架页面
function f5() {
	window.parent.frames['c'].location.reload();
}
function lhide() {
	$('.left').click(function() {
		if ($('#left').css('display')=='block') {
			$('#left').hide();
			$('#right').css({'width':w_width+'px'});
			$('.left').css('left','-2px');
		}else {
			$('#left').show();
			$('#right').css({'width':(w_width-176)+'px'});
			$('.left').css('left','156px');
		}
	});
}
//表单控件阴影
function shadow() {
	$('#content .text, .qtext, .ctext, .dtext, textarea').focus(function() {
		$(this).css({'border':'solid 1px #333','boxShadow':'0px 0px 6px #333'});
	});
	$('#content .text, .qtext, .ctext, .dtext, textarea').blur(function() {
		$(this).css({'border':'solid 1px #ccc','boxShadow':'none'});
	})
}
//全选复选框
function funbox() {
	//复选框选择
	$('#table .indel').change(function() {
		if (!$('#table .indel').attr('checked') == false) {
			for (i=0; i<$('#table .delid').size(); i++) {
				$('#table .delid').eq(i).attr('checked',true);
			}
		}else {
			for (i=0; i<$('#table .delid').size(); i++) {
				$('#table .delid').eq(i).attr('checked',false);
			}
		}
	});
	//全选
	$('#page .selbox').click(function() {
		for (i=0; i<$('#table .delid').size(); i++) {
			$('#table .delid').eq(i).attr('checked',true);
		}
		$('#table .indel').attr('checked',true);
	});
	//全不选
	$('#page .unselbox').click(function() {
		for (i=0; i<$('#table .delid').size(); i++) {
			$('#table .delid').eq(i).attr('checked',false);
		}
		$('#table .indel').attr('checked',false);
	});
	//反选
	$('#page .anti').click(function() {
		for (i=0; i<$('#table .delid').size(); i++) {
			if (!$('#table .delid').eq(i).attr('checked')==true) {
				$('#table .delid').eq(i).attr('checked',true);
			}else {
				$('#table .delid').eq(i).attr('checked',false);
			}
		}
	});
}
$(window).resize(function() {
	win();
});
$(function() {
    win();
	shadow();
	funbox();
	lhide();
});