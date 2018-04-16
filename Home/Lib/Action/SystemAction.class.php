<?php
//系统设置/操作类
class SystemAction extends CommonAction {
	//IP操作开始
	public function ip() {
		parent::userauth2(21);
		$keyword = I('get.keyword','','htmlspecialchars');
		$ip = D('Ip');
		import('ORG.Util.Page');						// 导入分页类
		if ($keyword!='') {
			$where['Ip'] = $keyword;
		}else {
			$where['Ip'] = '';
		}
		$count = $ip->where($where)->count();			//总记录数
		$Page = new Page($count,15);					//实例化分页类 传入总记录数和每页显示的记录数
		$Page->setConfig('header','条记录');
		$Page->setConfig('prev','<img src="__IMAGE__/prev.gif" border="0" title="上一页" />');
		$Page->setConfig('next','<img src="__IMAGE__/next.gif" border="0" title="下一页" />');
		$Page->setConfig('first','<img src="__IMAGE__/first.gif" border="0" title="第一页" />');
		$Page->setConfig('last','<img src="__IMAGE__/last.gif" border="0" title="最后一页" />');
		$show = $Page->show();							//分页显示输出
		$volist = $ip->relation(true)->where($where)->order('Dtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('volist',$volist);
		$this->assign('page',$show);					//输出分页
		$this->assign('keyword',$keyword);
		$this->co = $count;
		$this->display();
	}
	public function ipadd() {
		parent::win_userauth(22);
		$this->display();
	}
	public function ipadd_do() {
		//验证用户权限
		parent::userauth(22);
		//判断请求类型
		if ($this->isAjax()) {
			$data['Uid'] = $_SESSION['ThinkUser']['ID'];
			$data['Ip'] = I('post.ip','');
			$data['StartTime'] = I('post.stime','');
			$data['EndTime'] = I('post.etime','');
			$data['Status'] = I('post.status','');
			$data['Description'] = I('post.description','');
			$data['Dtime'] = date('Y-m-d H:i:s');
			$ip = D('Ip');
			if ($ip->create($data)) {
				$ip->add();
				parent::operating(__ACTION__,0,'新增IP限制登录成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'新增失败：',$ip->getError());
				R('Public/errjson',array($ip->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//删除数据
	public function del() {
		//验证用户权限
		parent::userauth(23);
		//判断是否是ajax请求
		if ($this->isAjax()) {
			$id = I('post.id','');
			if ($id=='' || !is_numeric($id)) {
				parent::operating(__ACTION__,1,'参数错误');
				R('Public/errjson',array('参数ID类型错误'));
			}else {
				$id=intval($id);
				$ip=M('ip');
				$where=array('ID'=>$id);
				if ($ip->where($where)->getField('ID')) {
					$ip->where($where)->delete();
					parent::operating(__ACTION__,0,'删除成功');
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
	}
	//批量删除
	public function indel() {
		//验证用户权限
		parent::userauth(23);
		if ($this->isAjax()) {
			if (!$delid=explode(',',$this->_post('delid'))) {
				R('Public/errjson',array('请选中后再删除'));
			}
			//将最后一个元素弹出栈
			array_pop($delid);
			$id=join(',',$delid);
			$ip=M('ip');
			if ($ip->delete("$id")) {
				parent::operating(__ACTION__,0,'删除成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'删除失败：'.$ip->getError());
				R('Public/errjson',array('删除失败'));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//IP操作结束
	
	//模块管理开始
	public function module() {
		parent::userauth2(25);
		$module = D('Module');
		$list=$module->relation(true)->order('Msort asc')->select();
		$arr=$this->classid($list);
		$this->assign('co',count($list));
		$this->assign('list',$arr);
		$this->display('module');
	}
	//添加模块
	public function module_add() {
		parent::win_userauth(26);
		$module = M('module');
		$list=$module->order('Msort asc')->getField('ID,Sid,ModuleName,Msort');
		$arr=$this->classid($list);
		$this->assign('list',$arr);
		$this->display('moduleadd');
	}
	//无限循环分类
	protected function classid($volist,$nan=0,$html='──',$level=0) {
		$arr=array();
		foreach($volist as $val) {
			if ($val['Sid'] == $nan) {
				$val['classname'] = $val['ModuleName'];
				$val['html'] = str_repeat($html,$level);
				$arr[] = $val;
				$arr = array_merge($arr,self::classid($volist,$val['ID'],$html,$level+1));
			}
		}
		return $arr;
	}
	//添加模块处理
	public function module_add_do() {
		parent::userauth(26);
		if ($this->isAjax()) {
			$data['Sid'] = I('post.sid','');
			$data['ModuleName'] = I('post.mname','','htmlspecialchars');
			$data['ModuleImg'] = I('post.img','');
			$data['ModuleUrl'] = I('post.url','');
			$data['Status'] = I('post.status','');
			$data['Msort'] = I('post.msort','');
			$data['Description'] = I('post.description','','htmlspecialchars');
			$module = D('Module');
			if ($module->create($data)) {
				$module->add();
				parent::operating(__ACTION__,0,'新增模块成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'新增失败：'.$module->getError());
				R('Public/errjson',array($module->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//修改模块
	public function module_edit() {
		parent::win_userauth(27);
		$id = I('get.id','');
		if ($id=='' || !is_numeric($id)) {
			parent::operating(__ACTION__,1,'参数错误');
			$this->content='参数ID类型错误，请关闭本窗口';
			exit($this->display('Public:err'));
		}
		$id=intval($id);
		$module=M('module');
		//获取分类信息
		$data=array('ID' => $id);
		if ($result=$module->where($data)->find()) {
			$this->result=$result;
			$list=$module->order('Msort asc')->getField('ID,Sid,ModuleName,Msort');
			$arr=$this->classid($list);
			$this->assign('list',$arr);
			$this->assign('result',$result);
			$this->display('moduleedit');
		}else {
			parent::operating(__ACTION__,1,'数据不存在');
			$this->content='没有找到相关数据，请关闭本窗口';
			$this->display('Public:err');
		}
	}
	//图标列表
	public function img () {
		$this->display('img');
	}
	//图标列表请求
	public function img_do() {
		$imgcount=1031;			//图标总数
		$speed=100;				//分页数
		if ($this->isAjax()) {
			$page = I('post.page','');
			$html='';
			if (floor($imgcount/$speed)+1 == $page) {
				$stratpage=1000;
				$endpage = $imgcount;
			}else {
				$stratpage=$page*$speed;
				if ($page>1) {
					$stratpage-=$speed;
				}else {
					$stratpage=$page;
				}
				$endpage=$page*$speed;
			}
			for ($i = $stratpage; $i <= $endpage; $i++) {
				$html['y'.$i]="/img/$i.png";
			}
			echo json_encode($html);
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//修改模块处理
	public function module_edit_do() {
		parent::userauth(27);
		if ($this->isAjax()) {
			$data['ID'] = I('post.id','');
			$data['Sid'] = I('post.sid','');
			$data['ModuleName'] = I('post.mname','','htmlspecialchars');
			$data['ModuleImg'] = I('post.img','');
			$data['ModuleUrl'] = I('post.url','');
			$data['Status'] = I('post.status','');
			$data['Msort'] = I('post.msort','');
			$data['Description'] = I('post.description','','htmlspecialchars');
			
			$module = D('Module');
			if ($module->create($data)) {
				$module->save();
				parent::operating(__ACTION__,0,'更新成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'更新失败：'.$module->getError());
				R('Public/errjson',array($module->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//模块删除
	public function moduledel() {
		//验证用户权限
		parent::userauth(28);
		//判断是否是ajax请求
		if ($this->isAjax()) {
			$id = I('post.id','');
			if ($id=='' || !is_numeric($id)) {
				parent::operating(__ACTION__,1,'参数错误');
				R('Public/errjson',array('参数ID类型错误'));
			}else {
				$id=intval($id);
				$module=M('module');
				$where=array('ID'=>$id);
				if ($module->where($where)->getField('ID')) {
					$module->where($where)->delete();
					parent::operating(__ACTION__,0,'删除模块成功');
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
	}
	//系统配置
	public function systemconfig() {
		parent::userauth2(30);
		$config['tarce'] = C('SHOW_PAGE_TRACE');
		$config['debug'] = C('APP_STATUS');
		$config['sessionuser'] = C('USER_AUTH_SESSION');
		$config['log'] = C('LOG_RECORD');
		$logclass = explode(',',C('LOG_LEVEL'));
		$config['url'] = C('URL_MODEL');						//URL模式
		$config['urlcase'] = C('URL_CASE_INSENSITIVE');			//URL大小写是否开启
		$config['tokenon'] = C('TOKEN_ON');						// 是否开启令牌验证
		$config['tokenreset'] = C('TOKEN_RESET');				//令牌验证出错后是否重置令牌 默认为true
		$config['dbfieldcheck'] = C('DB_FIELDTYPE_CHECK');		//是否开启字段类型验证
		foreach($logclass as $val) {
			$config['typelog'][$val]=$val;
		}
		$this->assign('config',$config);
		$this->display();
	}
	//系统配置修改
	public function systemconfig_do() {
		//验证用户权限
		parent::userauth2(30);
		if ($this->isPost()) {
			$config['SHOW_PAGE_TRACE'] = I('post.trace');
			$config['APP_STATUS'] = I('post.debug','');
			$config['USER_AUTH_SESSION'] = I('post.sessionuser','');
			$config['LOG_RECORD'] = I('post.log','');
			$config['LOG_LEVEL'] = join(',',$_POST['typelog']);
			$config['URL_MODEL'] = I('post.url','');						//Url模式
			$config['URL_CASE_INSENSITIVE'] = I('post.urlcase','');			//URL大小写是否开启
			$config['TOKEN_ON'] = I('post.tokenon','');
			$config['TOKEN_RESET'] = I('post.tokenreset','');
			$config['DB_FIELDTYPE_CHECK'] = I('post.dbfieldcheck','');
			$settingstr="<?php \n return array(\n";
			foreach($config as $key => $val){
				if ($val == 'false') {
					$settingstr.= "\t'".$key."'=>false,\n";
				}elseif ($val == 'true') {
					$settingstr.= "\t'".$key."'=>true,\n";
				}else {
					$settingstr.= "\t'".$key."'=>'".$val."',\n";
				}
			}
			$settingstr.="\n);\n?>";
			if (file_put_contents(CONF_PATH.'setting.php',$settingstr,FILE_USE_INCLUDE_PATH)) {
				parent::operating(__ACTION__,0,'系统配置修改成功');
				$this->success('修改成功','systemconfig',2);
			}else {
				parent::operating(__ACTION__,1,'系统配置修改失败');
				$this->error('修改失败，可能是由于没有写入权限导致。');
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			$this->error('非法请求');
		}
	}
	//核心配置
	public function systemcore() {
		parent::userauth2(33);
		$config = require(CONF_PATH.'core.php');
		$this->assign('config',$config);
		$this->display();
	}
	//核心配置修改
	public function systemcore_do() {
		//验证用户权限
		parent::userauth2(33);
		if ($this->isPost()) {
			$config = $_POST;
			$settingstr="<?php \n return array(\n";
			foreach($config as $key => $val){
				$settingstr.= "\t'".$key."'=>'".$val."',\n";
			}
			$settingstr.="\n);\n?>";
			if (file_put_contents(CONF_PATH.'core.php',$settingstr,FILE_USE_INCLUDE_PATH)) {
				parent::operating(__ACTION__,0,'核心配置文件修改成功');
				$this->success('修改成功','systemcore',2);
			}else {
				parent::operating(__ACTION__,1,'核心配置文件修改失败，可能是没有写入权限导致');
				$this->error('修改失败，可能是由于没有写入权限导致。');
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			$this->error('非法请求');
		}
	}
	//网站配置
	public function systemwebsite() {
		parent::userauth2(53);
		$config = require(CONF_PATH.'webconfig.php');
		$this->assign('config',$config);
		$this->display();
	}
	//网站配置修改
	public function systemwebsite_do() {
		//验证用户权限
		parent::userauth2(53);
		if ($this->isPost()) {
			$config = $_POST;
			$settingstr="<?php \n return array(\n";
			foreach($config as $key => $val){
				$settingstr.= "\t'".$key."'=>'".$val."',\n";
			}
			$settingstr.="\n);\n?>";
			if (file_put_contents(CONF_PATH.'webconfig.php',$settingstr,FILE_USE_INCLUDE_PATH)) {
				parent::operating(__ACTION__,0,'网站配置文件修改成功');
				$this->success('修改成功','systemwebsite',2);
			}else {
				parent::operating(__ACTION__,1,'网站配置文件修改失败');
				$this->error('修改失败，可能是由于没有写入权限导致。');
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			$this->error('非法请求');
		}
	}
}
?>