<?php
// 本类由系统自动生成，仅供测试用途
class FrontAction extends Action {
    public function index(){
		//检测是否已经登录
		$this->authFront();
    }
    
    //判断用户是否登录的方法
    public function authFront(){
		$nowtime = time();
		$end_time=C('USER_AUTH_SESSION');		//读取配置文件中session的过期时间
		if (!isset($_SESSION['ThinkClientUser'])) {
			R('Public/location',array('阿哦！您还没有登录',__APP__.'/Index'));
		}else {
			if (($nowtime - $_SESSION['ThinkClientUser']['Logintime']) > $end_time) {
				unset($_SESSION['ThinkClientUser']);
				R('Public/location',array('阿哦！登录超时',__APP__.'/Index'));
			}else {
				$user = M('client');
				$uid = $_SESSION['ThinkClientUser']['ID'];
				$result=$user->where("ID = $uid")->count();
				if ($result != 1) {
					unset($_SESSION['ThinkClientUser']);
					R('Public/location',array('非法用户登录',__APP__.'/Index'));
				}else {
					$_SESSION['ThinkClientUser']['Logintime'] = $nowtime;
				}
			}
		}
    }
    
    //进货首页地址
    public function page(){
    	//检测是否已经登录
    	$this->authFront();
    	$this->display('Index/page');
    }
    
