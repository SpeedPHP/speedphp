<?php
/**
 * SpeedPHP Framework, Enjoy the Speed of PHP!
 * 
 * @author jake <jake@speedphp.com>
 * @link http://www.speedphp.com/
 * @copyright Copyright (c) 2008 - 2012, www.speedphp.com.
 * @license http://www.speedphp.com/license.txt
 */

define('SP_VERSION', '3.2.2');

defined('APP_PATH') or define('APP_PATH', dirname(__FILE__).'/app');
defined('SP_PATH') or define('SP_PATH', dirname(__FILE__).'/SpeedPHP');

$__default_configs =  array(
	'mode' => 'debug', // 应用程序模式，默认为调试模式
	'sp_drivers_path' => SP_PATH.'/Drivers', // 框架各类驱动文件目录
	'sp_include_path' => array( SP_PATH.'/Extensions' ), // 框架扩展功能载入路径
	'launch' => array(), // 自动执行点的根节点
	
	'auto_load_controller' => array('spArgs'), // 控制器自动加载的扩展类名
	'auto_load_model' => array('spPager','spVerifier','spCache','spLinker'), // 模型自动加载的扩展类名
	
	'sp_error_throw_exception' => false, // 是否抛出异常
	'allow_trace_onrelease' => false, // 是否允许在部署模式下输出调试信息
	'sp_notice_php' => SP_PATH."/Misc/notice.php", // 框架默认的错误提示程序
	
	'inst_class' => array(), // 已实例化的类名称
	'import_file' => array(), // 已经载入的文件
	'sp_access_store' => array(), // 使用spAccess保存到内存的变量
	'view_registered_functions' => array(), // 视图内注册的函数记录

	'default_controller' => 'main', // 默认的控制器名称
	'default_action' => 'index',  // 默认的动作名称
	'url_controller' => 'c',  // 请求时使用的控制器变量标识
	'url_action' => 'a',  // 请求时使用的动作变量标识

	'auto_session' => true, // 是否自动开启SESSION支持
	'dispatcher_error' => "spError('路由错误，请检查控制器目录下是否存在该控制器/动作。');", // 定义处理路由错误的函数
	
	'sp_cache' => APP_PATH.'/tmp', // 框架临时文件夹目录
	'sp_app_id' => '',  // 框架识别ID
	'controller_path' => APP_PATH.'/controller', // 用户控制器程序的路径定义
	'model_path' => APP_PATH.'/model', // 用户模型程序的路径定义


	'url' => array( // URL设置
		'url_path_base' => '', // URL的根目录访问地址，默认为空则是入口文件index.php
	),
	
	'db' => array(  // 数据库连接配置
		'driver' => 'mysql',   // 驱动类型
		'host' => 'localhost', // 数据库地址
		'port' => 3306,        // 端口
		'login' => 'root',     // 用户名
		'password' => '',      // 密码
		'database' => '',      // 库名称
		'prefix' => '',           // 表前缀
		'persistent' => false,    // 是否使用长链接
	),
	'db_driver_path' => '', // 自定义数据库驱动文件地址
	'db_spdb_full_tblname' => true, // spDB是否使用表全名
	
	'view' => array( // 视图配置
		'enabled' => true, // 开启视图
		'config' =>array(
			'template_dir' => APP_PATH.'/tpl', // 模板目录
			'compile_dir' => APP_PATH.'/tmp', // 编译目录
			'cache_dir' => APP_PATH.'/tmp', // 缓存目录
			'left_delimiter' => '{',  // smarty左限定符
			'right_delimiter' => '}', // smarty右限定符
			'auto_literal' => true, // Smarty3新特性
		),
		'debugging' => false, // 是否开启视图调试功能，在部署模式下无法开启视图调试功能
		'engine_name' => 'Smarty', // 模板引擎的类名称，默认为Smarty
		'engine_path' => SP_PATH.'/Drivers/Smarty/Smarty.class.php', // 模板引擎主类路径
		'auto_display' => false, // 是否使用自动输出模板功能
		'auto_display_sep' => '/', // 自动输出模板的拼装模式，/为按目录方式拼装，_为按下划线方式，以此类推
		'auto_display_suffix' => '.html', // 自动输出模板的后缀名
	),
	
	'lang' => array(), // 多语言设置，键是每种语言的名称，而值可以是default（默认语言），语言文件地址或者是翻译函数
					// 同时请注意，在使用语言文件并且文件中存在中文等时，请将文件设置成UTF8编码
	'ext' => array(), // 扩展使用的配置根目录
		
	'include_path' => array(
		APP_PATH.'/include',
	), // 用户程序扩展类载入路径
);

