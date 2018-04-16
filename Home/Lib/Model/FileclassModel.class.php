<?php
class FileclassModel extends RelationModel {
	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
		array('ID','number','ID号参数获取失败',0,'',2),									//在更新数据时验证ID是否正确
		array('ClassName','require','请输入目录名称！'),									//验证模块名称
		array('Sid','number','目录ID获取失败',0,''),										//验证目录ID
		array('Sortid','number','请填写正确的排序ID',0,''),
		array('Description','0,50','描述请在50个字符以内！',0,'length'),
		array('Status','number','状态获取正确错误'),
	);
	//自动完成
	protected $_auto = array ( 
		array('Dtime','Dtime',1,'callback'),
	);
	//添加当前时间
	protected function Dtime() {
		return date('Y-m-d H:i:s');
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