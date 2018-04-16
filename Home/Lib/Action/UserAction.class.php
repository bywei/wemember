<?php
//用户名类
class UserAction extends CommonAction {
	public function index() {
		parent::userauth2(2);
		$keyword = I('get.keyword','','htmlspecialchars');
		$user = D('User');
		import('ORG.Util.Page');						// 导入分页类
		$where['Username']=$keyword;
		$count = $user->where($where)->count();			//总记录数
		$Page = new Page($count,15);					//实例化分页类 传入总记录数和每页显示的记录数
		$Page->setConfig('header','条记录');
		$Page->setConfig('prev','<img src="__IMAGE__/prev.gif" border="0" title="上一页" />');
		$Page->setConfig('next','<img src="__IMAGE__/next.gif" border="0" title="下一页" />');
		$Page->setConfig('first','<img src="__IMAGE__/first.gif" border="0" title="第一页" />');
		$Page->setConfig('last','<img src="__IMAGE__/last.gif" border="0" title="最后一页" />');
		$show = $Page->show();							//分页显示输出
		$volist = $user->relation(true)->where($where)->order('Dtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('volist',$volist);
		$this->assign('page',$show);					//输出分页
		$this->assign('keyword',$keyword);
		$this->co = $count;
		$this->display();
	}
	public function useradd() {
		parent::win_userauth(3);
		$role=M('role');
		$volist=$role->where('Status=0')->getField('ID,Rolename,Status');
		$this->assign('volist',$volist);
		$this->display('add');
	}
	//添加用户
	public function useradd_do() {
		//验证用户权限
		parent::userauth(3);
		$data=array();
		if ($this->isAjax()) {
			$data['Roleid'] = I('post.roleid','','htmlspecialchars');
			$data['Username'] = I('post.username','','htmlspecialchars');
			$data['Password'] = I('post.password','');
			$data['Email'] = I('post.email','');
			$data['Description'] = I('post.description','','htmlspecialchars');
			$data['Status'] = I('post.status','');
			//自动完成验证与新增
			$user=D('User');
			if ($user->create($data)) {
				$uid=$user->add();
				$role=M('role');
				$r=$role->where('ID='.$data['Roleid'])->getField('Competence');
				//给新用户添加默认权限
				$user->where("ID=$uid")->setField(array('Competence' => $r));
				parent::operating(__ACTION__,0,'新增用户：'.$data['Username']);
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'新增失败：'.$data['Username']);
				R('Public/errjson',array($user->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//修改界面
	public function useredit() {
		parent::win_userauth(4);
		$id = I('get.id','');
		if ($id=='' || !is_numeric($id)) {
			parent::operating(__ACTION__,1,'参数错误');
			$this->content='参数ID类型错误，请关闭本窗口';
			exit($this->display('Public:err'));
		}
		$id=intval($id);
		$role=M('role');
		$user=M('user');
		//获取分类信息
		$volist=$role->where('Status=0')->order('Dtime')->getField('ID,Rolename,Status');
		$data=array('ID' => $id);
		if ($result=$user->where($data)->find()) {
			$this->result=$result;
			$this->volist=$volist;
			//获取权限数据
			$co=M('competence');
			$aulist=$co->where('Sid=0 AND Status=0')->order('Dtime asc')->getField('ID,Cname,Status');
			$slist=$co->where('Sid<>0 AND Status=0')->order('Dtime asc')->getField('ID,Sid,Cname,Status');
			$this->aulist=$aulist;
			$this->slist=$slist;
			$this->display('edit');
		}else {
			parent::operating(__ACTION__,1,'数据不存在');
			$this->content='没有找到相关数据，请关闭本窗口';
			$this->display('Public:err');
		}
	}
	//用户修改处理
	public function useredit_do() {
		//验证用户权限
		parent::userauth(4);
		$data=array();
		if ($this->isAjax()) {
			$data['ID'] = I('post.id','');
			$data['Roleid'] = I('post.roleid','');
			$data['Username'] = I('post.username','','htmlspecialchars');
			$data['Password'] = I('post.password','');
			$data['Email'] = I('post.email','');
			$data['Description'] = I('post.description','','htmlspecialchars');
			$data['Competence'] = I('post.comp','');
			$data['Status'] = I('post.status');
			$user=D('User');
			if ($user->create($data)) {
				$user->save();
				parent::operating(__ACTION__,0,'更改用户资料：'.$data['Username']);
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'更新失败：'.$data['Username']);
				R('Public/errjson',array($user->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求：');
			R('Public/errjson',array('非法请求'));
		}
	}
	//个别信息修改
	public function uedit() {
		parent::win_userauth(18);
		$id = $_SESSION['ThinkUser']['ID'];
		$user=M('user');
		$data=array('ID' => $id);
		if ($result=$user->where($data)->find()) {
			$this->result=$result;
			//获取权限数据
			$this->display('uedit');
		}else {
			parent::operating(__ACTION__,1,'没有找到相关数据：'.$id);
			$this->content='没有找到相关数据，请关闭本窗口';
			$this->display('Public:err');
		}
	}
	//个别信息修改处理
	public function uedit_do() {
		//验证用户权限
		parent::userauth(18);
		$data=array();
		if ($this->isAjax()) {
			$data['ID'] = $_SESSION['ThinkUser']['ID'];
			$password = I('post.password','');
			$data['Email'] = I('post.email','');
			if (strlen($password)<6 || strlen($password)>18) {
				R('Public/errjson',array('请输入6～18位数的安全密码'));
			}
			if (!preg_match('/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/i',$data['Email'])) {
				R('Public/errjson',array('请输入正确的邮箱地址'));
			}
			$data['Password']=R('Public/sha1pow',array($password));
			$user=M('user');
			$where=array('ID' => $data['ID']);
			if ($user->where($where)->setField($data)) {
				parent::operating(__ACTION__,0,'修改密码成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'更新失败：'.$user->getError());
				R('Public/errjson',array($user->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//删除数据
	public function userdel() {
		//验证用户权限
		parent::userauth(5);
		//判断是否是ajax请求
		if ($this->isAjax()) {
			if (I('post.post','')=='ok') {
				$id=I('post.id','');
				if ($id=='' || !is_numeric($id)) {
					parent::operating(__ACTION__,1,'参数错误');
					R('Public/errjson',array('参数ID类型错误'));
				}else {
					$id=intval($id);
					if ($id==1) {
						parent::operating(__ACTION__,1,'不能删除系统默认用户');
						R('Public/errjson',array('不能删除系统默认用户'));
					}
					$user=M('user');
					$where=array('ID'=>$id);
					if ($user->where($where)->getField('ID')) {
						$user->where($where)->delete();
						parent::operating(__ACTION__,0,'删除用户ID为：'.$id.'的数据');
						R('Public/errjson',array('ok'));
					}else {
						parent::operating(__ACTION__,1,'删除用户失败：'.$user->getError());
						R('Public/errjson',array($user->getError()));
					}
				}
			}else {
				parent::operating(__ACTION__,1,'非法请求');
				R('Public/errjson',array('非法请求'));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//批量删除
	public function in_user_del() {
		//验证用户权限
		parent::userauth(5);
		if ($this->isAjax()) {
			if (!$delid=explode(',',I('post.delid',''))) {
				R('Public/errjson',array('请选中后再删除'));
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			if (in_array(1,$delid)) {
				R('Public/errjson',array('不能删除ID号为1的数据'));
			}
			$id=join(',',$delid);
			$user=M('user');
			if ($user->delete("$id")) {
				parent::operating(__ACTION__,0,'批量删除ID为：'.$id.'的数据');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'批量删除用户失败：'.$user->getError());
				R('Public/errjson',array($user->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
}
?>