$GLOBALS['G_SP'] = spConfigReady($__default_configs, $spConfig);

if('debug' == strtolower($GLOBALS['G_SP']['mode'])){
	define("SP_DEBUG", true);
}else{
	define("SP_DEBUG", false);
	error_reporting(0);
}
@set_magic_quotes_runtime(0);

if($GLOBALS['G_SP']['auto_session'])@session_start();

// 当在二级目录中使用SpeedPHP框架时，自动获取当前访问的文件名
if('' == $GLOBALS['G_SP']['url']["url_path_base"]){
	if(basename($_SERVER['SCRIPT_NAME']) === basename($_SERVER['SCRIPT_FILENAME']))
		$GLOBALS['G_SP']['url']["url_path_base"] = $_SERVER['SCRIPT_NAME'];
	elseif (basename($_SERVER['PHP_SELF']) === basename($_SERVER['SCRIPT_FILENAME']))
		$GLOBALS['G_SP']['url']["url_path_base"] = $_SERVER['PHP_SELF'];
	elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === basename($_SERVER['SCRIPT_FILENAME']))
		$GLOBALS['G_SP']['url']["url_path_base"] = $_SERVER['ORIG_SCRIPT_NAME'];
}


// 构造执行路由
$__controller = isset($_REQUEST[$GLOBALS['G_SP']["url_controller"]]) ? 
	$_REQUEST[$GLOBALS['G_SP']["url_controller"]] : 
	$GLOBALS['G_SP']["default_controller"];
$__action = isset($_REQUEST[$GLOBALS['G_SP']["url_action"]]) ? 
	$_REQUEST[$GLOBALS['G_SP']["url_action"]] : 
	$GLOBALS['G_SP']["default_action"];

class spController { 

	public $v;
	
	private $__template_vals = array();
	
	public $layout = null;
	
	public function __construct()
	{	
		if(true == $GLOBALS['G_SP']['view']['enabled']){
			$this->v = spClass($GLOBALS['G_SP']['view']['engine_name'],null,$GLOBALS['G_SP']['view']['engine_path']);
			foreach( $GLOBALS['G_SP']['view']['config'] as $key => $value )$this->v->{$key} = $value;

			if( !empty($GLOBALS['G_SP']['sp_app_id']) && isset($this->v->compile_id) )$this->v->compile_id = $GLOBALS['G_SP']['sp_app_id'];
			if( !is_writable($GLOBALS['G_SP']['view']['config']['compile_dir']) )spError('View Engine: complie_dir is not writable!');
			if( !is_writable($GLOBALS['G_SP']['view']['config']['cache_dir']) )spError('View Engine: cache_dir is not writable!');

			spAddViewFunction('spUrl', '__template_spUrl');
			$this->__template_url = array(
				'root' => '网址根目录',
				'base' => '应用根目录',
				'current' => '当前目录',
			);
			$this->__init();
		}
	}

    public function jump($url, $delay = 0){
		echo "<html><head><meta http-equiv='refresh' content='{$delay};url={$url}'></head><body></body></html>";
		exit;
    }

