<?php
return array(
	//'配置项'=>'配置值'
	'URL_PATHINFO_DEPR' => '/',								//修改分隔符
	'URL_ROUTER_ON' => true,								//开启路由功能
	'URL_CASE_INSENSITIVE' =>true,
	'TMPL_L_DELIM' => '<{',									//修改左右定界符
	'TMPL_R_DELIM' => '}>',
	'DEFAULT_CHARSET'=> 'utf-8',							//编码设置
	'SESSION_AUTO_START' => true,							//开启SESSION
	'URL_HTML_SUFFIX' => 'html|shmtl|xml', 					// 多个用 | 分割伪静态配置
	'VAR_PAGE'=>'page',										//分页变量
	//字段自动进行模糊查询
	'DB_LIKE_FIELDS'=>'Username|Ip|CompanyName|ContactName',
	'LOAD_EXT_CONFIG' => 'setting,core,db,webconfig',							//载入额外配置项文件
	'TOKEN_NAME' => '__hash__',  							// 令牌验证的表单隐藏字段名称
	'TOKEN_TYPE'=>'md5', 									//令牌哈希验证规则 默认为MD5
	
	'TMPL_PARSE_STRING' => array(
		'__APP__' => __ROOT__.'/index.php',					// 更改默认的__APP__ 替换规则
		'__JS__' => __ROOT__.'/Public/js',
		'__CSS__' => __ROOT__.'/Public/style/home',
		'__CSS_PATH__' => __ROOT__.'/Public/style',
		'__IMAGE_PATH__' => __ROOT__.'/Public/image',
		'__IMAGE__' => __ROOT__.'/Public/image/home',
		'__UPLOAD__' => __ROOT__,
	)
);
?>