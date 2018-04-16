<?php
class CompetenceModel extends Model {
	//自动验证
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
		array('ID','number','ID号参数获取失败',0,'',2),							//在更新数据时验证ID是否正确
		array('Sid','number','所属目录ID不正确',0,''),								//验证所属目录是否正确
		array('Cname','require','请输入权限名称！'),								//检查权限是否为空
		array('Cname','2,20','权限名称请在20个字符以内！',0,'length'),				//将权限名称限定在20个字符以内
		array('Description','0,50','权限描述请在50个字符以内！',0,'length'),			//将描述限定在50个字符以内
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