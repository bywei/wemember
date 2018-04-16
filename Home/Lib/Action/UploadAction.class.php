<?php
//上传处理类
class UploadAction extends Action {
	//上传图片
	public function picdo() {
		$verifyToken = md5($_POST['timestamp']);
		$dirName = $_POST['dirName'];
		$Uid = intval($_POST['Uid']);
		$sid = intval($_POST['sid']);
		$user = M('user');
		if ($uresult = $user->where("ID = $Uid")->find()) {
			$comp=explode(',',$uresult['Competence']);			//当前用户权限获取
			array_pop($comp);
			if (!in_array(40,$comp)) {
				$this->operating($Uid,__ACTION__,1,'没有操作权限');
				exit('抱歉，您没有此操作权限');
			}
		}else {
			$this->operating($Uid,__ACTION__,1,'非法操作');
			exit('非法操作');
		}
		$file = M('file');
		//用户空间大小判断
		if ($file->where("Uid=$Uid")->sum('FileSize') >= C('UPLOAD_USER_CAPACITY')*1024*1024) {
			$this->operating($Uid,__ACTION__,1,'空间超出限制');
			exit('很遗憾的告诉您：您的云端空间已经超啦，请联系网站管理员增加空间容量');
		}
		//总空间大小判断
		if ($file->sum('FileSize') >= C('UPLOAD_CAPACITY')*1024*1024) {
			$this->operating($Uid,__ACTION__,1,'空间超出限制');
			exit('很遗憾的告诉您：云端空间已经不能再放任何东西了，请联系网站管理员增加空间容量');
		}
		if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
			import('ORG.Net.UploadFile');
			$upload = new UploadFile();											//实例化上传类
			$upload->maxSize  = intval(C('UPLOAD_FILE_PIC_SIZE'))*1024;				//设置附件上传大小
			$arrtype = explode('|',C('UPLOAD_FILE_PIC_TYPE'));
			$upload->allowExts  = $arrtype;										//设置附件上传类型
			$upload->autoSub = true;											//使用子目录保存上传文件 
			$upload->subType = 'date';											//子目录创建方式，默认为hash，可以设置为hash或者date
			$upload->savePath =  'Uploads/'.$dirName.$Uid.'/';							//设置附件上传目录
			if(!$upload->upload()){												//上传错误提示错误信息
				echo $upload->getErrorMsg();
			}else{																// 上传成功 获取上传文件信息
				$info =  $upload->getUploadFileInfo();
				$arrinfo=array();
				for ($i=0; $i<count($info); $i++) {
					$arrinfo=$info[$i];
				}
				$data['Sid'] = $sid;
				$data['Uid'] = $Uid;
				$data['FileName'] = $arrinfo['name'];
				$data['FilePath'] = $arrinfo['savepath'].$arrinfo['savename'];
				$data['FileSize'] = $arrinfo['size'];
				$data['FileType'] = $arrinfo['extension'];
				$data['Dtime'] = date('Y-m-d H:i:s');
				$file->add($data);
				$this->operating($Uid,__ACTION__,0,'上传成功');
				return true;
			}
		}else {
			$this->operating($Uid,__ACTION__,1,'非法请求');
			exit('非法请求');
		}
	}
	//上传文件
	public function filedo() {
		$verifyToken = md5($_POST['timestamp']);
		$dirName = $_POST['dirName'];
		$Uid = intval($_POST['Uid']);
		$sid = intval($_POST['sid']);
		$user = M('user');
		if ($uresult = $user->where("ID = $Uid")->find()) {
			$comp=explode(',',$uresult['Competence']);			//当前用户权限获取
			array_pop($comp);
			if (!in_array(40,$comp)) {
				exit('抱歉，您没有此操作权限');
			}
		}else {
			exit('抱歉，您没有此操作权限');
		}
		$file = M('file');
		//用户空间大小判断
		if ($file->where("Uid=$Uid")->sum('FileSize') >= C('UPLOAD_USER_CAPACITY')*1024*1024) {
			$this->operating($Uid,__ACTION__,1,'空间超出限制');
			exit('很遗憾的告诉您：您的云端空间已经超啦，请联系网站管理员增加空间容量');
		}
		//总空间大小判断
		if ($file->sum('FileSize') >= C('UPLOAD_CAPACITY')*1024*1024) {
			$this->operating($Uid,__ACTION__,1,'空间超出限制');
			exit('很遗憾的告诉您：云端空间已经不能再放任何东西了，请联系网站管理员增加空间容量');
		}
		if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
			import('ORG.Net.UploadFile');
			$upload = new UploadFile();											//实例化上传类
			$upload->maxSize  = intval(C('UPLOAD_FILE_FILE_SIZE'))*1024;				//设置附件上传大小
			$arrtype = explode('|',C('UPLOAD_FILE_FILE_TYPE'));
			$upload->allowExts  = $arrtype;										//设置附件上传类型
			$upload->autoSub = true;											//使用子目录保存上传文件 
			$upload->subType = 'date';											//子目录创建方式，默认为hash，可以设置为hash或者date
			$upload->savePath =  'Uploads/'.$dirName.'/'.$Uid.'/';				//设置附件上传目录
			if(!$upload->upload()){												//上传错误提示错误信息
				echo $upload->getErrorMsg();
			}else{																// 上传成功 获取上传文件信息
				$info =  $upload->getUploadFileInfo();
				$arrinfo=array();
				for ($i=0; $i<count($info); $i++) {
					$arrinfo=$info[$i];
				}
				$data['Sid'] = $sid;
				$data['Uid'] = $Uid;
				$data['FileName'] = $arrinfo['name'];
				$data['FilePath'] = $arrinfo['savepath'].$arrinfo['savename'];
				$data['FileSize'] = $arrinfo['size'];
				$data['FileType'] = $arrinfo['extension'];
				$data['Dtime'] = date('Y-m-d H:i:s');
				$file->add($data);
				$this->operating($Uid,__ACTION__,0,'上传成功');
				return true;
			}
		}else {
			$this->operating($Uid,__ACTION__,1,'非法请求');
			exit('非法请求');
		}
	}
	//操作记录
	public function operating($uid,$url,$status,$description) {
		$data = array();
		$data['Uid'] = $uid;
		$data['Url'] = $url;
		$data['Description'] = $description;
		$data['Ip'] = get_client_ip();
		$data['Status'] = $status;
		$data['Dtime'] = date('Y-m-d H:i:s');
		$operating = M('operating');
		$operating->add($data);
	}
}
?>