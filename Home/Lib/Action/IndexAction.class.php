<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {
    public function index(){
		//检测是否已经登录
		if (isset($_SESSION['ThinkClientUser'])) {
			$end_time=C('USER_AUTH_SESSION');
			if ((time() - $_SESSION['ThinkClientUser']['Logintime']) > $end_time) {
				unset($_SESSION['ThinkClientUser']);
				$this->display('Index/login');
			}else {
				$this->redirect('Index/home');
			}
		}else {
			$this->display('Index/login');
		}
    }
    
    public function home(){
    	$page = I('get.page','page','htmlspecialchars');
    	$page = isset($page) ? $page : 'page';
    	$this->assign('page',$page);
    	$this->display('Index/home');
    }
    
    public function signup(){
		$this->display('Index/signup');
    }
    
    public function signup_do(){
    	if ($this->isAjax()) {
    		$username = I('post.username','','htmlspecialchars');
    		$password = I('post.password','');
    		if (!preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{2,16}$/u',$username)) {
    			R('Public/errjson',array('请输入合法的用户名'));
    		}
    		if (strlen($password)<6 || strlen($password)>18) {
    			R('Public/errjson',array('请输入6位数以上的密码'));
    		}
    		if (!(strnatcasecmp($_SESSION['verify'], md5(strtolower(I('post.code',''))))==0)) {
    			R('Public/errjson',array('请输入正确的验证码'));
    		}
    		
    		$data=array();
			$cont=array();
			$data['Username'] = I('post.username','','htmlspecialchars');
			$data['Password'] = I('post.password','');
			$data['UserPhone'] = I('post.userPhone','','htmlspecialchars');
			
			$cont['Phone'] = I('post.userPhone','','htmlspecialchars');
			$cont['ContactName'] = $username;
			
			//自动完成验证与新增
			$client=D('Client');
			//验证是否存在用户名
			$where['Username'] = $username;
			if ($client->where($where)->count()) {
				R('Public/errjson',array('该用户名已存在'));
			}
			if ($client->create($data)) {
				//添加联系人
				$cid = $client->order('ID desc')->getField('ID')+1;
				$cont['Cid'] = $cid++;
				$contact=D('Contact');
				if ($contact->create($cont)) {
					$id = $client->add();
					$cid = $contact->add();
					$client->where("ID=$id")->Save(array('Cid' => $cid));
				}else {
					R('Public/errjson',array('注册联系人失败'));
				}
				//销毁验证码session
				session('verify',null);
				R('Public/errjson',array('ok'));
			}else {
				R('Public/errjson',array('注册失败'));
			}
    	}else {
			R('Public/errjson',array('非法请求注册'));
		}
    }
    
	//前台登录验证
	public function login() {
		if ($this->isAjax()) {
			$login = array();
			$username = I('post.username','','htmlspecialchars');
			$password = I('post.password','');
			if (!preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{2,16}$/u',$username)) {
				R('Public/errjson',array('请输入合法的用户名'));
			}
			if (strlen($password)<6 || strlen($password)>18) {
				R('Public/errjson',array('请输入6位数以上的密码'));
			}
			if (!(strnatcasecmp($_SESSION['verify'], md5(strtolower(I('post.code',''))))==0)) {
				R('Public/errjson',array('请输入正确的验证码'));
			}
			$area = $this->area();
			$dip = M('ip');
			$whereIP['Ip'] = $area['ip'];
			$resip = $dip->where($whereIP)->find();
			if ($resip) {
				if ($resip['Status'] == 1) {
					$this->loginlog(0,'未知','<div class="de2">被封锁IP尝试登录</div>',$area['country'].'.'.$area['area'],$area['ip']);
					R('Public/errjson',array('您的IP异常已被封禁，请等待管理员解除封禁！'));
				}else {
					$endtime = strtotime($resip['EndTime']);		//结束时间
					if (($endtime - date('Y-m-d')) > 1) {
						$this->loginlog(0,'未知','<div class="de2">被封锁帐号尝试登录</div>',$area['country'].'.'.$area['area'],$area['ip']);
						R('Public/errjson',array('您的IP异常已被封禁，请等待管理员解除封禁！'));
					}
				}
			}
			$where['Username'] = $username;
			$user = M('Client');
			if ($re = $user->where($where)->count()) {
				$where['Status']=0;
				if (!$user->where($where)->count()) {
					$this->loginlog($re['ID'],$username,'<div class="de2">违规帐号登录</div>',$area['country'].'.'.$area['area'],$area['ip']);
					R('Public/errjson',array('当前帐号已被封禁，请等待解除～！'));
				}
				$where['Password']=sha1(md5($password));
				if (!$result=$user->where($where)->getField('ID,Username,Password,UserPhone,CompanyName,Status,Dtime')) {
					$this->loginlog($re['ID'],$username,'<div class="de2">登录密码错误</div>',$area['country'].'.'.$area['area'],$area['ip']);
					R('Public/errjson',array('登录密码错误！'));
				}
				//将二维数组转为一维数组
				foreach($result as $key => $val) {
					$arr = $val;
				}
				//日志记录
				$this->loginlog($arr['ID'],$username,'<div class="de1">登录成功</div>',$area['country'].'.'.$area['area'],$area['ip']);
				$arr['Loginarea'] = $area['country'].'.'.$area['area'];
				$arr['Loginip'] = $area['ip'];
				$arr['Logintime'] = time();
				$arr['Logincount'] = $arr['Logincount']+1;
				$_SESSION['ThinkClientUser'] = $arr;
				//销毁验证码session
				session('verify',null);
				R('Public/errjson',array('ok'));
			}else {
				$this->loginlog(0,$username,'<div class="de2">用户不存在</div>',$area['country'].'.'.$area['area'],$area['ip']);
				R('Public/errjson',array('用户名不存在'));
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
	//管理界面
	public function main() {
		A('Common');
		$this->session=$_SESSION['ThinkUser'];
		//===模块导航开始===
		if (!S('list')) {
			$module = M('module');
			$list=$module->where('Sid = 0')->order('Msort asc')->select();
			$volist=$module->where('Sid > 0')->order('Msort asc')->select();
			S('list', $list, $configcache['DataCache']*3600);
			S('volist', $volist, $configcache['DataCache']*3600);
		}
		$this->assign('list', S('list'));
		$this->assign('volist', S('volist'));
		//===模块导航结束===
		$this->display();
	}
	//框架显示
	public function content() {
		A('Common');
		//将数据缓存
		if (!S('info')) {
			//获取系统信息
			$systeminfo['THINK_VERSION'] = THINK_VERSION;
			$systeminfo['SERVER_SOFTWARE'] = $_SERVER["SERVER_SOFTWARE"];
			$systeminfo['PHP_OS'] = PHP_OS;
			$systeminfo['mysql'] = mysql_get_server_info();
			$systeminfo['core'] = require(CONF_PATH.'core.php');
			//获取客户信息
			$client = M('client');
			//客户总数
			$cl['clientcount'] = $client->where('Recycle = 0')->count();
			//正在跟进
			$cl['clientcount2'] = $client->where('Recycle = 0 AND FollowUp = 55')->count();
			//待跟进
			$cl['clientcount3'] = $client->where('Recycle = 0 AND FollowUp = 56')->count();
			//完成跟进
			$cl['clientcount4'] = $client->where('Recycle = 0 AND FollowUp = 62')->count();
			$contact = M('contact');
			$cl['contactcount'] = $client->count();								//联系人总数
			//用户总数
			$user = M('user');
			$systeminfo['user'] = $user->count();
			//文件总数
			$file = M('file');
			$systeminfo['file'] = $file->count();
			//新闻总数
			$news = M('news');
			$systeminfo['news'] = $news->count();
			$info = array_merge($systeminfo, $cl);
			S('info',$info,C('DataCache')*3600);
		}
		$this->assign('info',S('info'));
		//新闻显示
		if (!S('newslist')) {
			$news = M('news');
			$newslist = $news->order('Sortid,Dtime desc')->limit(10)->select();
			S('newslist', $newslist, C('DataCache')*3600);
		}
		$this->assign('newslist',S('newslist'));
		//在线人数统计
		if (!S('statis')) {
			$statis = M('statis');
			$usercount = $statis->count();
			S('usercount', $usercount, 120);
		}
		$this->assign('usercount',S('usercount'));
		$this->display();
	}
	//退出登录
	public function quit() {
		$statis = M('statis');
		$statis->where('Uid='.$_SESSION['ThinkUser']['ID'])->delete();
		session('[destroy]');	//销毁所有SESSION
		$this->redirect('Index/index');
	}
}
?>