<?php
class UserModel extends RelationModel {
	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
		array('ID','number','ID号参数获取失败',0,'',2),									//在更新数据时验证ID是否正确
		array('Roleid','number','角色ID获取失败',0,''),									//在更新数据时验证ID是否正确
		array('Username','require','请输入用户名称！'),									//检查用户名是否为空
		array('Username','','此用户名已经存在！',0,'unique',3),								//检查用户名称是否存在，仅在新增时验证
		array('Username','2,20','用户名称请在20个字符以内！',0,'length'),					//将用户名称限定在20个字符以内
		array('Username','/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{2,16}$/u','请输入合法的用户名！',0,'regex'),		//判断用户名是否合法
		array('Password','6,18','请输入6~18位数的安全密码！',0,'length',1),					//验证密码长度
		array('Password','6,18','请输入6~18位数的安全密码！',2,'length',2),					//修改时如果密码不为空则进行判断
		array('Email','email','请填写正确的邮箱！'),										//验证邮箱的有效性
		array('Description','0,50','角色描述请在50个字符以内！',0,'length'),					//将描述限定在50个字符以内
		array('Competence','require','请给角色授权！'),									//将描述限定在50个字符以内
		array('Competence','2,255','角色授权超出范围！',0,'length',3),						//将字段长度限定在255个字以内
		array('Status','number','状态获取值不正确')										//获取状态值
	);
	//自动完成
	protected $_auto = array ( 
		array('Dtime','Dtime',1,'callback'),
		array('Password','sha1pow',1,'callback'),
		array('Password','sha2pow',2,'callback')
	);
	//添加当前时间
	protected function Dtime() {
		return date('Y-m-d H:i:s');
	}
	//新增时加密密码
	protected function sha1pow($Password) {
		return sha1(md5($Password));
	}
	//修改时加密密码
	protected function sha2pow($Password) {
		if ($Password!='') {
			return sha1(md5($Password));
		}else {
			return false;
		}
	}
	//关联查询
	protected $_link = array(
		'Role' => array(
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'Role',
			'foreign_key'=>'Roleid',
			'mapping_name'=>'role',
			'mapping_fields'=>'Rolename',
			'as_fields'=>'Rolename'
		),
	);
}
?>