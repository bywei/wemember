<?php
//下拉菜单类
class DmenuModel extends RelationModel {
	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
		array('ID','number','ID号参数获取失败',0,'',2),									//在更新数据时验证ID是否正确
		array('Sid','number','目录ID获取失败',0,''),			
		array('MenuName','require','请输入名称！'),								
		array('MenuName','1,50','名称请控制在50个字符以内！',0,'length'),						
		array('Description','0,100','描述请在100个字符以内！',0,'length'),
		array('Sortid','number','请填写正确的排序ID'),
	);
	//自动完成
	protected $_auto = array ( 
		array('Dtime','Dtime',1,'callback'),
		array('Uid','uid',3,'callback'),
	);
	//添加当前时间
	protected function Dtime() {
		return date('Y-m-d H:i:s');
	}
	//管理员ID
	protected function uid() {
		return $_SESSION['ThinkUser']['ID'];
	}
	//关联查询
	protected $_link = array(
		'User' => array(
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'User',
			'foreign_key'=>'Uid',
			'mapping_name'=>'user',
			'mapping_fields'=>'Username',
			'as_fields'=>'Username'
		),
	);
}
?>