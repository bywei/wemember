<?php
class NewsModel extends RelationModel {
	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
		array('ID','number','ID号参数获取失败',0,'',2),		
		array('Sid','require','分类ID获取失败'),								
		array('Sid','number','分类ID获取失败'),
		array('Title','require','请填写标题'),
		array('Title','1,80','标题请在80个字符以内！',0,'length'),
		array('Sortid','require','请填写排序ID'),								
		array('Sortid','number','排序ID必须是数字'),
		array('Description','0,200','描述请在200个字符以内！',0,'length'),		
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
		'Newsclass' => array(
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'Newsclass',						
			'foreign_key'=>'Sid',
			'mapping_name'=>'ClassName',
			'mapping_fields'=>'ClassName',
			'as_fields'=>'ClassName'
		),
	);
}
?>