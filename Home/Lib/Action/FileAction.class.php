<?php
//文件管理类
class FileAction extends CommonAction {
	//文件管理
	public function filelist() {
		parent::userauth2(35);
		$sid = I('get.sid','');
		if ($sid!='') {
			$where['Sid'] = intval($sid);
		}else {
			$sid = 0;
			$where['Sid'] = 0;
		}
		$uid = $_SESSION['ThinkUser']['ID'];
		//判断出目录
		$dir = array();
		//判断是否是超级管理员组，如果不是则取出相应用户的文件夹及文件信息
		$where['Uid'] = $uid;
		
		$fileclass = M('fileclass');
		$file = M('file');
		//得出文件总数
		$filelist = $file->where("Uid = $uid")->select();
		//筛选快捷导航文件夹
		$list=$fileclass->where("Uid = $uid")->order('Sortid asc')->getField('ID,Sid,ClassName,Sortid');
		//筛选文件夹
		$volist = $fileclass->where($where)->order('Sortid asc')->select();
		//判断是否为空
		if (count($volist) > 0 || count($list) > 0) {
			$arr=$this->classid($list);
		}else {
			$volist = array();
			$arr = array();
		}
		if (count($filelist) <= 0) {
			$filelist = array();
		}
		$fileinfo = array();
		//文件总数
		$fileinfo['filecount'] = count($filelist);	
		//文件总容量
		$fileinfo['filesum'] = $file->where("Uid = $uid")->sum('FileSize');		
		//当前文件夹的文件总数												
		$fileinfo['count'] = count($volist)+count($filelist);
		//当前目录sid		
		$fileinfo['sid'] = $sid;		
		//用户云端剩余空间																	
		$fileinfo['scapacity'] = round((C('UPLOAD_USER_CAPACITY')*1024*1024-$fileinfo['filesum'])/1024/1024,2);
		//用户云端空间总容量	
		$fileinfo['capacity'] = C('UPLOAD_USER_CAPACITY');
		$this->assign('list',$arr);
		$this->assign('fileinfo',$fileinfo);
		$this->display();
	}
	//文件数据请求
	public function fileajax() {
		$sid = I('get.sid','');
		if ($sid!='') {
			$where['Sid'] = intval($sid);
		}else {
			$sid = 0;
			$where['Sid'] = 0;
		}
		$uid = $_SESSION['ThinkUser']['ID'];
		//取出相应用户的文件夹及文件信息
		$where['Uid'] = $uid;
		$fileclass = M('fileclass');
		$file = M('file');
		$volist = $fileclass->where("Uid = $uid AND Sid = $sid")->order('Sortid asc')->select();
		$filelist = $file->where($where)->order('Dtime desc')->select();
		//判断对应文件目录中的文件数量
		if (count($filelist) > 0) {
			for($i=0; $i<count($filelist); $i++) {
				switch ($filelist[$i]['FileType']) {
					case 'jpg':	
					case 'png':	
					case 'gif':	
					case 'bmp':	
							$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__UPLOAD__').'/'.$filelist[$i]['FilePath']; break;
					case 'gif':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/gif.png'; break;
					case 'flac':$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/flac.png'; break;
					case 'fla':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/fla.png'; break;
					case 'jpg':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/jpeg.png'; break;
					case 'bmp':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/bmp.png'; break;
					case 'png':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/png.png'; break;
					case 'tif':	
					case 'tiff':$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/tiff.png'; break;
					case 'mdb':	
					case 'accdb':$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/accdb.png'; break;
					case 'rar':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/rar.png'; break;
					case 'zip':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/zip.png'; break;
					case 'xlsx':
					case 'xls':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/xlsx.png'; break;
					case 'pptx':
					case 'ppt':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/pptx.png'; break;
					case 'doc':
					case 'docx':$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/docx.png'; break;
					case 'wmv':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/wmv.png'; break;
					case 'wav':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/wav.png'; break;
					case 'txt':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/text.png'; break;
					case 'pub':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/pub.png'; break;
					case 'psd':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/psd.png'; break;
					case 'pdf':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/pdf.png'; break;
					case 'mp4':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/mp4.png'; break;
					case 'mp3':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/mp3.png'; break;
					case 'mov':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/mov.png'; break;
					case 'midi':$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/midi.png'; break;
					case 'jsf':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/jsf.png'; break;
					case 'ini':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/ini.png'; break;
					case 'html':$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/html.png'; break;
					case 'css':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/css.png'; break;
					case 'avi':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/avi.png'; break;
					default: $filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/systeme.png'; break;
				}
			}
		}
		$html='';
		foreach($volist as $vo) {
			$html.='
			<div data-title="'.C('TMPL_PARSE_STRING.__APP__').'/File/filelist?sid='.$vo['ID'].'" class="dira">
				<dl class="dir Ddir" id="'.$vo['ID'].'">
					<dt><img src="'.C('TMPL_PARSE_STRING.__IMAGE__').'/yes.png" border="0" class="g" /><img src="'.C('TMPL_PARSE_STRING.__IMAGE__').'/dir.png" border="0" /></dt>
					<dd>'.$vo['ClassName'].'</dd>
					<div class="bj"><a href="javascript:;" id="diredit" class="'.$vo['ID'].'" >编辑</a><a href="javascript:;" id="dirdel" class="'.$vo['ID'].'" >删除</a></div>
				</dl>
			</div>';
		}
		foreach($filelist as $vo) {
			$html.='
			<dl class="dir f indel" id="'.$vo['ID'].'" title="no">
				<dt><img src="'.C('TMPL_PARSE_STRING.__IMAGE__').'/yes.png" border="0" class="g" /><img src="'.$vo['img'].'" border="0" class="f" title="'.$vo['FileName'].'：双击图片可进行预览" /></dt>
				<dd title="'.$vo['FileName'].'">'.$vo['FileName'].'</dd>
				<div class="bj"><a href="javascript:;" class="fedit" id="'.$vo['ID'].'">编辑</a><a href="javascript:;" id="del" class="'.$vo['ID'].'">删除</a><a href="'.C('TMPL_PARSE_STRING.__APP__').'/File/down?filename='.$vo['FilePath'].'">下载</a></div>
			</dl>';
		}
		echo $html;
	}
	//查看共享文件
	public function fileshare() {
		//验证用户权限
		parent::userauth2(77);
		$uid = $_SESSION['ThinkUser']['ID'];
		//判断出目录
		$dir = array();
		//判断是否是超级管理员组，如果不是则取出相应用户的文件夹及文件信息
		$where['Uid'] = $uid;
		$fileclass = M('fileclass');
		$file = M('file');
		//得出文件总数
		$filelist = $file->where("Share = 1")->select();
		//筛选快捷导航文件夹
		$list=$fileclass->where("Uid = $uid")->order('Sortid asc')->getField('ID,Sid,ClassName,Sortid');
		//筛选文件夹
		$volist = $fileclass->where($where)->order('Sortid asc')->select();
		//判断是否为空
		if (count($volist) > 0 || count($list) > 0) {
			$arr=$this->classid($list);
		}else {
			$volist = array();
			$arr = array();
		}
		if (count($filelist) <= 0) {
			$filelist = array();
		}
		$fileinfo = array();
		//文件总数
		$fileinfo['filecount'] = count($filelist);	
		//文件总容量
		$fileinfo['filesum'] = $file->where("Share = 1")->sum('FileSize');		
		//当前文件夹的文件总数												
		$fileinfo['count'] = count($volist)+count($filelist);	
		$this->assign('list',$arr);
		$this->assign('fileinfo',$fileinfo);
		$this->display();
	}
	//共享文件请求
	public function fileshareajax() {
		parent::userauth(77);
		$uid = $_SESSION['ThinkUser']['ID'];
		$fileclass = M('fileclass');
		$file = M('file');
		$volist = $fileclass->where("Uid = $uid")->order('Sortid asc')->select();
		$filelist = $file->where("Share = 1")->order('Dtime desc')->select();
		//判断对应文件目录中的文件数量
		if (count($filelist) > 0) {
			for($i=0; $i<count($filelist); $i++) {
				switch ($filelist[$i]['FileType']) {
					case 'jpg':	
					case 'png':	
					case 'gif':	
					case 'bmp':	
							$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__UPLOAD__').'/'.$filelist[$i]['FilePath']; break;
					case 'gif':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/gif.png'; break;
					case 'flac':$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/flac.png'; break;
					case 'fla':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/fla.png'; break;
					case 'jpg':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/jpeg.png'; break;
					case 'bmp':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/bmp.png'; break;
					case 'png':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/png.png'; break;
					case 'tif':	
					case 'tiff':$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/tiff.png'; break;
					case 'mdb':	
					case 'accdb':$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/accdb.png'; break;
					case 'rar':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/rar.png'; break;
					case 'zip':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/zip.png'; break;
					case 'xlsx':
					case 'xls':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/xlsx.png'; break;
					case 'pptx':
					case 'ppt':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/pptx.png'; break;
					case 'doc':
					case 'docx':$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/docx.png'; break;
					case 'wmv':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/wmv.png'; break;
					case 'wav':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/wav.png'; break;
					case 'txt':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/text.png'; break;
					case 'pub':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/pub.png'; break;
					case 'psd':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/psd.png'; break;
					case 'pdf':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/pdf.png'; break;
					case 'mp4':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/mp4.png'; break;
					case 'mp3':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/mp3.png'; break;
					case 'mov':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/mov.png'; break;
					case 'midi':$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/midi.png'; break;
					case 'jsf':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/jsf.png'; break;
					case 'ini':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/ini.png'; break;
					case 'html':$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/html.png'; break;
					case 'css':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/css.png'; break;
					case 'avi':	$filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/avi.png'; break;
					default: $filelist[$i]['img'] = C('TMPL_PARSE_STRING.__IMAGE__').'/file/systeme.png'; break;
				}
			}
		}
		$html='';
		foreach($filelist as $vo) {
			$html.='
			<dl class="dir f indel" id="'.$vo['ID'].'" title="no">
				<dt><img src="'.C('TMPL_PARSE_STRING.__IMAGE__').'/yes.png" border="0" class="g" /><img src="'.$vo['img'].'" border="0" class="f" title="'.$vo['FileName'].'：双击图片可进行预览" /></dt>
				<dd title="'.$vo['FileName'].'">'.$vo['FileName'].'</dd>
				<div class="bj"><a href="'.C('TMPL_PARSE_STRING.__APP__').'/File/down?filename='.$vo['FilePath'].'">下载</a></div>
			</dl>';
		}
		echo $html;
	}
	//添加分类目录
	public function fileclass_add() {
		parent::win_userauth(36);
		$sid = I('get.sid','');
		$uid = $_SESSION['ThinkUser']['ID'];
		$fileclass = M('fileclass');
		$list=$fileclass->where("Uid=$uid")->order('Sortid asc')->getField('ID,Sid,ClassName,Sortid');
		$arr=$this->classid($list);
		$this->assign('list',$arr);
		$this->assign('sid',$sid);
		$this->display('fileclassadd');
	}
	//添加分类处理目录
	public function fileclass_add_do() {
		parent::userauth(36);
		if ($this->isAjax()) {
			$data['ClassName'] = I('post.classname','','htmlspecialchars');
			$data['Sid'] = I('post.sid','');
			$data['Description'] = I('post.description','','htmlspecialchars');
			$data['Sortid'] = I('post.sortid','');
			$data['Status'] = I('post.status','');
			$data['Uid'] = $_SESSION['ThinkUser']['ID'];
			$fileclass = D('Fileclass');
			if ($fileclass->create($data)) {
				$fileclass->add();
				parent::operating(__ACTION__,0,'新增成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'新增失败：'.$fileclass->getError());
				R('Public/errjson',array($fileclass->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//修改分类
	public function fileclass_edit() {
		parent::win_userauth(37);
		$id = I('get.id','');
		if ($id=='' || !is_numeric($id)) {
			parent::operating(__ACTION__,1,'参数错误');
			$this->content='参数ID类型错误，请关闭本窗口';
			exit($this->display('Public:err'));
		}
		$id=intval($id);
		$fileclass=M('fileclass');
		$uid = $_SESSION['ThinkUser']['ID'];
		//获取分类信息
		$data=array('ID' => $id,'Uid' => $uid);
		if ($result=$fileclass->where($data)->find()) {
			$list=$fileclass->where("Uid=$uid")->order('Sortid asc')->getField('ID,Sid,ClassName,Sortid');
			$arr=$this->classid($list);
			$this->assign('list',$arr);
			$this->assign('result',$result);
			$this->display('fileclassedit');
		}else {
			parent::operating(__ACTION__,1,'数据不存在');
			$this->content='没有找到相关数据，请关闭本窗口';
			$this->display('Public:err');
		}
	}
	//修改分类处理
	public function fileclass_edit_do() {
		parent::userauth(37);
		if ($this->isAjax()) {
			$data['ID'] = I('post.id','');
			$data['ClassName'] = I('post.classname','','htmlspecialchars');
			$data['Sid'] = I('post.sid','');
			$data['Description'] = I('post.description','','htmlspecialchars');
			$data['Sortid'] = I('post.sortid','');
			$data['Status'] = I('post.status','');
			
			$fileclass = D('Fileclass');
			if ($fileclass->create($data)) {
				$fileclass->save();
				parent::operating(__ACTION__,0,'更新成功');
				R('Public/errjson',array('ok'));
			}else {
				parent::operating(__ACTION__,1,'更新失败：'.$fileclass->getError());
				R('Public/errjson',array($fileclass->getError()));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//无限循环分类
	protected function classid($volist,$nan=0,$html='──',$level=0) {
		$arr=array();
		foreach($volist as $val) {
			if ($val['Sid'] == $nan) {
				$val['classname'] = $val['ClassName'];
				$val['html'] = str_repeat($html,$level);
				$arr[] = $val;
				$arr = array_merge($arr,self::classid($volist,$val['ID'],$html,$level+1));
			}
		}
		return $arr;
	}
	//无限循环指定分类（指定下级）
	protected function xclassid($volist,$nan) {
		$arr=array();
		foreach($volist as $val) {
			if ($val['Sid'] == $nan) {
				$arr[] = $val;
				$arr = array_merge($arr,self::classid($volist,$val['ID']));
			}
		}
		return $arr;
	}
	//删除分类
	public function dirdel() {
		//验证用户权限
		parent::userauth(38);
		//判断是否是ajax请求
		if ($this->isAjax()) {
			$id = I('post.id','');
			if ($id=='' || !is_numeric($id)) {
				parent::operating(__ACTION__,1,'参数错误');
				R('Public/errjson',array('参数ID类型错误'));
			}else {
				$uid = $_SESSION['ThinkUser']['ID'];
				$id = intval($id);
				
				$fileclass = M('fileclass');
				$file = M('file');
				$fwhere['ID'] = array('in','1,2,3');
				if ($fileclass->where($fwhere)->count()) {
					parent::operating(__ACTION__,1,'删除系统默认文件夹失败');
					R('Public/errjson',array('不能删除系统默认的文件夹'));
				}
				if ($fileclass->where("ID=$id AND Uid=$uid")->getField('ID')) {
					//选出对应文件夹
					$list = $fileclass->order('Sortid asc')->select();
					$arr = $this->xclassid($list,$id);
					//判断有没有下级目录
					if (count($arr) > 0) {
						//循环遍历出来的数组
						for($i=0; $i<count($arr); $i++) {
							$arrfile = array();
							//查找file表里有没有对应该目录的文件
							$arrfile = $file->where('Sid='.$arr[$i]['ID'])->select();
							//判断返回的数组是否有数据
							if (count($arrfile) > 0) {
								//循环删除所有属于该目录的文件及数据
								for($j=0; $j<count($arrfile); $j++) {
									$this->dirfiledel($arrfile[$j]['ID'],$arrfile[$j]['FilePath']);
								}
							}
							//删除目录数据
							$fileclass->where('ID='.$arr[$i]['ID'])->delete();
						}
					}
					//删除当前目录下的所有文件
					$filelist = $file->where("Sid=$id AND Uid=$uid")->select();
					//判断有无数据,有数据则循环删除
					if (count($filelist)>0) {
						for($i=0; $i<count($filelist); $i++) {
							$this->dirfiledel($filelist[$i]['ID'],$filelist[$i]['FilePath']);
						}
					}
					//删除当前目录文件夹
					$fileclass->where("ID=$id AND Uid=$uid")->delete();
					parent::operating(__ACTION__,0,'删除文件及文件夹成功');
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
	//文件夹附带删除文件方法
	public function dirfiledel($id,$filepath) {
		parent::userauth(42);
		$id = intval($id);
		if ($id!='' && is_numeric($id)) {
			$file = M('file');
			if (@unlink($filepath)) {
				$file->where("ID=$id")->delete();
			}else {
				R('Public/errjson',array('删除文件失败，可能是没有操作权限'));
			}
		}else {
			R('Public/errjson',array('没有找到相关文件'));
		}
	}
	//修改文件信息
	public function fileedit() {
		parent::win_userauth(41);
		$id = I('get.id','');
		if ($id=='' || !is_numeric($id)) {
			parent::operating(__ACTION__,1,'参数错误');
			$this->content='参数ID类型错误，请关闭本窗口';
			exit($this->display('Public:err'));
		}
		$id=intval($id);
		$file=M('file');
		$fileclass=M('fileclass');
		//获取分类信息
		$data=array('ID' => $id,'Uid' => $_SESSION['ThinkUser']['ID']);
		if ($result=$file->where($data)->find()) {
			$list=$fileclass->where('Uid='.$_SESSION['ThinkUser']['ID'])->order('Sortid asc')->getField('ID,Sid,ClassName,Sortid');
			$arr=$this->classid($list);
			$this->assign('list',$arr);
			$this->assign('result',$result);
			$this->display('fileedit');
		}else {
			parent::operating(__ACTION__,1,'数据不存在');
			$this->content='没有找到相关数据，请关闭本窗口';
			$this->display('Public:err');
		}
	}
	//修改文件处理
	public function fileedit_do() {
		//验证用户权限
		parent::userauth(41);
		if ($this->isAjax()) {
			$data['ID'] = I('post.id','');
			$data['FileName'] = I('post.filename','','htmlspecialchars');
			$data['Sid'] = I('post.sid','');
			$data['Description'] = I('post.description','','htmlspecialchars');
			$data['Share'] = I('post.share',0);
			if (mb_strlen($data['FileName']) > 50) {
				R('Public/errjson',array('请将文件名控制在50个字符以内'));
			}
			if (mb_strlen($data['Description']) > 50) {
				R('Public/errjson',array('请将描述控制在50个字符以内'));
			}
			$file = M('file');
			if ($file->where("ID=".$data['ID'])->save($data)) {
				parent::operating(__ACTION__,0,'修改成功');
				R('Public/errjson',array('ok'));
			}else {
				R('Public/errjson',array($data['ID']));
			}
		}else {
			parent::operating(__ACTION__,1,'非法请求');
			R('Public/errjson',array('非法请求'));
		}
	}
	//下载文件
	public function down() {
		parent::userauth2(43);
		$string = I('get.filename','');
		if (file_exists($string)) {
			$string=iconv("utf-8","gb2312",$string);
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename=".basename($string));  
			readfile($string);
		}else {
			parent::operating(__ACTION__,1,'文件不存在：'.$string);
			$this->error('文件不存在');
		}
	}
	//删除文件
	public function filedel() {
		parent::userauth(42);
		$id = I('post.id','');
		$id = intval($id);
		if ($id!='' && is_numeric($id)) {
			$file = M('file');
			if ($result=$file->where("ID=$id")->find()) {
				if (@unlink($result['FilePath'])) {
					$file->where("ID=$id AND Uid=".$_SESSION['ThinkUser']['ID'])->delete();
					parent::operating(__ACTION__,0,'删除成功');
					R('Public/errjson',array('ok'));
				}else {
					parent::operating(__ACTION__,1,'删除失败');
					R('Public/errjson',array('删除文件失败，可能是没有操作权限'));
				}
			}else {
				parent::operating(__ACTION__,1,'没有找到相关文件');
				R('Public/errjson',array('没有找到相关文件'));
			}
		}else {
			parent::operating(__ACTION__,1,'没有找到相关文件');
			R('Public/errjson',array('没有找到相关文件'));
		}
	}
	//批量删除文件
	public function fileindel() {
		parent::userauth(42);
		$delid = I('post.delid','');
		if ($delid=='') {
			R('Public/errjson',array('请选中后再删除'));
		}
		$arrdel = explode(',',$delid);
		array_pop($arrdel);
		for($i=0; $i<count($arrdel); $i++) {
			$id = $arrdel[$i];
			if ($id!='' && is_numeric($id)) {
				$file = M('file');
				if ($result=$file->where("ID=$id AND Uid=".$_SESSION['ThinkUser']['ID'])->find()) {
					if (@unlink($result['FilePath'])) {
						$file->where("ID=$id")->delete();
					}else {
						parent::operating(__ACTION__,1,'删除文件失败，可能是没有操作权限');
						R('Public/errjson',array('删除文件失败，可能是没有操作权限'));
					}
				}else {
					parent::operating(__ACTION__,1,'没有找到相关文件');
					R('Public/errjson',array('没有找到相关文件'));
				}
			}else {
				parent::operating(__ACTION__,1,'没有找到相关文件');
				R('Public/errjson',array('没有找到相关文件'));
			}
		}
		parent::operating(__ACTION__,0,'删除成功');
		R('Public/errjson',array('ok'));
	}
	//移动文件
	public function filemove() {
		parent::win_userauth(39);
		$moveid = I('get.moveid','');
		if ($moveid=='') {
			$this->error('没有选中相关文件');
		}
		$fileclass = M('fileclass');
		$list = $fileclass->where('Uid='.$_SESSION['ThinkUser']['ID'])->order('Sortid asc')->getField('ID,Sid,ClassName,Sortid');
		$arr = $this->classid($list);
		$this->assign('list',$arr);
		$this->assign('moveid',$moveid);
		$this->display();
	}
	//移动文件处理
	public function filemovedo() {
		parent::userauth(39);
		$moveid = I('post.moveid','');
		$sid = I('post.sid','');
		//切割文件ID
		$arrid = explode(',',$moveid);
		array_pop($arrid);
		//判断目录ID是否正确
		if ($sid!='' && is_numeric($sid)) {
			$file = M('file');
			$data['Sid'] = $sid;
			//循环更新对应文件目录
			for($i=0; $i<count($arrid); $i++) {
				if (!$file->where('ID='.$arrid[$i])->save($data)) {
					//失败直接返回
					parent::operating(__ACTION__,1,'文件移动失败');
					R('Public/errjson',array('文件移动失败'));
				}
			}
			parent::operating(__ACTION__,0,'文件移动成功');
			R('Public/errjson',array('ok'));
		}else {
			parent::operating(__ACTION__,1,'目录ID获取失败');
			R('Public/errjson',array('目录ID获取失败'));
		}
	}
	//上传图片
	public function uploadpic() {
		parent::userauth2(40);
		$sid = I('get.sid','');
		$sid = intval($sid);
		$type = explode('|',C('UPLOAD_FILE_PIC_TYPE'));
		$filetype='';
		foreach($type as $val) {
			$filetype .= '*.'.$val.";";
		}
		$fileclass = M('fileclass');
		$list = $fileclass->where('Uid='.$_SESSION['ThinkUser']['ID'])->order('Sortid asc')->getField('ID,Sid,ClassName,Sortid');
		if (count($list) > 0) {
			$arr = $this->classid($list);
		}else {
			$arr = array();
		}
		$this->assign('filetype',$filetype);
		$this->assign('list',$arr);
		$this->assign('sid',$sid);
		$this->assign('filesize',C('UPLOAD_FILE_PIC_SIZE'));
		$this->display('uploadpic');
	}
	//上传文件
	public function uploadfile() {
		parent::userauth2(40);
		$sid = I('get.sid','');
		$sid = intval($sid);
		$type = explode('|',C('UPLOAD_FILE_FILE_TYPE'));
		$filetype='';
		foreach($type as $val) {
			$filetype .= '*.'.$val.";";
		}
		$fileclass = M('fileclass');
		$list = $fileclass->where('Uid='.$_SESSION['ThinkUser']['ID'])->order('Sortid asc')->getField('ID,Sid,ClassName,Sortid');
		if (count($list) > 0) {
			$arr = $this->classid($list);
		}else {
			$arr = array();
		}
		$this->assign('filetype',$filetype);
		$this->assign('list',$arr);
		$this->assign('sid',$sid);
		$this->assign('filesize',C('UPLOAD_FILE_FILE_SIZE'));
		$this->display('uploadfile');
	}
}
?>