<?php
//联系人表
class ContactModel extends RelationModel {
	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
		array('ID','number','ID号参数获取失败',0,'',2),									//在更新数据时验证ID是否正确
		array('Cid','number','客户资料ID获取失败',0),	
		array('ContactName','require','请填写联系人'),
		array('ContactName','1,30','联系人请在30个字符以内！',0,'length'),
		array('Sex','1,4','性别必须在4个字符以内！',0,'length'),
		array('Qq','0,30','QQ号码请在30个字符以内！',0,'length'),
		array('Skype','0,30','Skype请在30个字符以内！',0,'length'),
		array('Alww','0,30','阿里旺旺请在30个字符以内！',0,'length'),
		array('Phone','0,30','手机号码请在30个字符以内！',0,'length'),
		array('Tel','0,20','电话号码请在20个字符以内！',0,'length'),
		array('Fax','0,20','传真请在20个字符以内！',0,'length'),
		array('Email','0,40','Email请在40个字符以内！',0,'length'),
		array('Email','Email','请填写正确的Email！',2),
		array('Content','0,1000','备注请在1000个字符以内！',0,'length'),
		array('Post','number','职位必须是数字'),
		array('Qq','number','QQ号码必须是数字',2),
	);
	//自动完成
	protected $_auto = array ( 
		array('Dtime','Dtime',1,'callback'),
		array('FinalTime','Dtime',3,'callback'),
		array('Uid','Uid',1,'callback'),
	);
	//添加当前时间
	protected function Dtime() {
		return date('Y-m-d H:i:s');
	}
	//添加用户ID
	protected function Uid() {
		return $_SESSION['ThinkUser']['ID'];
	}
	//关联查询
	protected $_link = array(
		'Client' => array(
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'Client',
			'foreign_key'=>'Cid',
			'mapping_name'=>'CompanyName',
			'mapping_fields'=>'CompanyName,Recycle',
			'as_fields'=>'CompanyName,Recycle'
		),
	);
}
?>