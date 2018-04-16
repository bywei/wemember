<?php
class NewsclassModel extends RelationModel {
	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
		array('ID','number','ID号参数获取失败',0,'',2),								//在更新数据时验证ID是否正确
		array('ClassName','require','请输入分类名称！'),								//检查名称是否为空
		array('ClassName','2,20','分类名称请在20个字符以内！',0,'length'),				//名称限定在20个字符以内
		array('ClassName','','分类名称已经存在！',0,'unique',3),								// 在新增的时候验证name字段是否唯一
		array('Description','0,50','权限描述请在50个字符以内！',0,'length'),				//将描述限定在50个字符以内
	);
	//自动完成
	protected $_auto = array ( 
		array('Dtime','Dtime',1,'callback'),
		array('Uid','userid',3,'callback'),
	);
	//添加当前时间
	protected function Dtime() {
		return date('Y-m-d H:i:s');
	}
	//添加当前管理员
	protected function userid() {
		return $_SESSION['ThinkUser']['ID'];
	}
	//关联查询
	protected $_link = array(
		'User' => array(
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'User',						//关联的模型名
			'foreign_key'=>'Uid',						//关联的外键名称
			'mapping_name'=>'user',						//映射的名称
			'mapping_fields'=>'Username',				//关联的字段
			'as_fields'=>'Username'						//映射成数据对像中的某个字段
		),
	);
}
?>