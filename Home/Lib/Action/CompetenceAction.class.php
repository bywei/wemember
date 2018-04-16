<?php
//权限类
class CompetenceAction extends CommonAction {
	public function index() {
		parent::userauth2(12);
		$co = M('competence');
		$keyword = I('get.keyword','','htmlspecialchars');
		import('ORG.Util.Page');		// 导入分页类
		$where['Sid&Cname']=array('0',array('LIKE',"%$keyword%"),'_multi'=>true);		//多条件查询
		$count = $co->where($where)->count();			//总记录数
		$Page = new Page($count,4);		//实例化分页类 传入总记录数和每页显示的记录数
		$Page->setConfig('header','条记录');
		$Page->setConfig('prev','<img src="__IMAGE__/prev.gif" border="0" title="上一页" />');
		$Page->setConfig('next','<img src="__IMAGE__/next.gif" border="0" title="下一页" />');
		$Page->setConfig('first','<img src="__IMAGE__/first.gif" border="0" title="第一页" />');
		$Page->setConfig('last','<img src="__IMAGE__/last.gif" border="0" title="最后一页" />');
		$show = $Page->show();			//分页显示输出
		
		$volist=$co->where($where)->order('Dtime')->limit($Page->firstRow.','.$Page->listRows)->select();
		$sidlist=$co->where('Sid<>0')->order('Dtime')->select();
		
		$this->assign('volist',$volist);
		$this->assign('sidlist',$sidlist);
		$this->assign('page',$show);
		$this->assign('keyword',$keyword);
		$this->co=$count;
		$this->display();
	}
	public function cadd() {
		parent::win_userauth(13);
		$co=M('competence');
		$volist=$co->where('Sid=0 AND Status=0')->order('Dtime')->getField('ID,Cname,Status');
		$this->volist=$volist;
		$this->display('add');
	}
	//新增权限
	public function cadd_do() {
		//验证用户权限
		parent::userauth(13);
		$data=array();
		//判断是否是ajax请求
		if ($this->isAjax()) {
			$data['Sid'] = I('post.sid','');
			$data['Cname'] = I('post.cname','','htmlspecialchars');
			$data['Description'] = I('post.description','','htmlspecialchars');
			$data['Status'] = I('post.status','');
			$co=D('Competence');
			//自动创建并验证数据
			if ($co->create($data)) {
				$co->add();
				parent::operating(__ACTION__,0,'新增成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'新增失败'.$co->getError());
				R('Public/errjson',array($co->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//修改权限信息
	public function cedit() {
		parent::win_userauth(14);
		$id = I('get.id','');
		if ($id=='' || !is_numeric($id)) {
			$this->content='参数ID类型错误，请关闭本窗口';
			exit($this->display('Public:err'));
		}
		$id=intval($id);
		$co=M('competence');
		//获取分类信息
		$volist=$co->where('Sid=0 AND Status=0')->order('Dtime')->getField('ID,Cname,Status');
		$data=array('ID' => $id);
		if ($result=$co->where($data)->find()) {
			$this->result=$result;
			$this->volist=$volist;
			$this->display('edit');
		}else {
			parent::operating(__ACTION__,1,'没有找到相关数据：'.$id);
			$this->content='没有找到相关数据，请关闭本窗口';
			$this->display('Public:err');
		}
	}
	//修改处理
	public function cedit_do() {
		//验证用户权限
		parent::userauth(14);
		$data=array();
		//判断是否是ajax请求
		if ($this->isAjax()) {
			$data['ID'] = I('post.id','');
			$data['Sid'] = I('post.sid','');
			$data['Cname'] = I('post.cname','','htmlspecialchars');
			$data['Description'] = I('post.description','','htmlspecialchars');
			$data['Status'] = I('post.status','');
			//自动创建数据及完成验证
			$co=D('Competence');
			if ($co->create($data)) {
				$co->save();
				parent::operating(__ACTION__,0,'更新成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'更新失败'.$co->getError());
				R('Public/errjson',array($co->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//删除数据
	public function cdel() {
		//验证用户权限
		parent::userauth(15);
		//判断是否是ajax请求
		if ($this->isAjax()) {
			if (I('post.post','') == 'ok') {
				$id = I('post.id','');
				if ($id=='' || !is_numeric($id)) {
					parent::operating(__ACTION__,1,'参数错误');
					R('Public/errjson',array('参数ID类型错误'));
				}else {
					$id=intval($id);
					$role=M('competence');
					$where=array('ID'=>$id,'Sid'=>$id,'_logic'=>'OR');
					if ($role->where("ID=$id")->getField('ID')) {
						$role->where($where)->delete();
						parent::operating(__ACTION__,0,'删除成功');
						R('Public/errjson',array('ok'));
					}else {
						parent::operating(__ACTION__,1,'删除失败');
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