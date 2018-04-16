<?php
//下拉菜单类
class DmenuAction extends CommonAction {
	//下拉菜单
	public function index() {
		parent::userauth2(60);
		$sid = I('get.sid','');
		$menu = D('Dmenu');
		import('ORG.Util.Page');						// 导入分页类
		if ($sid!='') {
			$where['Sid'] = $sid;
		}else {
			$where['Sid'] = 0;
		}
		$count = $menu->where($where)->count();			//总记录数
		$Page = new Page($count,15);					//实例化分页类 传入总记录数和每页显示的记录数
		$Page->setConfig('header','条记录');
		$Page->setConfig('prev','<img src="__IMAGE__/prev.gif" border="0" title="上一页" />');
		$Page->setConfig('next','<img src="__IMAGE__/next.gif" border="0" title="下一页" />');
		$Page->setConfig('first','<img src="__IMAGE__/first.gif" border="0" title="第一页" />');
		$Page->setConfig('last','<img src="__IMAGE__/last.gif" border="0" title="最后一页" />');
		$show = $Page->show();							//分页显示输出
		$volist = $menu->relation(true)->where($where)->order('Sortid asc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$list = $menu->where('Sid = 0')->order('Sortid asc')->select();
		$this->assign('volist',$volist);
		$this->assign('list',$list);
		$this->assign('sid',$sid);
		$this->assign('page',$show);					//输出分页
		$this->co = $count;
		$this->display('System/dmenu');
	}
	//添加
	public function dmenuadd() {
		parent::win_userauth(61);
		$dmenu = M('dmenu');
		$list=$dmenu->where('Sid = 0')->order('Sortid asc')->getField('ID,Sid,MenuName,Sortid');
		$this->assign('list',$list);
		$this->display('System/dmenuadd');
	}
	//添加处理
	public function dmenuadd_do() {
		parent::userauth(61);
		if ($this->isAjax()) {
			$data['Sid'] = I('post.sid','');
			$data['MenuName'] = I('post.menuname','','htmlspecialchars');
			$data['Description'] = I('post.description','','htmlspecialchars');
			$data['Sortid'] = I('post.sortid','');
			$dmenu = D('Dmenu');
			if ($dmenu->create($data)) {
				$dmenu->add();
				parent::operating(__ACTION__,0,'新增成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'新增失败：'.$dmenu->getError());
				R('Public/errjson',array($dmenu->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			$this->error('非法请求');
		}
	}
	//修改
	public function dmenuedit() {
		parent::win_userauth(62);
		$id = I('get.id','');
		if ($id=='' || !is_numeric($id)) {
			parent::operating(__ACTION__,1,'参数错误');
			$this->content='参数ID类型错误，请关闭本窗口';
			exit($this->display('Public:err'));
		}
		$id=intval($id);
		$dmenu=M('dmenu');
		//获取分类信息
		$data=array('ID' => $id);
		if ($result=$dmenu->where($data)->find()) {
			$this->result=$result;
			$list=$dmenu->where('Sid = 0')->order('Sortid asc')->getField('ID,Sid,MenuName,Sortid');
			$this->assign('list',$list);
			$this->assign('result',$result);
			$this->display('System/dmenuedit');
		}else {
			parent::operating(__ACTION__,1,'没有找到数据');
			$this->content='没有找到相关数据，请关闭本窗口';
			$this->display('Public:err');
		}
	}
	//修改处理
	public function dmenuedit_do() {
		parent::userauth(62);
		if ($this->isAjax()) {
			$data['ID'] = I('post.id','');
			$data['Sid'] = I('post.sid','');
			$data['MenuName'] = I('post.menuname','','htmlspecialchars');
			$data['Description'] = I('post.description','','htmlspecialchars');
			$data['Sortid'] = I('post.sortid','');
			$dmenu = D('Dmenu');
			if ($dmenu->create($data)) {
				$dmenu->save();
				parent::operating(__ACTION__,0,'更新成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'更新失败'.$dmenu->getError());
				R('Public/errjson',array($dmenu->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			$this->error('非法请求');
		}
	}
	//删除
	public function del() {
		//验证用户权限
		parent::userauth(63);
		if ($this->isAjax()) {
			if (!$delid=explode(',',$this->_post('delid'))) {
				R('Public/errjson',array('请选中后再删除'));
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			$id=join(',',$delid);
			$dmenu=M('dmenu');
			if ($dmenu->delete("$id")) {
				parent::operating(__ACTION__,0,'删除成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'删除失败：'.$dmenu->getError());
				R('Public/errjson',array('删除失败'));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
}
?>