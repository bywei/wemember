<?php
//登录日志类
class LoginlogAction extends CommonAction {
	public function index() {
		parent::userauth2(19);
		$keyword = I('get.keyword','','htmlspecialchars');
		$log = D('Loginlog');
		import('ORG.Util.Page');						// 导入分页类
		if ($keyword!='') {
			$where['Uid']=$keyword;
		}
		$count = $log->where($where)->count();			//总记录数
		$Page = new Page($count,15);					//实例化分页类 传入总记录数和每页显示的记录数
		$Page->setConfig('header','条记录');
		$Page->setConfig('prev','<img src="__IMAGE__/prev.gif" border="0" title="上一页" />');
		$Page->setConfig('next','<img src="__IMAGE__/next.gif" border="0" title="下一页" />');
		$Page->setConfig('first','<img src="__IMAGE__/first.gif" border="0" title="第一页" />');
		$Page->setConfig('last','<img src="__IMAGE__/last.gif" border="0" title="最后一页" />');
		$show = $Page->show();							//分页显示输出
		$volist = $log->relation(true)->where($where)->order('Dtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('volist',$volist);
		$this->assign('page',$show);					//输出分页
		$this->assign('keyword',$keyword);
		$this->co = $count;
		$this->display('Public:loginlog');
	}
	//删除数据
	public function del() {
		//验证用户权限
		parent::userauth(20);
		//判断是否是ajax请求
		if ($this->isAjax()) {
			if (I('post.post','') == 'ok') {
				$id = I('post.id','');
				if ($id=='' || !is_numeric($id)) {
					R('Public/errjson',array('参数ID类型错误'));
				}else {
					$id=intval($id);
					$log=M('loginlog');
					$where=array('ID'=>$id);
					if ($log->where($where)->getField('ID')) {
						$log->where($where)->delete();
						parent::operating(__ACTION__,0,'登录日志删除成功');
						R('Public/errjson',array('ok'));
					}else {
						parent::operating(__ACTION__,1,'数据不存在');
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
	//批量删除
	public function indel() {
		//验证用户权限
		parent::userauth(20);
		if ($this->isAjax()) {
			if (!$delid=explode(',',$this->_post('delid'))) {
				R('Public/errjson',array('请选中后再删除'));
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			$id=join(',',$delid);
			$log=M('loginlog');
			if ($log->delete("$id")) {
				parent::operating(__ACTION__,0,'登录日志删除成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'登录日志删除失败');
				R('Public/errjson',array('删除失败'));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
}
?>