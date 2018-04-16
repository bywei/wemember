<?php
class CommonAction extends Action {
	public function _initialize() {
		$this->checkAdminSession();
		$this->configcache();
	}
	//判断用户是否登录的方法
	public function checkAdminSession() {
		$nowtime = time();
		$end_time=C('USER_AUTH_SESSION');		//读取配置文件中session的过期时间
		if (!isset($_SESSION['ThinkUser'])) {
			R('Public/location',array('阿哦！您还没有登录',__APP__.'/Admin/index'));
		}else {
			if (($nowtime - $_SESSION['ThinkUser']['Logintime']) > $end_time) {
				unset($_SESSION['ThinkUser']);
				R('Public/location',array('阿哦！登录超时',__APP__.'/Admin/index'));
			}else {
				$user = M('user');
				$uid = $_SESSION['ThinkUser']['ID'];
				$result=$user->where("ID = $uid")->count();
				if ($result != 1) {
					unset($_SESSION['ThinkUser']);
					R('Public/location',array('非法用户登录',__APP__.'/Admin/index'));
				}else {
					$_SESSION['ThinkUser']['Logintime'] = $nowtime;
					$this->statis($uid);
				}
			}
		}
	}
	//在线人数统计
	public function statis($uid) {
		$statis = M('statis');
		$where['Dtime'] = array('LT', time()-120);
		if ($statis->where("Uid = $uid")->count()) {
			$statis->where("Uid = $uid")->save(array('Dtime' => time()));
			$statis->where($where)->delete();
		}else {
			$statis->where($where)->delete();
			$time = time();
			$data = array('Uid' => $uid, 'Dtime' => time());
			$statis->add($data);
		}
	}	
	//基本配置信息缓存
	public function configcache() {
		if (!S('configcache')) {
			$configcache = require(CONF_PATH.'webconfig.php');
			S('configcache', $configcache, $configcache['DataCache']*3600);
		}
		$this->assign('configcache',S('configcache'));
	}
	//操作记录
	public function operating($url,$status,$description) {
		$data = array();
		$data['Uid'] = $_SESSION['ThinkUser']['ID'];
		$data['Url'] = $url;
		$data['Description'] = $description;
		$data['Ip'] = get_client_ip();
		$data['Status'] = $status;
		$data['Dtime'] = date('Y-m-d H:i:s');
		$operating = M('operating');
		$operating->add($data);
	}
	//用户权限验证1(ajax发送返回验证)
	public function userauth($auth) {
		$comp=explode(',',$_SESSION['ThinkUser']['Competence']);			//当前用户权限获取
		array_pop($comp);
		if (!in_array($auth,$comp)) {
			$err=array('s'=>'抱歉，您没有此操作权限');
			exit(json_encode($err));
		}
	}
	//用户权限验证2(页面)
	public function userauth2($auth) {
		$comp=explode(',',$_SESSION['ThinkUser']['Competence']);			//当前用户权限获取
		array_pop($comp);
		if (!in_array($auth,$comp)) {
			$this->content='抱歉，您没有此操作权限';
			exit($this->display('Public:error'));
		}
	}
	//用户权限验证3(窗口验证)
	public function win_userauth($auth) {
		$comp=explode(',',$_SESSION['ThinkUser']['Competence']);			//当前用户权限获取
		array_pop($comp);
		if (!in_array($auth,$comp)) {
			$this->content='抱歉，您没有此操作权限';
			exit($this->display('Public:err'));
		}
	}
	//删除缓存文件
	public function delDir($dirName) {
		 $dh = opendir($dirName);
		 //循环读取文件
		 while ($file = readdir($dh)) {
			 if($file != '.' && $file != '..') {
				 $fullpath = $dirName . '/' . $file;
				 //判断是否为目录
				 if(!is_dir($fullpath)) {
					 //如果不是,删除该文件
					 if(!unlink($fullpath)) {
						 echo $fullpath . '无法删除,可能是没有权限!<br>';
					 }
				 } else {
					 //如果是目录,递归本身删除下级目录
					 $this->delDir($fullpath);
				 }
			 }
		 }
		 //关闭目录
		 closedir($dh);
		 //删除目录
		 if(!rmdir($dirName)) {
			 R('Public/errjson',array($dirName.'__目录删除失败'));
		 }
	}
	//一键清空缓存
	public function clearcache() {
		//验证用户权限
		$this->userauth(24);
		if ($this->isAjax()) {
			if (I('post.clear','')=='ok') {
				$fileDel = 'Home/Runtime';
				if (file_exists($fileDel)) {
					$this->delDir($fileDel);
					$this->operating(__ACTION__,0,'清空站点缓存');
					R('Public/errjson',array('ok'));
				}else {
					R('Public/errjson',array('缓存目录不存在'));
				}
			}else {
				R('Public/errjson',array('非法请求'));
			}
		}else {
			R('Public/errjson',array('非法请求'));
		}
	}
	//链接请求
	public function link() {
		$with = D('With');
		$Uid = $_SESSION['ThinkUser']['ID'];
		$date = date('Y-m-d H:i:s');
		//总跟单数
		$withlist = $with->relation(true)->where("Uid = $Uid AND Status = 0 AND Remind = 0 AND RemindTime <= '$date'")->count();
		R('Public/errjson',array($withlist));
	}
}
?>