<?php
class IpModel extends RelationModel {
	protected $_validate = array(
		array('Ip','','要封禁的IP已经在黑名单中了',0,'unique',3),			//验证IP是否已经存在
		array('Ip','/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/i','请填写正确的IP地址',0,'regex'),
		//array('StartTime','/^(\d{4})\-(\d{2})\-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/','请选择正确的日期',2,'regex',3),
		//array('EndTime','/^(\d{4})\-(\d{2})\-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/','请选择正确的日期',2,'regex',3),
		array('Status','number','封禁值获取出错',0),
		array('Description','0,50','请将描述控制在50个字符以内',0,'length',3)
	);
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