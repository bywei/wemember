<?php
//角色类
class RoleAction extends CommonAction {
	public function index() {
		parent::userauth2(7);
		$Role = M('role');
		$volist = $Role->order('Dtime')->getField('ID,Rolename,Description,Status,Dtime');
		$this->assign('volist',$volist);
		$co = count($volist);
		$this->co=$co;
		$this->display();
	}
	public function roleadd() {
		parent::win_userauth(8);
		$co = M('competence');
		$volist = $co->where('Sid=0 AND Status=0')->order('Dtime asc')->getField('ID,Cname,Status');
		$slist = $co->where('Sid<>0 AND Status=0')->order('Dtime asc')->getField('ID,Sid,Cname,Status');
		$this->volist = $volist;
		$this->slist = $slist;
		$this->display('add');
	}
	//新增角色
	public function roleadd_do() {
		//验证用户权限
		parent::userauth(8);
		$data=array();
		if ($this->isAjax()) {
			$data['Rolename'] = I('post.rolename','','htmlspecialchars');
			$data['Description'] = I('post.description','','htmlspecialchars');
			$data['Competence'] = I('post.comp','');
			$data['Status'] = I('post.status','');
			$role=D('Role');
			if ($role->create($data)) {
				$role->add();
				parent::operating(__ACTION__,0,'新增成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'新增失败'.$role->getError());
				R('Public/errjson',array($role->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//修改角色信息
	public function roleedit() {
		parent::win_userauth(9);
		$id = I('get.id','');
		if ($id == '' || !is_numeric($id)) {
			parent::operating(__ACTION__,1,'参数错误');
			$this->content = '参数ID类型错误，请关闭本窗口';
			exit($this->display('Public:err'));
		}
		$id = intval($id);
		$role = M('role');
		if ($result = $role->where("ID = $id")->find()) {
			$this->result = $result;
			//获取权限数据
			$co = M('competence');
			$volist = $co->where('Sid=0 AND Status=0')->order('Dtime asc')->getField('ID,Cname,Status');
			$slist = $co->where('Sid<>0 AND Status=0')->order('Dtime asc')->getField('ID,Sid,Cname,Status');
			$this->volist = $volist;
			$this->slist = $slist;
			$this->display('edit');
		}else {
			parent::operating(__ACTION__,1,'数据不存在');
			$this->content = '没有找到相关数据，请关闭本窗口';
			$this->display('Public:err');
		}
	}
	//修改处理
	public function roleedit_do() {
		//验证用户权限
		parent::userauth(9);
		$data=array();
		if ($this->isAjax()) {
			$data['ID'] = I('post.id','');
			$data['Rolename'] = I('post.rolename','','htmlspecialchars');
			$data['Description'] = I('post.description','','htmlspecialchars');
			$data['Competence'] = I('post.comp','');
			$data['Status'] = I('post.status','');
			$role = D('Role');
			$where = array('ID'=>$data['ID']);
			if ($role->create($data)) {
				$role->save();
				//修改所有属于该角色的用户权限
				$user = M('user');
				$user->where('Roleid='.$data['ID'])->setField('Competence',$data['Competence']);
				parent::operating(__ACTION__,0,'更新成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'更新失败：'.$role->getError());
				R('Public/errjson',array($role->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//删除数据
	public function roledel() {
		//验证用户权限
		parent::userauth(10);
		//判断是否是ajax请求
		if ($this->isAjax()) {
			if (I('post.post','')=='ok') {
				$id = I('post.id','');
				if ($id == '' || !is_numeric($id)) {
					parent::operating(__ACTION__,1,'参数错误');
					R('Public/errjson',array('参数ID类型错误'));
				}else {
					$id = intval($id);
					if ($id==1) {
						parent::operating(__ACTION__,1,'不能删除系统默认角色');
						R('Public/errjson',array('不能删除此角色'));
					}
					$role = M('role');
					$where = array('ID'=>$id);
					if ($role->where($where)->getField('ID')) {
						$role->where($where)->delete();
						parent::operating(__ACTION__,0,'删除成功');
						R('Public/errjson',array('ok'));
					}else {
						parent::operating(__ACTION__,1,'数据不存在：'.$id);
						R('Public/errjson',array('数据不存在'));
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
}
?>