    public function error($msg, $url = ''){
		$url = empty($url) ? "window.history.back();" : "location.href=\"{$url}\";";
		echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script>function sptips(){alert(\"{$msg}\");{$url}}</script></head><body onload=\"sptips()\"></body></html>";
		exit;
    }

    public function success($msg, $url = ''){
		$url = empty($url) ? "window.history.back();" : "location.href=\"{$url}\";";
		echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script>function sptips(){alert(\"{$msg}\");{$url}}</script></head><body onload=\"sptips()\"></body></html>";
		exit;
    }

	public function __set($name, $value)
	{
		$this->__template_vals[$name] = $value;
	}

	public function __get($name)
	{
		return $this->__template_vals[$name];
	}
	
	private function __init()
	{
	
	}
	
	public function display($tplname, $output = true, $check_exists = true) 
	{
		$tplname = $GLOBALS['G_SP']['view']['config']['template_dir'].'/'.ltrim($tplname, '/');
		if( !is_readable($tplname) ){
			if( $check_exists )
				spError("View Engine: Unable to load template file '{$tplname}'");
			else
				return false;
		}
		if( $this->layout ){
			$this->__template_file = $tplname;
			$tplname = $this->layout;
		}
		@ob_start();
		if($GLOBALS['G_SP']['view']['enabled']){
			foreach( $GLOBALS['G_SP']["view_registered_functions"] as $alias => $func )$this->v->registerPlugin("function", $alias, $func);
			$this->v->assign($this->__template_vals);
			if($GLOBALS['G_SP']['view']['debugging'] && SP_DEBUG)$this->v->debugging = true;
			try {
					$this->v->display($tplname);
			} catch (Exception $e) {
				spError( 'View Engine: '.$e->getMessage() );
			}
		}else{
			extract($this->__template_vals);
			include $tplname;
		}
		if( !$output )return ob_get_clean();
	}

	public function args($name = null, $default = null, $callback_funcname = null)
	{
		if(!isset($GLOBALS['G_SP']['request_variables'][$name]))return $default;
		$arg = $name ? $GLOBALS['G_SP']['request_variables'][$name] : $GLOBALS['G_SP']['request_variables'];
		if($callback_funcname)array_walk_recursive($arg, $callback_funcname);
		return $arg;
	}
}

/**
 * spModel 系统模型类，所有模型类的父类 应用程序中的每个模型类都应继承于spModel。
 */
class spModel {
	/**
	 * 供检验值的规则与返回信息
	 */
	public $verifier = null;
	
	/**
	 * 增加的自定义验证函数
	 */
	public $addrules = array();
	/**
	 * 表主键
	 */
	public $pk;
	/**
	 * 表名称
	 */
	public $table;

	/**
	 * 关联描述
	 */
	public $linker = null;
	
	/**
	 * 表全名
	 */
	public $tbl_name = null;
	
	/**
	 * 数据驱动程序
	 */
	public $_db;

	/**
	 * 构造函数
	 */
	public function __construct()
	{
		if( null == $this->tbl_name )$this->tbl_name = $GLOBALS['G_SP']['db']['prefix'] . $this->table;
		if( '' == $GLOBALS['G_SP']['db_driver_path'] ){
			$GLOBALS['G_SP']['db_driver_path'] = $GLOBALS['G_SP']['sp_drivers_path'].'/'.$GLOBALS['G_SP']['db']['driver'].'.php';
		}
		$this->_db = spClass('db_'.$GLOBALS['G_SP']['db']['driver'], array(0=>$GLOBALS['G_SP']['db']), $GLOBALS['G_SP']['db_driver_path']);
	}

	/**
	 * 从数据表中查找一条记录
	 *
	 * @param conditions    查找条件，数组array("字段名"=>"查找值")或字符串，
	 * 请注意在使用字符串时将需要开发者自行使用escape来对输入值进行过滤
	 * @param sort    排序，等同于“ORDER BY ”
	 * @param fields    返回的字段范围，默认为返回全部字段的值
	 */
	public function find($conditions = null, $sort = null, $fields = null)
	{
		if( $record = $this->findAll($conditions, $sort, $fields, 1) ){
			return array_pop($record);
		}else{
			return false;
		}
	}
	
	/**
	 * 从数据表中查找记录
	 *
	 * @param conditions    查找条件，数组array("字段名"=>"查找值")或字符串，
	 * 请注意在使用字符串时将需要开发者自行使用escape来对输入值进行过滤
	 * @param sort    排序，等同于“ORDER BY ”
	 * @param fields    返回的字段范围，默认为返回全部字段的值
	 * @param limit    返回的结果数量限制，等同于“LIMIT ”，如$limit = " 3, 5"，即是从第3条记录（从0开始计算）开始获取，共获取5条记录
	 *                 如果limit值只有一个数字，则是指代从0条记录开始。
	 */
	public function findAll($conditions = null, $sort = null, $fields = null, $limit = null)
	{
		$where = "";
		$fields = empty($fields) ? "*" : $fields;
		if(is_array($conditions)){
			$join = array();
			foreach( $conditions as $key => $condition ){
				$condition = $this->escape($condition);
				$join[] = "{$key} = {$condition}";
			}
			$where = "WHERE ".join(" AND ",$join);
		}else{
			if(null != $conditions)$where = "WHERE ".$conditions;
		}
		if(null != $sort){
			$sort = "ORDER BY {$sort}";
		}else{
			$sort = "ORDER BY {$this->pk}";
		}
		$sql = "SELECT {$fields} FROM {$this->tbl_name} {$where} {$sort}";
		if(null != $limit)$sql = $this->_db->setlimit($sql, $limit);
		return $this->_db->getArray($sql);
	}
	/**
	 * 过滤转义字符
	 *
	 * @param value 需要进行过滤的值
	 */
	public function escape($value)
	{
		return $this->_db->__val_escape($value);
	}
	// __val_escape是val的别名，向前兼容
	public function __val_escape($value){return $this->escape($value);}
	
	/**
	 * 在数据表中新增一行数据
	 *
	 * @param row 数组形式，数组的键是数据表中的字段名，键对应的值是需要新增的数据。
	 */
	public function create($row)
	{
		if(!is_array($row))return false;
		$row = $this->__prepera_format($row);
		if(empty($row))return false;
		foreach($row as $key => $value){
			$cols[] = $key;
			$vals[] = $this->escape($value);
		}
		$col = join(',', $cols);
		$val = join(',', $vals);

		$sql = "INSERT INTO {$this->tbl_name} ({$col}) VALUES ({$val})";
		if( false != $this->_db->exec($sql) ){ // 获取当前新增的ID
			if( $newinserid = $this->_db->newinsertid() ){
				return $newinserid;
			}else{
				return array_pop( $this->find($row, "{$this->pk} DESC",$this->pk) );
			}
		}
		return false;
	}

	/**
	 * 按条件删除记录
	 *
	 * @param conditions 数组形式，查找条件，此参数的格式用法与find/findAll的查找条件参数是相同的。
	 */
	public function delete($conditions)
	{
		$where = "";
		if(is_array($conditions)){
			$join = array();
			foreach( $conditions as $key => $condition ){
				$condition = $this->escape($condition);
				$join[] = "{$key} = {$condition}";
			}
			$where = "WHERE ( ".join(" AND ",$join). ")";
		}else{
			if(null != $conditions)$where = "WHERE ( ".$conditions. ")";
		}
		$sql = "DELETE FROM {$this->tbl_name} {$where}";
		return $this->_db->exec($sql);
	}

	/**
	 * 按字段值查找一条记录
	 *
	 * @param field 字符串，对应数据表中的字段名
	 * @param value 字符串，对应的值
	 */
	public function findBy($field, $value)
	{
		return $this->find(array($field=>$value));
	}

	/**
	 * 按字段值修改一条记录
	 *
	 * @param conditions 数组形式，查找条件，此参数的格式用法与find/findAll的查找条件参数是相同的。
	 * @param field 字符串，对应数据表中的需要修改的字段名
	 * @param value 字符串，新值
	 */
	public function updateField($conditions, $field, $value)
	{
		return $this->update($conditions, array($field=>$value));
	}

	/**
	 * 使用SQL语句进行查找操作，等于进行find，findAll等操作
	 *
	 * @param sql 字符串，需要进行查找的SQL语句
	 */
	public function findSql($sql)
	{
		return $this->_db->getArray($sql);
	}

	/**
	 * 执行SQL语句，相等于执行新增，修改，删除等操作。
	 *
	 * @param sql 字符串，需要执行的SQL语句
	 */
	public function runSql($sql)
	{
		return $this->_db->exec($sql);
	}
	// query是runSql的别名，向前兼容
	public function query($sql){return $this->runSql($sql);}

	/**
	 * 返回最后执行的SQL语句供分析
	 */
	public function dumpSql()
	{
		return end( $this->_db->arrSql );
	}
	
	/**
	 * 返回上次执行update,create,delete,exec的影响行数
	 */
	public function affectedRows()
	{
		return $this->_db->affected_rows();
	}
	/**
	 * 计算符合条件的记录数量
	 *
	 * @param conditions 查找条件，数组array("字段名"=>"查找值")或字符串，
	 * 请注意在使用字符串时将需要开发者自行使用escape来对输入值进行过滤
	 */
	public function findCount($conditions = null)
	{
		$where = "";
		if(is_array($conditions)){
			$join = array();
			foreach( $conditions as $key => $condition ){
				$condition = $this->escape($condition);
				$join[] = "{$key} = {$condition}";
			}
			$where = "WHERE ".join(" AND ",$join);
		}else{
			if(null != $conditions)$where = "WHERE ".$conditions;
		}
		$sql = "SELECT COUNT({$this->pk}) AS SP_COUNTER FROM {$this->tbl_name} {$where}";
		$result = $this->_db->getArray($sql);
		return $result[0]['SP_COUNTER'];
	}

	/**
	 * 魔术函数，执行模型扩展类的自动加载及使用
	 */
	public function __call($name, $args)
	{
		if(in_array($name, $GLOBALS['G_SP']["auto_load_model"])){
			return spClass($name)->__input($this, $args);
		}elseif(!method_exists( $this, $name )){
			spError("方法 {$name} 未定义");
		}
	}

	/**
	 * 修改数据，该函数将根据参数中设置的条件而更新表中数据
	 * 
	 * @param conditions    数组形式，查找条件，此参数的格式用法与find/findAll的查找条件参数是相同的。
	 * @param row    数组形式，修改的数据，
	 *  此参数的格式用法与create的$row是相同的。在符合条件的记录中，将对$row设置的字段的数据进行修改。
	 */
	public function update($conditions, $row)
	{
		$where = "";
		$row = $this->__prepera_format($row);
		if(empty($row))return false;
		if(is_array($conditions)){
			$join = array();
			foreach( $conditions as $key => $condition ){
				$condition = $this->escape($condition);
				$join[] = "{$key} = {$condition}";
			}
			$where = "WHERE ".join(" AND ",$join);
		}else{
			if(null != $conditions)$where = "WHERE ".$conditions;
		}
		foreach($row as $key => $value){
			$value = $this->escape($value);
			$vals[] = "{$key} = {$value}";
		}
		$values = join(", ",$vals);
		$sql = "UPDATE {$this->tbl_name} SET {$values} {$where}";
		return $this->_db->exec($sql);
	}
	
	/**
	 * 替换数据，根据条件替换存在的记录，如记录不存在，则将条件与替换数据相加并新增一条记录。
	 * 
	 * @param conditions    数组形式，查找条件，请注意，仅能使用数组作为该条件！
	 * @param row    数组形式，修改的数据
	 */
	public function replace($conditions, $row)
	{
		if( $this->find($conditions) ){
			return $this->update($conditions, $row);
		}else{
			if( !is_array($conditions) )spError('replace方法的条件务必是数组形式！');
			$rows = spConfigReady($conditions, $row);
			return $this->create($rows);
		}
	}
	
	/**
	 * 为设定的字段值增加
	 * @param conditions    数组形式，查找条件，此参数的格式用法与find/findAll的查找条件参数是相同的。
	 * @param field    字符串，需要增加的字段名称，该字段务必是数值类型
	 * @param optval    增加的值
	 */
	public function incrField($conditions, $field, $optval = 1)
	{
		$where = "";
		if(is_array($conditions)){
			$join = array();
			foreach( $conditions as $key => $condition ){
				$condition = $this->escape($condition);
				$join[] = "{$key} = {$condition}";
			}
			$where = "WHERE ".join(" AND ",$join);
		}else{
			if(null != $conditions)$where = "WHERE ".$conditions;
		}
		$values = "{$field} = {$field} + {$optval}";
		$sql = "UPDATE {$this->tbl_name} SET {$values} {$where}";
		return $this->_db->exec($sql);
	}
	
	/**
	 * 为设定的字段值减少
	 * @param conditions    数组形式，查找条件，此参数的格式用法与find/findAll的查找条件参数是相同的。
	 * @param field    字符串，需要减少的字段名称，该字段务必是数值类型
	 * @param optval    减少的值
	 */
	public function decrField($conditions, $field, $optval = 1)
	{
		return $this->incrField($conditions, $field, - $optval);
	}

	/**
	 * 按给定的数据表的主键删除记录
	 *
	 * @param pk    字符串或数字，数据表主键的值。
	 */
	public function deleteByPk($pk)
	{
		return $this->delete(array($this->pk=>$pk));
	}

	/**
	 * 按表字段调整适合的字段
	 * @param rows    输入的表字段
	 */
	private function __prepera_format($rows)
	{
		$columns = $this->_db->getTable($this->tbl_name);
		$newcol = array();
		foreach( $columns as $col ){
			$newcol[$col['Field']] = $col['Field'];
		}
		return array_intersect_key($rows,$newcol);
	}

}


/**
 * spRun  执行用户代码
 */
function spRun(){
	GLOBAL $__controller, $__action;
	// 对路由进行自动执行相关操作
	spLaunch("router_prefilter");
	
	$GLOBALS['G_SP']['request_variables'] = array_merge($_GET, $_POST);
	
	// 对将要访问的控制器类进行实例化
	$handle_controller = spClass($__controller, null, $GLOBALS['G_SP']["controller_path"].'/'.$__controller.".php");
	// 调用控制器出错将调用路由错误处理函数
	if(!is_object($handle_controller) || !method_exists($handle_controller, $__action)){
		eval($GLOBALS['G_SP']["dispatcher_error"]);
		exit;
	}
	// 路由并执行用户代码
	$handle_controller->$__action();
	// 控制器程序运行完毕，进行模板的自动输出
	if($GLOBALS['G_SP']['view']['auto_display']){
		$__tplname = $__controller.$GLOBALS['G_SP']['view']['auto_display_sep'].
				$__action.$GLOBALS['G_SP']['view']['auto_display_suffix']; // 拼装模板路径
		$handle_controller->display($__tplname, 1, 0);
	}
	// 对路由进行后续相关操作
	spLaunch("router_postfilter");
}

/**
 * dump  格式化输出变量程序
 * 
 * @param vars    变量
 * @param output    是否将内容输出
 * @param show_trace    是否将使用spError对变量进行追踪输出
 */
function dump($vars, $output = true, $show_trace = false){
	// 部署模式下同时不允许查看调试信息的情况，直接退出。
	if(true != SP_DEBUG && true != $GLOBALS['G_SP']['allow_trace_onrelease'])return;
	if( true == $show_trace ){ // 显示变量运行路径
		$content = spError(htmlspecialchars(print_r($vars, true)), true, false);
	}else{
		$content = "<div align=left><pre>\n" . htmlspecialchars(print_r($vars, true)) . "\n</pre></div>\n";
	}
    if(true != $output) { return $content; } // 直接返回，不输出。 
       echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body>{$content}</body></html>"; 
	   return;
}

/**
 * import  载入包含文件
 * 
 * @param filename    需要载入的文件名或者文件路径
 * @param auto_search    载入文件找不到时是否搜索系统路径或文件，搜索路径的顺序为：应用程序包含目录 -> 应用程序Model目录 -> sp框架包含文件目录
 * @param auto_error    自动提示扩展类载入出错信息
 */
function import($sfilename, $auto_search = true, $auto_error = false){
	if(isset($GLOBALS['G_SP']["import_file"][md5($sfilename)]))return true; // 已包含载入，返回
	// 检查$sfilename是否直接可读
	if( true == @is_readable($sfilename) ){
		require($sfilename); // 载入文件
		$GLOBALS['G_SP']['import_file'][md5($sfilename)] = true; // 对该文件进行标识为已载入
		return true;
	}else{
		if(true == $auto_search){ // 需要搜索文件
			// 按“应用程序包含目录 -> 应用程序Model目录 -> sp框架包含文件目录”的顺序搜索文件
			foreach(array_merge( $GLOBALS['G_SP']['include_path'], array($GLOBALS['G_SP']['model_path']), $GLOBALS['G_SP']['sp_include_path'] ) as $sp_include_path){
				// 检查当前搜索路径中，该文件是否已经载入
				if(isset($GLOBALS['G_SP']["import_file"][md5($sp_include_path.'/'.$sfilename)]))return true;
				if( is_readable( $sp_include_path.'/'.$sfilename ) ){
					require($sp_include_path.'/'.$sfilename);// 载入文件
					$GLOBALS['G_SP']['import_file'][md5($sp_include_path.'/'.$sfilename)] = true;// 对该文件进行标识为已载入
					return true;
				}
			}
		}
	}
	if( true == $auto_error )spError("未能找到名为：{$sfilename}的文件");
	return false;
}

/**
 * spClass  类实例化函数  自动载入类定义文件，实例化并返回对象句柄
 * 
 * @param class_name    类名称
 * @param args   类初始化时使用的参数，数组形式
 * @param sdir 载入类定义文件的路径，可以是目录+文件名的方式，也可以单独是目录。sdir的值将传入import()进行载入
 * @param force_inst 是否强制重新实例化对象
 */
function spClass($class_name, $args = null, $sdir = null, $force_inst = false){
	// 检查类名称是否正确，以保证类定义文件载入的安全性
	if(preg_match('/[^a-z0-9\-_.]/i', $class_name))spError($class_name."类名称错误，请检查。");
	// 检查是否该类已经实例化，直接返回已实例对象，避免再次实例化
	if(true != $force_inst)if(isset($GLOBALS['G_SP']["inst_class"][$class_name]))return $GLOBALS['G_SP']["inst_class"][$class_name];
	// 如果$sdir不能读取，则测试是否仅路径
	if(null != $sdir && !import($sdir) && !import($sdir.'/'.$class_name.'.php'))return false;
	
	$has_define = false;
	// 检查类定义是否存在
	if(class_exists($class_name, false) || interface_exists($class_name, false)){
		$has_define = true;
	}else{
		if( true == import($class_name.'.php')){
			$has_define = true;
		}
	}
	if(false != $has_define){
		$argString = '';$comma = ''; 
		if(null != $args)for ($i = 0; $i < count($args); $i ++) { $argString .= $comma . "\$args[$i]"; $comma = ', '; } 
		eval("\$GLOBALS['G_SP']['inst_class'][\$class_name]= new \$class_name($argString);"); 
		return $GLOBALS['G_SP']["inst_class"][$class_name];
	}
	spError($class_name."类定义不存在，请检查。");
}

/**
 * spError 框架定义的系统级错误提示
 * 
 * @param msg    出错信息
 * @param output    是否输出
 * @param stop    是否停止程序
 */
function spError($msg, $output = true, $stop = true){
	if($GLOBALS['G_SP']['sp_error_throw_exception'])throw new Exception($msg);
	if(true != SP_DEBUG){error_log($msg);if(true == $stop)exit;}
	$traces = debug_backtrace();
	$bufferabove = ob_get_clean();
	require_once($GLOBALS['G_SP']['sp_notice_php']);
	if(true == $stop)exit;
}

/**
 * spLaunch  执行扩展程序
 * 
 * @param configname    扩展程序设置点名称
 * @param launchargs    扩展参数
 * @param return    是否存在返回数据，如需要返回，则该扩展点仅能有一个扩展操作
 */
function spLaunch($configname, $launchargs = null, $returns = false ){
	if( isset($GLOBALS['G_SP']['launch'][$configname]) && is_array($GLOBALS['G_SP']['launch'][$configname]) ){
		foreach( $GLOBALS['G_SP']['launch'][$configname] as $launch ){
			if( is_array($launch) ){
				$reval = spClass($launch[0])->{$launch[1]}($launchargs);
			}else{
				$reval = call_user_func_array($launch, $launchargs);
			}
			if( true == $returns )return $reval;
		}
	}
	return false;
}

/**
 *
 * spUrl
 *
 * URL模式的构建函数
 *
 * @param controller    控制器名称，默认为配置'default_controller'
 * @param action    动作名称，默认为配置'default_action' 
 * @param args    传递的参数，数组形式
 * @param anchor    跳转锚点
 * @param no_sphtml    是否应用spHtml设置，在false时效果与不启用spHtml相同。
 */
function spUrl($controller = null, $action = null, $args = null, $anchor = null) {
	$controller = ( null != $controller ) ? $controller : $GLOBALS['G_SP']["default_controller"];
	$action = ( null != $action ) ? $action : $GLOBALS['G_SP']["default_action"];
	// 使用扩展点
	if( $launch = spLaunch("function_url", array('controller'=>$controller, 'action'=>$action, 'args'=>$args, 'anchor'=>$anchor), true ))return $launch;
	$url = $GLOBALS['G_SP']['url']["url_path_base"]."?". $GLOBALS['G_SP']["url_controller"]. "={$controller}&";
	$url .= $GLOBALS['G_SP']["url_action"]. "={$action}";
	if(null != $args)foreach($args as $key => $arg) $url .= "&{$key}={$arg}";

	if(null != $anchor) $url .= "#".$anchor;
	return $url;
}
function __template_spUrl($params)
{
	$controller = $GLOBALS['G_SP']["default_controller"];
	$action = $GLOBALS['G_SP']["default_action"];
	$args = array();
	$anchor = null;
	foreach($params as $key => $param){
		if( $key == $GLOBALS['G_SP']["url_controller"] ){
			$controller = $param;
		}elseif( $key == $GLOBALS['G_SP']["url_action"] ){
			$action = $param;
		}elseif( $key == 'anchor' ){
			$anchor = $param;
		}else{
			$args[$key] = $param;
		}
	}
	return spUrl($controller, $action, $args, $anchor);
}

/**
 * __mkdirs
 *
 * 循环建立目录的辅助函数
 *
 * @param dir    目录路径
 * @param mode    文件权限
 */
function __mkdirs($dir, $mode = 0777)
{
	if (!is_dir($dir)) {
		__mkdirs(dirname($dir), $mode);
		return @mkdir($dir, $mode);
	}
	return true;
}

/**
 * spExt
 *
 * 扩展类获取扩展配置的函数
 *
 * @param ext_node_name    扩展配置名
 */
function spExt($ext_node_name)
{
	return (empty($GLOBALS['G_SP']['ext'][$ext_node_name])) ? false : $GLOBALS['G_SP']['ext'][$ext_node_name];
}

/**
 * spAddViewFunction
 *
 * 将函数注册到模板内使用，该函数可以是对象的方法，类的方法或是函数。
 *
 * @param alias    函数在模板内的别名
 * @param callback_function    回调的函数或方法
 */
function spAddViewFunction($alias, $callback_function)
{
	return $GLOBALS['G_SP']["view_registered_functions"][$alias] = $callback_function;
}

/**
 * json_decode/json_encode
 *
 * 兼容在未配置JSON扩展的情况下使用Services_JSON类
 *
 */
if ( !function_exists('json_decode') ){
	function json_decode($content, $assoc=false){
		if ( $assoc ){
			return spClass("Services_JSON", array(16))->decode($content);
		} else {
			return spClass("Services_JSON")->decode($content);
		}
	}
}
if ( !function_exists('json_encode') ){
    function json_encode($content){return spClass("Services_JSON")->encode($content);}
}

/**
 * spConfigReady   快速将用户配置覆盖到框架默认配置
 * 
 * @param preconfig    默认配置
 * @param useconfig    用户配置
 */
function spConfigReady( $preconfig, $useconfig = null){
	$nowconfig = $preconfig;
	if (is_array($useconfig)){
		foreach ($useconfig as $key => $val){
			if (is_array($useconfig[$key])){
				@$nowconfig[$key] = is_array($nowconfig[$key]) ? spConfigReady($nowconfig[$key], $useconfig[$key]) : $useconfig[$key];
			}else{
				@$nowconfig[$key] = $val;
			}
		}
	}
	return $nowconfig;
}
