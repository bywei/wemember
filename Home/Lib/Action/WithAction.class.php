<?php
//跟单类
class WithAction extends CommonAction {
	//跟单类型
	public function with() {
		//权限验证
		parent::userauth2(78);
		$this->display('Client/with');
	}
	public function withajax() {
		$keyword = I('get.keyword','','htmlspecialchars');
		$Cid = I('get.Cid','','htmlspecialchars');
		$with = D('With');
		import('ORG.Util.Page');						// 导入分页类
		if($_SESSION['ThinkUser']['Roleid'] != 1){
			$where['Uid'] = $_SESSION['ThinkUser']['ID'];
		}
		if ($Cid!='' && is_numeric($Cid)) {
			$where['Cid']  = intval($Cid);
		}
		if ($keyword!='') {
			$where['NextTime']  = array('like',"%$keyword%");
		}
		$count = $with->where($where)->count();			//总记录数
		$Page = new Page($count,15);					//实例化分页类 传入总记录数和每页显示的记录数
		$Page->setConfig('header','条记录');
		$Page->setConfig('prev','<img src="'.C('TMPL_PARSE_STRING.__IMAGE__').'/prev.gif" border="0" title="上一页" />');
		$Page->setConfig('next','<img src="'.C('TMPL_PARSE_STRING.__IMAGE__').'/next.gif" border="0" title="下一页" />');
		$Page->setConfig('first','<img src="'.C('TMPL_PARSE_STRING.__IMAGE__').'/first.gif" border="0" title="第一页" />');
		$Page->setConfig('last','<img src="'.C('TMPL_PARSE_STRING.__IMAGE__').'/last.gif" border="0" title="最后一页" />');
		$show = $Page->show();							//分页显示输出
		$dmenu = M('dmenu');
		$dlist = $dmenu->order('Sortid asc')->select();
		$volist = $with->relation(true)->where($where)->order('Dtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$html = '';
		//判断有无数据
		if (count($volist) > 0) {
			//循环取出对应下拉菜单的数据
			for($i=0; $i<count($volist); $i++) {
				for($j=0; $j<count($dlist); $j++) {
					if ($volist[$i]['Wast'] == $dlist[$j]['ID']) {
						$volist[$i]['Wast'] = $dlist[$j]['MenuName'];
					}
				}
			}
			//输出数据
			$i=1;
			$public = A('Public');
			foreach($volist as $vo) {
				//将搜索的标为红色
				$NextTime = str_replace($keyword,'<font>'.$keyword.'</font>',$vo['NextTime']);		//客户名称
				if ($b=$i % 2 == 0) { 
					$tr2 = 'tr2';
				}else {
					$tr2 = '';
				}
				$remind = '';
				if ($vo['Remind'] == 0) {
					$remind = '<font style="color:#f00">未完成</font>';
				}else {
					$remind = '<font style="color:#06f">完成</font>';
				}
				$html .= "
					<tr class='tr ".$tr2."'>
						<td class='tc'><input type='checkbox' class='delid' value='".$vo['ID']."' /></td>
						<td class='tc'>".$vo['OrderNo']."</td>
						<td class='tc'>".$vo['ConsigneeName']."</td>
						<td class='tc'>".$vo['Phone']."</td>
						<td class='tc'>".$vo['GoodsNum']."</td>
						<td class='tc'>".$vo['Address']."</td>
						<td class='tc'>".$vo['Content']."</td>
						<td><a title='点击只显示此公司的跟单情况' class='compyname' href='".C('TMPL_PARSE_STRING.__APP__')."/With/withajax?Cid=".$vo['Cid']."'>".$vo['CompanyName']."</a></td>
						<td class='tc'>".$public->orderStatus($vo['Status'])."</td>
						<td class='tc'>".$remind."</td>
						<td class='tc'>".$public->Beautifytime($vo['Dtime'])."</td>
						<td class='tc fixed_w'><a href='".$vo['ID']."' class='edit'><img src='".C('TMPL_PARSE_STRING.__IMAGE__')."/edit.png' border='0' title='查看详细/修改' /></a><a href='".$vo['ID']."' class='del'><img src='".C('TMPL_PARSE_STRING.__IMAGE__')."/delete.png' border='0' title='删除' /></a></td>
					</tr>
				";
				$i++;
			}
			$data = array('s'=>'ok','html'=>$html,'page'=>'<span class="page">'.$show.'</span>');
			echo json_encode($data);
		}else {
			$html = "<tr class='tr'><td class='tc' colspan='9'>暂无数据，等待添加～！</td></tr>";
			$data = array('s'=>'no','html'=>$html);
			echo json_encode($data);
		}
	}
	//窗口跟单数据
	public function winwith() {
		parent::win_userauth(78);
		//验证用户权限
		$Cid = I('get.Cid','');
		if ($Cid=='' || !is_numeric($Cid)) {
			parent::operating(__ACTION__,1,'参数错误');
			$this->error('参数不正确');
		}
		$this->assign('Cid',$Cid);
		$this->display('Client/winwith');
	}
	//窗口跟单数据Ajax请求
	public function winwithajax() {
		$Cid = I('get.Cid','','htmlspecialchars');
		$with = D('With');
		import('ORG.Util.Page');						// 导入分页类
		$where['Uid'] = $_SESSION['ThinkUser']['ID'];
		$where['Cid']  = intval($Cid);
		$count = $with->where($where)->count();			//总记录数
		$Page = new Page($count,15);					//实例化分页类 传入总记录数和每页显示的记录数
		$Page->setConfig('header','条记录');
		$Page->setConfig('prev','<img src="'.C('TMPL_PARSE_STRING.__IMAGE__').'/prev.gif" border="0" title="上一页" />');
		$Page->setConfig('next','<img src="'.C('TMPL_PARSE_STRING.__IMAGE__').'/next.gif" border="0" title="下一页" />');
		$Page->setConfig('first','<img src="'.C('TMPL_PARSE_STRING.__IMAGE__').'/first.gif" border="0" title="第一页" />');
		$Page->setConfig('last','<img src="'.C('TMPL_PARSE_STRING.__IMAGE__').'/last.gif" border="0" title="最后一页" />');
		$show = $Page->show();							//分页显示输出
		$dmenu = M('dmenu');
		$dlist = $dmenu->order('Sortid asc')->select();
		$volist = $with->relation(true)->where($where)->order('Dtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$html = '';
		//判断有无数据
		if (count($volist) > 0) {
			//循环取出对应下拉菜单的数据
			for($i=0; $i<count($volist); $i++) {
				for($j=0; $j<count($dlist); $j++) {
					if ($volist[$i]['Wast'] == $dlist[$j]['ID']) {
						$volist[$i]['Wast'] = $dlist[$j]['MenuName'];
					}
				}
			}
			//输出数据
			$i=1;
			$public = A('Public');
			foreach($volist as $vo) {
				//将搜索的标为红色
				if ($b=$i % 2 == 0) { 
					$tr2 = 'tr2';
				}else {
					$tr2 = '';
				}
				$remind = '';
				if ($vo['Remind'] == 0) {
					$remind = '<font style="color:#f00">未完成</font>';
				}else {
					$remind = '<font style="color:#06f">完成</font>';
				}
				$html .= "
					<tr class='tr ".$tr2."'>
						<td class='tc'><input type='checkbox' class='delid' value='".$vo['ID']."' /></td>
						<td class='tc'>".$vo['ID']."</td>
						<td class='tc'>".$vo['ContactName']."</td>
						<td class='tc'>".$vo['Wast']."</td>
						<td class='tc'>".$vo['NextTime']."</td>
						<td class='tc'>".$remind."</td>
						<td class='tc'>".$public->Beautifytime($vo['Dtime'])."</td>
						<td class='tc fixed_w'><a href='".$vo['ID']."' class='edit'><img src='".C('TMPL_PARSE_STRING.__IMAGE__')."/edit.png' border='0' title='查看详细/修改' /></a><a href='".$vo['ID']."' class='del'><img src='".C('TMPL_PARSE_STRING.__IMAGE__')."/delete.png' border='0' title='删除' /></a></td>
					</tr>
				";
				$i++;
			}
			$data = array('s'=>'ok','html'=>$html,'page'=>'<span class="page">'.$show.'</span>');
			echo json_encode($data);
		}else {
			$html = "<tr class='tr'><td class='tc' colspan='7'>暂无数据，等待添加～！</td></tr>";
			$data = array('s'=>'no','html'=>$html);
			echo json_encode($data);
		}
	}
	//新增跟单记录
	public function withadd() {
		parent::win_userauth(79);
		$Cid = I('get.Cid','');
		if ($Cid=='' || !is_numeric($Cid)) {
			parent::operating(__ACTION__,1,'参数错误');
			$this->content='参数ID类型错误，请关闭本窗口';
			exit($this->display('Public:err'));
		}
		//下拉菜单数据
		$dmenu = M('dmenu');
		$volist=$dmenu->where('Sid <> 0')->order('Sortid asc')->select();
		//客户公司名
		$client = M('client');
		$result = $client->where("ID = $Cid AND Uid=".$_SESSION['ThinkUser']['ID'])->find();
		//对象联系人
		$contact = M('contact');
		$contactlist = $contact->where("Cid = $Cid AND Uid=".$_SESSION['ThinkUser']['ID'])->select();
		$this->assign('volist',$volist);
		$this->assign('result',$result);
		$this->assign('contactlist',$contactlist);
		$this->display('Client/withadd');
	}
	//新增处理
	public function withadd_do() {
		parent::userauth(79);
		if ($this->isAjax()) {
			$data=array();		
			$data['Cid'] = I('post.cid','','htmlspecialchars');	
			$data['ConsigneeName'] = I('post.consigneeName','','htmlspecialchars');	
			$data['Phone'] = I('post.phone','','htmlspecialchars');	
			$data['GoodsNum'] = I('post.goodsNum','','htmlspecialchars');	
			$data['Address'] = I('post.address','','htmlspecialchars');	
			$data['NameId'] = I('post.nameid');
			$data['Wast'] = I('post.wast');
			$data['NextTime'] = I('post.nexttime','');
			$data['RemindTime'] = I('post.remindtime','');
			$data['Content'] = I('post.description','','htmlspecialchars');
			$data['Status'] = I('post.status',1);
			$data['Remind'] = I('post.remind',0);
			//自动完成验证与新增
			$with=D('With');
			if ($with->create($data)) {
				$with->add();
				parent::operating(__ACTION__,0,'新增成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'新增失败：'.$with->getError());
				R('Public/errjson',array($with->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//修改
	public function withedit() {
		parent::win_userauth(80);
		$id = I('get.id','');
		if ($id=='' || !is_numeric($id)) {
			parent::operating(__ACTION__,1,'参数错误');
			$this->content='参数ID类型错误，请关闭本窗口';
			exit($this->display('Public:err'));
		}
		$uid = $_SESSION['ThinkUser']['ID'];
		//下拉菜单数据
		$dmenu = M('dmenu');
		$volist=$dmenu->where('Sid <> 0')->order('Sortid asc')->select();
		//查出相应数据
		$with = D('With');
		$result = $with->relation(true)->where("ID = $id AND Uid = $uid")->find();
		//对象联系人
		$contact = M('contact');
		$contactlist = $contact->where("Cid = ".$result['Cid']." AND Uid=".$_SESSION['ThinkUser']['ID'])->select();
		if (!$result) {
			parent::operating(__ACTION__,1,'数据不存在');
			$this->content='不存在你要修改的数据，请关闭本窗口';
			exit($this->display('Public:err'));
		}
		$this->assign('volist',$volist);
		$this->assign('result',$result);
		$this->assign('contactlist',$contactlist);
		$this->display('Client/withedit');
	}
	//修改处理
	public function withedit_do() {
		parent::userauth(80);
		if ($this->isAjax()) {
			$data=array();		
			$data['ID'] = I('post.id','','htmlspecialchars');
			$data['ConsigneeName'] = I('post.consigneeName');
			$data['Phone'] = I('post.phone');
			$data['GoodsNum'] = I('post.goodsNum');
			$data['Address'] = I('post.address');
			$data['NameId'] = I('post.nameid');
			$data['Wast'] = I('post.wast');
			$data['NextTime'] = I('post.nexttime','');
			$data['RemindTime'] = I('post.remindtime','');
			$data['Content'] = I('post.description','','htmlspecialchars');
			$data['Status'] = I('post.status',0);
			$data['Remind'] = I('post.remind',0);
			//自动完成验证与新增
			$with=D('With');
			if ($with->create($data)) {
				$with->save();
				parent::operating(__ACTION__,0,'更新成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'更新失败：'.$with->getError());
				R('Public/errjson',array($with->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//删除跟单记录
	public function with_del() {
		parent::userauth(81);
		//判断是否是ajax请求
		if ($this->isAjax()) {
			$id=I('post.id','');
			if ($id=='' || !is_numeric($id)) {
				parent::operating(__ACTION__,1,'参数错误');
				R('Public/errjson',array('参数ID类型错误'));
			}else {
				$id=intval($id);
				$with = M('with');
				$where=array('ID'=>$id);
				if ($with->where($where)->getField('ID')) {
					$with->where($where)->delete();
					parent::operating(__ACTION__,0,'删除成功');
					R('Public/errjson',array('ok'));
				}else {
					parent::operating(__ACTION__,1,'数据不存在');
					R('Public/errjson',array('数据不存在'));
				}
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			$this->error('非法请求');
		}
	}
	//批量删除
	public function with_indel() {
		//验证用户权限
		parent::userauth(81);
		if ($this->isAjax()) {
			if (!$delid=explode(',',I('post.delid',''))) {
				R('Public/errjson',array('请选中后再删除'));
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			$id=join(',',$delid);
			$with=M('with');
			$co['ID'] = array('in',$id);
			if ($with->delete("$id")) {
				parent::operating(__ACTION__,0,'删除成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'删除失败');
				R('Public/errjson',array('删除失败'));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			$this->error('非法请求');
		}
	}

	
}
?>