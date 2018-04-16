<?php
class ModuleModel extends RelationModel {
	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
		array('ID','number','ID号参数获取失败',0,'',2),									//在更新数据时验证ID是否正确
		array('Sid','number','目录ID获取失败',0,''),										//验证目录ID
		array('ModuleName','require','请输入模块名称！'),									//验证模块名称
		array('ModuleName','2,20','模块名称请控制在20个字符以内！',0,'length'),
		array('ModuleImg','require','请选择菜单图标！'),									//验证图标
		array('Status','number','状态获取值不正确'),										//获取状态值
		array('Msort','number','请填写正确的排序ID'),
		array('Description','0,50','角色描述请在50个字符以内！',0,'length'),					//将描述限定在50个字符以内
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
			'class_name'=>'User',						//关联的模型名
			'foreign_key'=>'Uid',						//关联的外键名称
			'mapping_name'=>'user',						//映射的名称
			'mapping_fields'=>'Username',				//关联的字段
			'as_fields'=>'Username'						//映射成数据对像中的某个字段
		),
	);
}
?>