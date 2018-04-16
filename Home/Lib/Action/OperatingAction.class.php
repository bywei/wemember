<?php
//操作日志类
class OperatingAction extends CommonAction {
	public function index() {
		parent::userauth2(82);
		$keyword = I('get.keyword','','htmlspecialchars');
		$Url = I('get.Url','','htmlspecialchars');
		$Ip = I('get.Ip','','htmlspecialchars');
		$Uid = I('get.Uid','','htmlspecialchars');
		$Status = I('get.Status','','htmlspecialchars');
		$operating = D('Operating');
		import('ORG.Util.Page');						// 导入分页类
		if ($keyword!='') {
			$where['Description'] = $keyword;
		}else {
			$where['Description'] = '';
		}
		if ($Url!='') {
			$where['Url'] = $Url;
		}
		if ($Ip!='') {
			$where['Ip'] = $Ip;
		}
		if ($Uid!='') {
			$where['Uid'] = intval($Uid);
		}
		if ($Status!='') {
			$where['Status'] = intval($Status);
		}
		$count = $operating->where($where)->count();			//总记录数
		$Page = new Page($count,15);					//实例化分页类 传入总记录数和每页显示的记录数
		$Page->setConfig('header','条记录');
		$Page->setConfig('prev','<img src="__IMAGE__/prev.gif" border="0" title="上一页" />');
		$Page->setConfig('next','<img src="__IMAGE__/next.gif" border="0" title="下一页" />');
		$Page->setConfig('first','<img src="__IMAGE__/first.gif" border="0" title="第一页" />');
		$Page->setConfig('last','<img src="__IMAGE__/last.gif" border="0" title="最后一页" />');
		$show = $Page->show();							//分页显示输出
		$volist = $operating->relation(true)->where($where)->order('Dtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//标注时间和颜色
		if (count($volist) > 0) {
			$public = A('Public');
			for ($i=0; $i<count($volist); $i++) {
				//将搜索的标为红色
				$volist[$i]['Dtime'] = $public->Beautifytime($volist[$i]['Dtime']);
				if ($keyword!='') {
					$volist[$i]['Description'] = str_replace($keyword,'<font>'.$keyword.'</font>',$volist[$i]['Description']);		//描述
				}
			}
		}
		if (isset($where['Status'])) {
			$Status = $where['Status'];
		}else {
			$Status = 2;
		}
		$this->assign('volist',$volist);
		$this->assign('page',$show);					//输出分页
		$this->assign('keyword',$keyword);
		$this->assign('Status',$Status);
		$this->co = $count;
		$this->display('Public:operatinglog');
	}
	//删除数据
	public function del() {
		//验证用户权限
		parent::userauth(83);
		//判断是否是ajax请求
		if ($this->isAjax()) {
			if (I('post.post','') == 'ok') {
				$id = I('post.id','');
				if ($id=='' || !is_numeric($id)) {
					R('Public/errjson',array('参数ID类型错误'));
				}else {
					$id=intval($id);
					$operating=M('operating');
					$where=array('ID'=>$id);
					if ($operating->where($where)->getField('ID')) {
						$operating->where($where)->delete();
						R('Public/errjson',array('ok'));
					}else {
						R('Public/errjson',array('数据不存在'));
					}
				}
			}else {
				R('Public/errjson',array('非法请求'));
			}
		}else {
			R('Public/errjson',array('非法请求'));
		}
	}
	//批量删除
	public function indel() {
		//验证用户权限
		parent::userauth(83);
		if ($this->isAjax()) {
			if (!$delid=explode(',',$this->_post('delid'))) {
				R('Public/errjson',array('请选中后再删除'));
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			$id=join(',',$delid);
			$operating=M('operating');
			if ($operating->delete("$id")) {
				R('Public/errjson',array('ok'));
			}else {
				R('Public/errjson',array('删除失败'));
			}
		}else {
			R('Public/errjson',array('非法请求'));
		}
	}
}
?>