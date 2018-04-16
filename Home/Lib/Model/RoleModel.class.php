<?php
class RoleModel extends Model {
	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
		array('ID','number','ID号参数获取失败',0,'',2),							//在更新数据时验证ID是否正确
		array('Rolename','require','请输入角色名称！'),							//检查角色是否为空
		array('Rolename','','帐号名称已经存在！',0,'unique',1),						//检查角色名称是否存在，仅在新增时验证
		array('Rolename','2,20','角色名称请在20个字符以内！',0,'length'),			//将角色名称限定在20个字符以内
		array('Description','0,50','角色描述请在50个字符以内！',0,'length'),			//将描述限定在50个字符以内
		array('Competence','require','请给角色授权！'),							//将描述限定在50个字符以内
		array('Competence','2,255','角色授权超出范围！',0,'length',3),				//将字段长度限定在255个字以内
		array('Status','number','状态获取值不正确')								//获取状态值
	);
	//自动完成
	protected $_auto = array ( 
		array('Dtime','Dtime',1,'callback'),
	);
	//添加当前时间
	protected function Dtime() {
		return date('Y-m-d H:i:s');
	}
}
?>