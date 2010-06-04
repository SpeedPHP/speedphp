<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * db_oracle Oracle���ݿ������֧��
 */
class db_oracle {
	/**
	 * ���ݿ����Ӿ��
	 */
	public $conn;
	/**
	 * ִ�е�SQL����¼
	 */
	public $arrSql;
	/**
	 * execִ��Ӱ������
	 */
	private $num_rows;

	/**
	 * ��SQL����ȡ��¼�������������
	 * 
	 * @param sql  ִ�е�SQL���
	 */
	public function getArray($sql)
	{
		$this->arrSql[] = $sql;
		$result = $this->exec($sql);
		oci_fetch_all($result, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW);
		oci_free_statement($result);
		return $res;
	}
	
	/**
	 * ���ص�ǰ�����¼������ID
	 */
	public function newinsertid()
	{
		return FALSE; // ʹ��spModel��create�����в���������ID
	}
	
	/**
	 * ��ʽ����limit��SQL���
	 */
	public function setlimit($sql, $limit)
	{
		$limitarr = explode(',',str_replace(' ','',$limit));
		$total = (isset($limitarr[1])) ? ($limitarr[1] + $limitarr[0]) : $limitarr[0];
		$start = (isset($limitarr[1])) ? $limitarr[1] : 0;
		return "SELECT * FROM ( SELECT *, ROWNUM sptmp_limit_rownum FROM ({$sql}) sptmp_limit_tblname WHERE ROWNUM <= {$total} )WHERE sptmp_limit_rownum >= {$start}";
	}

	/**
	 * ִ��һ��SQL���
	 * 
	 * @param sql ��Ҫִ�е�SQL���
	 */
	public function exec($sql)
	{
		$this->arrSql[] = $sql;
		$result = oci_parse($this->conn, $sql);
		if( !$result or !oci_execute($result) ){
			$e = oci_error();spError('{$sql}<br />ִ�д���: ' . strip_tags($e['message']));
		}
		$this->num_rows = oci_num_rows($result);
		return $result;
	}
	
	
	/**
	 * ����Ӱ������
	 */
	public function affected_rows()
	{
		return $this->num_rows;
	}

	/**
	 * ��ȡ���ݱ��ṹ
	 *
	 * @param tbl_name  ������
	 */
	public function getTable($tbl_name)
	{
		return $this->getArray("SELECT column_name AS Field FROM USER_TAB_COLUMNS WHERE table_name = '{$tbl_name}'");
	}

	/**
	 * ���캯��
	 *
	 * @param dbConfig  ���ݿ�����
	 */
	public function __construct($dbConfig)
	{
		if(!function_exists('oci_connect'))spError('PHP����δ��װORACLE�����⣡');
		$linkfunction = ( TRUE == $dbConfig['persistent'] ) ? 'oci_pconnect' : 'oci_connect';
		if( ! $this->conn = $linkfunction($dbConfig['login'], $dbConfig['password'], $dbConfig['host'], 'ZHS16GBK') ){
			$e = oci_error();spError('���ݿ����Ӵ��� : ' . strip_tags($e['message']));
		}
	}
	/**
	 * �������ַ����й���
	 *
	 * @param value  ֵ
	 */
	public function __val_escape($value) {
		if(is_null($value))return null;
		if(is_bool($value))return $value ? 1 : 0;
		if(is_int($value))return (int)$value;
		if(is_float($value))return (float)$value;
		if(@get_magic_quotes_gpc())$value = stripslashes($value);
		$value = addslashes($value); // ?
		$value = str_replace("_","\_",$value);
		$value = str_replace("%","\%",$value);
		return $value;
	}

	/**
	 * ��������
	 */
	public function __destruct()
	{
		if( TRUE != $dbConfig['persistent'] )@oci_close($this->conn);
	}
}