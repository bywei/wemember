<?php
//文章类
class NewsAction extends CommonAction {
	public function classindex() {
		parent::userauth2(45);
		$newsclass = D('Newsclass');
		import('ORG.Util.Page');		// 导入分页类
		$count = $newsclass->count();			//总记录数
		$Page = new Page($count,15);		//实例化分页类 传入总记录数和每页显示的记录数
		$Page->setConfig('header','条记录');
		$Page->setConfig('prev','<img src="__IMAGE__/prev.gif" border="0" title="上一页" />');
		$Page->setConfig('next','<img src="__IMAGE__/next.gif" border="0" title="下一页" />');
		$Page->setConfig('first','<img src="__IMAGE__/first.gif" border="0" title="第一页" />');
		$Page->setConfig('last','<img src="__IMAGE__/last.gif" border="0" title="最后一页" />');
		$show = $Page->show();			//分页显示输出
		$volist=$newsclass->relation(true)->order('Dtime')->limit($Page->firstRow.','.$Page->listRows)->select();		
		$this->assign('volist',$volist);
		$this->assign('page',$show);
		$this->co=$count;
		$this->display();
	}
	//添加分类
	public function classadd() {
		parent::win_userauth(46);
		$this->display();
	}
	//添加分类处理
	public function classadd_do() {
		parent::userauth(46);
		if ($this->isAjax()) {
			$data = array();
			$data['ClassName'] = I('post.classname','');
			$data['Aliases'] = I('post.aliases','');
			$data['Description'] = I('post.description');
			$newsclass = D('Newsclass');
			//自动创建并验证数据
			if ($newsclass->create($data)) {
				$newsclass->add();
				parent::operating(__ACTION__,0,'新增成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'新增失败:'.$newsclass->getError());
				R('Public/errjson',array($newsclass->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//修改分类
	public function classedit() {
		parent::win_userauth(47);
		$id = I('get.id','');
		if ($id=='' || !is_numeric($id)) {
			parent::operating(__ACTION__,1,'参数错误');
			$this->content='参数ID类型错误，请关闭本窗口';
			exit($this->display('Public:err'));
		}
		$id=intval($id);
		$newsclass=M('newsclass');
		if ($result=$newsclass->where("ID=$id")->find()) {
			$this->result=$result;
			$this->display();
		}else {
			parent::operating(__ACTION__,1,'数据不存在');
			$this->content='没有找到相关数据，请关闭本窗口';
			$this->display('Public:err');
		}
	}
	//修改分类处理
	public function classedit_do() {
		parent::userauth(47);
		if ($this->isAjax()) {
			$data = array();
			$data['ID'] = I('post.id','');
			$data['ClassName'] = I('post.classname','');
			$data['Aliases'] = I('post.aliases','');
			$data['Description'] = I('post.description');
			$newsclass = D('Newsclass');
			//自动创建并验证数据
			if ($newsclass->create($data)) {
				$newsclass->save();
				parent::operating(__ACTION__,0,'更新成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'更新失败'.$newsclass->getError());
				R('Public/errjson',array($newsclass->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//删除分类
	public function classdel() {
		//验证用户权限
		parent::userauth(48);
		//判断是否是ajax请求
		if ($this->isAjax()) {
			$id = I('post.id','');
			if ($id=='' || !is_numeric($id)) {
				parent::operating(__ACTION__,1,'参数错误');
				R('Public/errjson',array('参数ID类型错误'));
			}else {
				$id=intval($id);
				$newsclass=M('newsclass');
				if ($newsclass->where("ID=$id")->delete()) {
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
	}
	
	//内容管理
	public function news() {
		//验证用户权限
		parent::userauth2(84);
		$sid = I('get.sid','','htmlspecialchars');
		if ($sid!='') {
			$where['Sid'] = intval($sid);
		}
		$news = D('News');
		import('ORG.Util.Page');				// 导入分页类
		$count = $news->where($where)->count();				//总记录数
		$Page = new Page($count,15);				//实例化分页类 传入总记录数和每页显示的记录数
		$Page->setConfig('header','条记录');
		$Page->setConfig('prev','<img src="__IMAGE__/prev.gif" border="0" title="上一页" />');
		$Page->setConfig('next','<img src="__IMAGE__/next.gif" border="0" title="下一页" />');
		$Page->setConfig('first','<img src="__IMAGE__/first.gif" border="0" title="第一页" />');
		$Page->setConfig('last','<img src="__IMAGE__/last.gif" border="0" title="最后一页" />');
		$show = $Page->show();					//分页显示输出
		$volist=$news->relation(true)->where($where)->order('Sortid,Dtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//分类数据
		$newsclass = M('newsclass');
		$clist = $newsclass->order('ID asc')->select();
		$this->assign('volist',$volist);
		$this->assign('clist',$clist);
		$this->assign('page',$show);
		$this->assign('sid',$sid);
		$this->co=$count;
		$this->display();
	}
	//添加内容
	public function add() {
		parent::win_userauth(49);
		$newsclass = M('newsclass');
		$clist = $newsclass->order('Dtime asc')->select();
		$this->assign('clist',$clist);
		//排序ID
		$news = M('News');
		$Sortid = $news->count()+1;
		$this->assign('Sortid',$Sortid);
		$this->display();
	}
	//添加处理
	public function add_do() {
		parent::userauth2(49);
		if ($this->isPost()) {
			$data = array();
			$data['Sid'] = $_POST['Sid'];
			$data['Title'] = htmlspecialchars($_POST['Title']);
			$data['Aliases'] = htmlspecialchars($_POST['Aliases']);
			$data['Sortid'] = $_POST['Sortid'];
			$data['Description'] = htmlspecialchars($_POST['Description']);
			$data['Content'] = $_POST['Content'];
			$news = D('News');
			//自动创建并验证数据
			if ($news->create($data)) {
				$news->add();
				parent::operating(__ACTION__,0,'新增成功');
				$this->success('添加成功',__APP__.'/News/news',3);
			}else {
				parent::operating(__ACTION__,1,'新增失败'.$news->getError());
				$this->error($news->getError());
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			$this->error('非法请求');
		}
	}
	//更新文章
	public function edit() {
		parent::win_userauth(50);
		$id = I('get.id','','htmlspecialchars');
		if ($id=='' || !is_numeric($id)) {
			parent::operating(__ACTION__,1,'参数错误');
			$this->error('参数ID类型错误');
		}
		$id=intval($id);
		$news=M('news');
		if ($result=$news->where("ID=$id")->find()) {
			//分类数据
			$newsclass = M('newsclass');
			$clist = $newsclass->order('ID asc')->select();
			$this->result=$result;
			$this->clist=$clist;
			$this->display();
		}else {
			parent::operating(__ACTION__,1,'数据不存在');
			$this->error('没有找到相关数据');
		}
	}
	//修改处理
	public function edit_do() {
		parent::userauth2(50);
		if ($this->isPost()) {
			$data = array();
			$data['ID'] = $_POST['ID'];
			$data['Sid'] = $_POST['Sid'];
			$data['Title'] = htmlspecialchars($_POST['Title']);
			$data['Aliases'] = htmlspecialchars($_POST['Aliases']);
			$data['Sortid'] = $_POST['Sortid'];
			$data['Description'] = htmlspecialchars($_POST['Description']);
			$data['Content'] = $_POST['Content'];
			$news = D('News');
			//自动创建并验证数据
			if ($news->create($data)) {
				$news->save();
				parent::operating(__ACTION__,0,'更新成功');
				$this->success('修改成功',__APP__.'/News/news',3);
			}else {
				parent::operating(__ACTION__,1,'更新失败：'.$news->getError());
				$this->error($news->getError());
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			$this->error('非法请求');
		}
	}
	//文章详情
	public function article() {
		$id = I('get.id','','htmlspecialchars');
		if ($id =='' && !is_numeric($id)) {
			$this->error('参数错误');
		}
		$id = intval($id);
		$news = D('News');
		$result=$news->relation(true)->where("ID = $id")->find();
		$news->where("ID = $id")->setInc('View');
		//分类数据
		$newsclass = M('newsclass');
		$clist = $newsclass->order('ID asc')->select();
		$this->assign('result',$result);
		$this->assign('clist',$clist);
		$this->display();
	}
	//删除
	public function newsdel() {
		//验证用户权限
		parent::userauth(51);
		//判断是否是ajax请求
		if ($this->isAjax()) {
			$id = I('post.id','');
			if ($id=='' || !is_numeric($id)) {
				parent::operating(__ACTION__,1,'参数错误');
				R('Public/errjson',array('参数ID类型错误'));
			}else {
				$id=intval($id);
				$news=M('news');
				if ($news->where("ID=$id")->delete()) {
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
	}
	//批量删除
	public function newsindel() {
		//验证用户权限
		parent::userauth(51);
		if ($this->isAjax()) {
			if (!$delid=explode(',',I('post.delid',''))) {
				R('Public/errjson',array('请选中后再删除'));
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			$id=join(',',$delid);
			$news=M('news');
			if ($news->delete("$id")) {
				parent::operating(__ACTION__,0,'删除成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'删除失败');
				R('Public/errjson',array('删除失败'));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
}
?>