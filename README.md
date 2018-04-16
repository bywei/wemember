#微店客户进货管理系统(wemember)\
  使用ThinkPHP开发，实现微商手机端进货，可对接微信公众号或者移动M站
##APP演示效果如下：
  ![screenshot](https://github.com/bywei/wemember/raw/master/Screenshots/app1.png)
  ![screenshot](https://github.com/bywei/wemember/raw/master/Screenshots/app2.png)
  ![screenshot](https://github.com/bywei/wemember/raw/master/Screenshots/app3.png)
  ![screenshot](https://github.com/bywei/wemember/raw/master/Screenshots/app3.png)

#管理后台
http://localhost/index.php/Admin/main/
默认用户名密码：admin/123456

![screenshot](https://github.com/bywei/wemember/raw/master/Screenshots/admin2.png)

客户订单管理

![screenshot](https://github.com/bywei/wemember/raw/master/Screenshots/admin1.png)

#ThinkPHP配置文件大全
```
//定义项目名称
define('APP_NAME', 'App');
//定义项目路径
define('APP_PATH', './App/');
//加载框架入文件
require './App/ThinkPHP/ThinkPHP.php';
//设置编译缓存目录
define('RUNTIME_PATH','./App/temp/');
//设置编译缓存文件名
define('RUNTIME_PATH','./App/temp/runtime_cache.php');
//开启调试模式
define('APP_DEBUG', true);
```
#项目配置文件大全
```
return array(
	'DEFAULT_MODULE'     => 'Index', //默认模块
	'URL_MODEL'          => '2', //URL模式
	'SESSION_AUTO_START' => true, //是否开启session
	'APP_STATUS' => 'debug', //应用调试模式状态
	'APP_GROUP_LIST' => 'Home,Admin', //项目分组设定
	'DEFAULT_GROUP'  => 'Home', //默认分组
	'LOAD_EXT_CONFIG' => 'user,db', // 加载扩展配置文件
	"LOAD_EXT_FILE"=>"user,db"	//函数加载
	'URL_HTML_SUFFIX'=>'html|shmtl|xml' // 多个用 | 分割伪静态配置
	'URL_ROUTER_ON'   => true, //开启路由
	'URL_ROUTE_RULES' => array( //定义路由规则
		'news/:year/:month/:day' => array('News/archive', 'status=1'),
		'news/:id'  => 'News/read',
		'news/read/:id' => '/news/:1',
	),
	'URL_CASE_INSENSITIVE' =>true	//URL访问不区分大小写
	'TMPL_L_DELIM'=>'<{',		//输出左右分隔符
	'TMPL_R_DELIM'=>'}>',
	'TMPL_TEMPLATE_SUFFIX' => '.tpl',	//修改模版文件的后缀名
	'TMPL_FILE_DEPR' => '_',	//更改模版目录层次 Index_index.html 类名_方法名
	'DEFAULT_THEME' => 'my',	//设置默认模版主题
	'TMPL_DETECT_THEME' => true,	//自动侦测模版主题
	'THEME_LIST' => 'your,my',	//支持的模版主题列表
	'TAG_NESTED_LEVEL' => 5,	//配置标签嵌套层级
	'VAR_PAGE'=>'page',		//分页变量
	'DB_LIKE_FIELDS' => 'title|content',	//设置数据库字段默认进行模糊查询
	'LOG_RECORD' => true,		// 开启日志记录
	'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR,WARN,NOTICE', // 只记录EMERG ALERT CRIT ERR 错误
	'HTML_CACHE_ON' => true		//是否开启静态缓存
	'HTML_CACHE_TIME' => '60'	//静态缓存时间，单位秒
	'DB_FIELDTYPE_CHECK'=>true, 	 // 开启字段类型验证	
	'TOKEN_ON'=>true,  // 是否开启令牌验证
	'TOKEN_NAME'=>'__hash__',   	 // 令牌验证的表单隐藏字段名称
	'TOKEN_TYPE'=>'md5', 		 //令牌哈希验证规则 默认为MD5
	'TOKEN_RESET'=>true, 		 //令牌验证出错后是否重置令牌 默认为true


        //数据库配置信息
        'DB_TYPE'   => 'mysql', // 数据库类型
        'DB_HOST'   => 'localhost', // 服务器地址
        'DB_NAME'   => 'thinkphp', // 数据库名
        'DB_USER'   => 'root', // 用户名
        'DB_PWD'    => '', // 密码
        'DB_PORT'   => 3306, // 端口
        'DB_PREFIX' => 'think_', // 数据库表前缀 
	'DB_DSN' => 'mysql://username:password@localhost:3306/DbName'
	'DEFAULT_CHARSET'=> 'utf-8'	//编码设置
);
load("@.user")	//手动载入函数库
```

# 使用说明

1.新建数据库

2.修改数据库配置：Home/Conf/db.php

3.发布项目到PHP运行环境

# 版本说明
```
wemember ver 1.0.0 H5前端进货，后端管理用户信息功能。
```
## 关于作者

bywei = 程序员百味

个人博客 = "http://www.bywei.cn"


