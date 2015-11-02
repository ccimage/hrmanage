<?php
return array(
	//'配置项'=>'配置值'
	//数据库配置信息
	'DB_TYPE'   => 'sqlite', // 数据库类型
	//'DB_HOST'   => 'localhost', // 服务器地址
	'DB_NAME'   => './Public/data/tastegame_hr', // 数据库名
	//'DB_USER'   => 'root', // 用户名
	//'DB_PWD'    => '', // 密码
	//'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => 'hr_', // 数据库表前缀
	'DB_CHARSET'	=> 'utf8',
	//其他配置
	'URL_CASE_INSENSITIVE' =>false,
	'URL_MODEL' => 2,
	'TMPL_VAR_IDENTIFY' => 'array',
	//'TMPL_L_DELIM'=>'<%',
	//'TMPL_R_DELIM'=>'%>',
	'SESSION_AUTO_START' => true, //是否开启session
	'SHOW_PAGE_TRACE' =>false,
	'URL_PARAMS_BIND_TYPE'=>1,

	//自定义配置信息
	'SITE_TITLE' => '',
	'USER_AUTH_KEY' => 'ts-2015-uid', //session中存用户id
	'USER_AUTH_NAME' => 'ts-2015-name', //存用户名
	'AUTH_CODE' => 'B42#a$-CODE',
); 