    //进货记录页面
    public function consignee(){
    	//检测是否已经登录
    	$this->authFront();
    	$data = $this->consignee_do();
    	$this->assign('result',$data);
    	$this->assign('cid',$_SESSION['ThinkClientUser']['ID']);
    	$this->display('Client/consignee');
    }
    //进货记录查询
    public function consigneeAjax(){
    	//检测是否已经登录
    	$this->authFront();
    	$data = $this->consignee_do();
    	echo json_encode($data);
    }
    //进货记录查询处理
    public function consignee_do(){
    	//检测是否已经登录
    	$this->authFront();
    	$keyword = I('get.keyword','','htmlspecialchars');
    	$with = D('With');
    	import('ORG.Util.Page');						// 导入分页类
    	$where['Cid'] = intval($_SESSION['ThinkClientUser']['ID']);
    	if ($keyword!='') {
    		$where['NextTime']  = array('like',"%$keyword%");
    	}
    	$count = $with->where($where)->count();			//总记录数
    	$Page = new Page($count,2);					//实例化分页类 传入总记录数和每页显示的记录数
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
    	
    	$data=array();
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
    					<ul class='receipt_lines'>
					      <li class='receipt_line'>
    					    <input type='hidden' class='delid' value='".$vo['ID']."' />
					        <span class='receipt_line_item'>订单编号:</span>
					        <span class='receipt_line_price'>".$vo['OrderNo']."</span>  
					      </li>
					      <li class='receipt_line'>
					        <span class='receipt_line_item'>进货人:</span>
					        <span class='receipt_line_price'>".$vo['ConsigneeName']."</span>  
					      </li>
					      <li class='receipt_line'>
					        <span class='receipt_line_item'>联系电话:</span>
					        <span class='receipt_line_price'>".$vo['Phone']."</span>  
					      </li>
					      <li class='receipt_line'>
					        <span class='receipt_line_item'>商品数量:</span>
					        <span class='receipt_line_price'>".$vo['GoodsNum']."</span>  
					      </li>
					      <li class='receipt_line'>
					        <span class='receipt_line_item'>联系地址:</span>
					        <span class='receipt_line_price'>".$vo['Address']."</span>  
					      </li>
					      <li class='receipt_line'>
					        <span class='receipt_line_item'>特殊备注:</span>
					        <span class='receipt_line_price'>".$vo['Content']."</span>  
					      </li>
					      <li class='receipt_line'>
					        <span class='receipt_line_item'>下单时间：</span>
					        <span class='receipt_line_price'>".$public->Beautifytime($vo['Dtime'])."</span>  
					      </li>
					    </ul><div class='orderStatusDiv'>".$this->orderStatusHtml($vo['Status'],$vo['ID'])."</div>";
    			$i++;
    		}
    		$data = array('s'=>'ok','html'=>$html,'page'=>'<span class="page">'.$show.'</span>');
    	}else {
    		$html = "<ul class='receipt_lines'> <li class='receipt_line'>暂无数据，等待下单～！     </li> </ul>";
    		$data = array('s'=>'no','html'=>$html);
    	}
    	return $data;
    }
    
    //订单状态展示
    public function orderStatusHtml($status,$orderVoId){
    	$statusHtml = '';
    	switch ($status){
    		case ($status == 1):
    			$statusHtml = "
    					<p class='receipt_total'>
					      <span class='receipt_total_item'>待发货</span>
					      <span class='receipt_total_price'><a class='orderStatus' href='javascript:void(0);' status='3' orderId='".$orderVoId."'>取消</a></span>
					    </p>
    					";
    			break;
    		case ($status == 2):
    			$statusHtml = "
    					<p class='receipt_total'>
					      <span class='receipt_total_item'>已发货</span>
					      <span class='receipt_total_price'><a class='orderStatus' href='javascript:void(0);' status='0' orderId='".$orderVoId."'>确认收货</a></span>
					    </p>
    					";
    			break;
    		case ($status == 3):
    			$statusHtml = "
    					<p class='receipt_total'>
					      <span class='receipt_total_item'>已取消</span>
					      <span class='receipt_total_price'>&nbsp;</span>
					    </p>
    					";
    			break;
    		case ($status == 0):
    			$statusHtml = "
    					<p class='receipt_total'>
					      <span class='receipt_total_item'>已结单</span>
					      <span class='receipt_total_price'>&nbsp;</span>
					    </p>
    					";
    			break;
    	}
    	return $statusHtml;
    }
    
    //修改订单状态
    public function changeOrderStatus_do(){
    	$orderId = I('post.orderId','','htmlspecialchars');
    	if ($this->isAjax()) {
	    	$data=array();
	    	$data['Status'] = I('post.status',0);
	    	$with=M('With');
	    	if ($with->create($data)) {
		    	$with->where('id='.$orderId)->save($data); 
		    	$html = $this->orderStatusHtml($data['Status'],$orderId);
		    	$data = array('s'=>'ok','html'=>$html);
		    	echo json_encode($data);
	    	}else {
	    		R('Public/errjson',array($with->getError()));
	    	}
    	}else {
    		R('Public/errjson',array('非法请求'));
    	}
    }
    
    //新增进货页面
    public function consigneeAdd(){
    	//检测是否已经登录
    	$this->authFront();
    	$this->assign('cid',$_SESSION['ThinkClientUser']['ID']);
    	$this->display('Client/consigneeadd');
    }
    //新增进货处理
    public function consigneeAdd_do(){
    	//检测是否已经登录
    	$this->authFront();
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
    			R('Public/errjson',array('ok'));
    		}else {
    			R('Public/errjson',array($with->getError()));
    		}
    	}else {
    		R('Public/errjson',array('非法请求'));
    	}
    }
    
    //会员管理
    public function clientupdate(){
    	//检测是否已经登录
    	$this->authFront();
    	$this->assign('client',$_SESSION['ThinkClientUser']);
    	$this->display('Client/clientupdate');
    }
    
    //会员管理处理
    public function clientupdate_do(){
    	//检测是否已经登录
    	$this->authFront();
    	if ($this->isAjax()) {
	    	$password = I('post.password1','');
	    	if (strlen($password)<6 || strlen($password)>18) {
	    		R('Public/errjson',array('请输入6位数以上的密码'));
	    	}
	    	$cid = I('post.cid','','htmlspecialchars');
	    	if($_SESSION['ThinkClientUser']['ID'] != $cid){
	    		R('Public/errjson',array('用户不一致,请重新登录'));
	    	}
	    	//自动完成验证与新增
	    	$data=array();
	    	$data['ID'] = $_SESSION['ThinkClientUser']['ID'];
	    	$data['Username'] = I('post.username','','htmlspecialchars');
	    	$data['Password'] = sha1(md5(I('post.password1','')));
	    		
	    	//验证是否存在用户名
	    	$client = D('client');
	    	$where['Username'] = I('post.username','','htmlspecialchars');
	    	$where['Password'] = sha1(md5(I('post.password','')));
	    	if (($client->where($where)->count()) == 0) {
	    		R('Public/errjson',array('请输入正确的密码'));
	    	}
	    	if ($client->create($data)) {
	    		$client->save();
	    		R('Public/errjson',array('ok'));
	    	}else {
	    		R('Public/errjson',array($with->getError()));
	    	}
    	}else {
    		R('Public/errjson',array('非法请求'));
    	}
    }
    
	//地理位置信息获取
	public function area() {
		$area = array();
		//位置获取
		import('ORG.Net.IpLocation');				// 导入IpLocation类
		$Ip = new IpLocation('UTFWry.dat');			// 实例化类 参数表示IP地址库文件
		$area = $Ip->getlocation();					// 获取某个IP地址所在的位
		return $area;
	}
	public function loginlog($uid,$username,$description,$area,$cip) {
		  //登录日志记录
		  $hlog['Uid'] = $uid;
		  $hlog['User'] = $username;
		  $hlog['Description'] = $description;
		  $hlog['Area'] = $area;
		  $hlog['Loginip'] = $cip;
		  $hlog['Dtime'] = date('Y-m-d H:i:s');
		  $log = M('loginlog');
		  $log->add($hlog);
	}
	//查看证书
	public function certificate() {
		//检测是否已经登录
		$id = I('get.id','');
		if($id){
			$user = M('Client');
			$where['ID'] = $id;
			$result=$user->where($where)->getField('ID,Username,Password,UserPhone,CompanyName,Status,Dtime');
			//将二维数组转为一维数组
			foreach($result as $key => $val) {
				$clientUser = $val;
			}
			$clientUserDtime = $clientUser['Dtime'];
		} else {
			$this->authFront();
			$clientUser = $_SESSION['ThinkClientUser'];
			$clientUserDtime = $_SESSION['ThinkClientUser']['Dtime'];
		}
		$date = date_create($clientUserDtime);
		$dtime = array();
		$dtime['yyyy'] = date_format($date,"Y");
		$dtime['mm'] = date_format($date,"m");
		$dtime['dd'] = date_format($date,"d");
		$this->assign('dtime',$dtime);
		$this->assign('client',$clientUser);
		$this->display('Index/certificate');
	}
	
	//退出登录
	public function quit() {
		session('[destroy]');	//销毁所有SESSION
		$this->redirect('Index/index');
	}
}
?>