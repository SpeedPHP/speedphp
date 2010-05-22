<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * spUrlRewrite �࣬����չ��ʽ֧��SpeedPHP���URL_REWRITE����չ��
 *
 * ����չ��ʹ�ã�����Ҫȷ������������URL_REWRITE���ܣ�������.htaccess���Ѿ������µ�����
 *
 * .htaccess����Ե�ǰӦ�ó����
 *
 * <IfModule mod_rewrite.c>
 * RewriteEngine On
 * RewriteCond %{REQUEST_FILENAME} !-f
 * RewriteCond %{REQUEST_FILENAME} !-d
 * RewriteRule ^(.*)$ index.php?$1 [L]
 * </IfModule>
 *
 * ����չҪ��SpeedPHP���2.5�汾���ϣ���֧�ֶ�spUrl�����Ĺҿ�����
 *
 * Ӧ�ó�����������Ҫʹ�õ�·�ɹҿ����Լ�spUrl�ҿ���
 * 'launch' => array( 
 *	 	'router_prefilter' => array( 
 *			array('spUrlRewrite', 'setReWrite'), 
 *		),
 *  	'function_url' => array(
 *			array("spUrlRewrite", "getReWrite"),
 * 	    ),
 *),
 *
 * ��spUrlRewrite������
 *
 * 'ext' => array(
 * 		'spUrlRewrite' => array(
 *			'hide_default' => true, // ����Ĭ�ϵ�main/index���ƣ�����ǰ������Ҫ���ص�Ĭ�϶�������GET������
 * 			'args_path_info' => false, // ��ַ�����Ƿ�ʹ��path_info�ķ�ʽ��Ĭ�Ϸ�
 *			'suffix' => '.html', // ���ɵ�ַ�Ľ�β��
 *		),
 * ),
 *
 */
if( SP_VERSION < 2.5 )spError('spUrlRewrite��չҪ��SpeedPHP��ܰ汾2.5���ϡ�');
class spUrlRewrite
{
	var $params = array(
		'hide_default' => true,
		'args_path_info' => false,
		'suffix' => '.html',
	);
	/**
	 * ���캯������������
	 */
	public function __construct()
	{
		$params = spExt('spUrlRewrite');
		if(is_array($params))$this->params = array_merge($this->params, $params);
	}	
	/**
	 * �ڿ�����/����ִ��ǰ����·�ɽ��и�װ��ʹ����Խ���URL_WRITE�ĵ�ַ
	 */
	public function setReWrite()
	{
		GLOBAL $__controller, $__action;

		$uri = substr($_SERVER["REQUEST_URI"], strlen(dirname($GLOBALS['G_SP']['url']['url_path_base'])));
		if( empty($uri) || '/' == $uri ){
			$__controller = $GLOBALS['G_SP']['default_controller'];
			$__action = $GLOBALS['G_SP']['default_action'];
			return ;
		}
		$lasturi = stristr($uri,$this->params['suffix']);if( false == $lasturi )return ;
		$firsturi = explode('/',trim(substr($uri, 0, -strlen($lasturi)),"\/\\"));
		if( true == $this->params['hide_default'] && !isset($firsturi[1]) ){ // ��������Ĭ������
			$__controller = $GLOBALS['G_SP']['default_controller'];
			$__action = $firsturi[0];
		}else{
			// ������
			$__controller = (empty($firsturi[0])) ? $GLOBALS['G_SP']['default_controller'] : $firsturi[0];
			$__action = (empty($firsturi[1])) ? $GLOBALS['G_SP']['default_action'] : $firsturi[1];
		}
		$lasturi = substr($lasturi, strlen($this->params['suffix']));
		if( "" != $lasturi ){
			if(true == $this->params['args_path_info']){
				$lasturi = explode('/',$lasturi);
				for($u = 1; $u < count($lasturi); $u++){
					spClass("spArgs")->set($lasturi[$u], isset($lasturi[$u+1]) ? $lasturi[$u+1] : false);$u+=1;
				}
			}else{
				$lasturi = explode('&',ltrim($lasturi,'?'));
				foreach( $lasturi as $val ){
					$valarr = explode('=',$val);spClass("spArgs")->set(isset($valarr[0])?$valarr[0]:"",isset($valarr[1])?$valarr[1]:"");
				}
			}
		}
	}


	/**
	 * �ڹ���spUrl��ַʱ���Ե�ַ����URL_WRITE�ĸ�д
	 *
	 * @param urlargs    spUrl�Ĳ���
	 */
	public function getReWrite($urlargs = array())
	{
		$url = trim(dirname($GLOBALS['G_SP']['url']["url_path_base"]),"\/\\");
		if( empty($url) ){$url = '/';}else{$url = '/'.$url.'/';}
		if( $GLOBALS['G_SP']["default_controller"] == $urlargs['controller'] && $GLOBALS['G_SP']["default_action"] == $urlargs['action'] ){
		}elseif( true == $this->params['hide_default'] && $GLOBALS['G_SP']["default_controller"] == $urlargs['controller'] ){ // ��������Ĭ������
			$url .= (null != $urlargs['action'] ? $urlargs['action'] : $GLOBALS['G_SP']["default_action"]).$this->params['suffix'];
		}else{
			// ������
			$controller = (null != $urlargs['controller']) ? $urlargs['controller'] : $GLOBALS['G_SP']["default_controller"];
			$action = (null != $urlargs['action']) ? $urlargs['action']: $GLOBALS['G_SP']["default_action"];
			$url .= "{$controller}/{$action}".$this->params['suffix'];
		}
		if(null != $urlargs['args']){
			if(true == $this->params['args_path_info']){
				foreach($urlargs['args'] as $key => $arg)$url .= "/{$key}/{$arg}";
			}else{
				$url .= '?';
				foreach($urlargs['args'] as $key => $arg)$url .= "{$key}={$arg}&";
				$url = rtrim($url,'&');
			}
		}
		return $url .((null != $urlargs['anchor']) ? "#{$anchor}" : '');
	}
}