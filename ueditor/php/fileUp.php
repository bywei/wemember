<?php
    /**
     * Created by JetBrains PhpStorm.
     * User: taoqili
     * Date: 12-7-26
     * Time: 上午10:32
     */
    header("Content-Type: text/html; charset=utf-8");
    error_reporting( E_ERROR | E_WARNING );
    include "Uploader.class.php";
    //上传配置
    $config = array(
        "savePath" => "../../Uploads/ueditor/file/" , //保存路径
        "allowFiles" => array( ".rar" , ".doc" , ".docx" , ".zip" , ".pdf" , ".txt" , ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg", ".ogg", ".mov", ".wmv", ".mp4", ".webm") , //文件允许格式
        "maxSize" => 100000, //文件大小限制，单位KB
        "fileNameFormat" => $_POST['fileNameFormat']
    );
    //生成上传实例对象并完成上传
    $up = new Uploader( "upfile" , $config );
	session_start();

    /**
     * 得到上传文件所对应的各个参数,数组结构
     * array(
     *     "originalName" => "",   //原始文件名
     *     "name" => "",           //新文件名
     *     "url" => "",            //返回的地址
     *     "size" => "",           //文件大小
     *     "type" => "" ,          //文件类型
     *     "state" => ""           //上传状态，上传成功时必须返回"SUCCESS"
     * )
     */
    $info = $up->getFileInfo();
	//判断是否上传成功，成功则入库
	if ($info['state'] == 'SUCCESS') {
		$arr['originalName'] = $info['originalName'];
		$arr['url'] = str_replace('../../','',$info['url']);
		$arr['size'] = $info['size'];
		$arr['type'] = str_replace('.','',$info['type']);
		$dbconfig = include_once '../../Home/Conf/db.php';
		require_once('Db.php');
		
		$uid = $_SESSION['ThinkUser']['ID'];
		$db = new DB($dbconfig['DB_HOST'],$dbconfig['DB_USER'],$dbconfig['DB_PWD'],$dbconfig['DB_NAME']);
		$sql = "INSERT INTO tp_file(Sid,Uid,FileName,FilePath,FileSize,FileType,Dtime) VALUES(3,'$uid','{$arr['originalName']}','{$arr['url']}','{$arr['size']}','{$arr['type']}',Now())";
		$db->add($sql);
	}
    /**
     * 向浏览器返回数据json数据
     * {
     *   'url'      :'a.rar',        //保存后的文件路径
     *   'fileType' :'.rar',         //文件描述，对图片来说在前端会添加到title属性上
     *   'original' :'编辑器.jpg',   //原始文件名
     *   'state'    :'SUCCESS'       //上传状态，成功时返回SUCCESS,其他任何值将原样返回至图片上传框中
     * }
     */
    echo '{"url":"' .$info[ "url" ] . '","fileType":"' . $info[ "type" ] . '","original":"' . $info[ "originalName" ] . '","state":"' . $info["state"] . '"}';

