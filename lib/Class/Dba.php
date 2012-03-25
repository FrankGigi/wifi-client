<?php
/**
 * Provides a database accessor..  
 * @Name DBA
 * @Author DingusXP
 * @version ver1.0/2007-12-18/DingusXP
 */
//include_once("../config/db.cfg.php");
class Dba
{
	/**
	 * provides a static $dba of mysqli for use.
	 */
	private $_dba ;	
	/**
	 * some constant variables..
	 */
	const DBA_NONE 		= NULL;
	const DBA_SUCCESS 	= 1;
	const DBA_FAIL 		= 0;
	const DBA_DS_ARRAY 	= 0;
	const DBA_DS_ASSOC 	= 1;
	const DBA_DS_ROW 	= 2;	
	const DBA_DS_ALL 	= 0;
	const DBA_DS_DATA 	= 1;
	const DBA_DS_INFO 	= 2;
	
	/**
	 * 	construct method : init $dba;	
	 * @Param [optional] array $dbParam: array("host"=>,"user"=>,"passwd"=>,"db"=>);
	 * @Return <none>
	*/
	function __construct($dbParam=NULL)
	{		
		global $CFG_DB;
		if (!isset($dbParam) || !is_array($dbParam))      $dbParam = $CFG_DB;		
		$db_host   = "localhost";
		$db_user   = "root";
		$db_passwd =  "yangfannifeng";
		$db_name   =  "yangfan";
		$connect = mysql_connect($db_host,$db_user,$db_passwd);
		mysql_select_db($db_name, $connect);
	}
	
	/**
 	 * return a mysqli object, $_dba of Dba;
	 * @Param [optional]array $dbParam; see function __construct()
	 * @Return object <initilized>mysqli
	*/	
	static function GetDba($dbParam=NULL)
	{
		$dba = Dba::Instance($dbParam);
		return $dba->_dba;
	}
	
	/**
	 * return an instance of Dba;
	 * @Param:[optional]array $dbParam; see function __construct()
	 * @Return:an instance of Dba;
	 */
	static function Instance($dbParam=NULL)
	{
		return (new Dba($dbParam));
	}
	
	/**
	 * change database
	 * @param string $dbname
	 * @return <none>
	 */
	function selectDb($dbname)
	{
		if (!empty($dbname))      @$this->_dba->select_db($dbname);
	}
	
	/**
	 * return a mysqli_result
	 * @Param string $sql :: query string
	 * @Return mysqli_result $rs
	*/
	function query( $sql )
	{	
		return mssql_query($sql);
	}	
	
	/**
	 * return a mysqli_result
	 * @Param string $sql :: query string
	 * @Return Dba::DBA_FAIL or insert_id
	*/
	function insert( $sql )
	{		
		$rs = mssql_query($sql);
		$id = mssql_query("select scope_IDENTITY() as id");
		$id = mssql_fetch_assoc($id);		
		if (!$id)    return self::DBA_FAIL ;
		else         return $id['id'];
	}	
	
	/**
	 * return a mysqli_result
	 * @Param:string $sql :: query string
	 * @Return:self::DBA_SUCCESS or self::DBA_FAIL
	*/
	function execute( $sql )
	{
		$rs = $this->_dba->query($sql);
		if (false == $rs)      return self::DBA_FAIL ;
		else                   return self::DBA_SUCCESS ;
	}
	
	function delete($sql)
	{
		return mssql_query($sql);
	}
	
	function update($sql)
	{
		return mssql_query($sql);
	}
	
	/**
	 * query and fetch dataset.
	 * @Param string $sql 
	 * @param [optional] $dsType: the type of return ds.
	 * 				DBA_DS_ALL = 0;  will return array("info"=>,"data"=>);
	 * 				DBA_DS_DATA = 1; will return array([data]); =>DEFAULT
	 * 				DBA_DS_INFO = 2; will return array([info],"data"=>);
	 * @param [optional] $arType: the type of ds array
	 * 				DBA_DS_ARRAY = 0;  the array indexed with both number and string;
	 * 				DBA_DS_ASSOC = 1; the array indexed by string[field name]; =>DEFAULT
	 * 				DBA_DS_ROW = 2; the array indexed by num;	
	 * @Return array $ds
	*/
	function getDs($sql,$dsType=1,$arType=1)
	{		
		$rs = mssql_query($sql) ;
		if ( false == $rs )	   return self::DBA_FAIL ;
		$data = array();
		switch ($arType)
		{
			case self::DBA_DS_ASSOC :
				while ($row = mssql_fetch_assoc($rs))  {  $data[] = $row;  }
				break;
			case self::DBA_DS_ARRAY :
				while ($row = mssql_fetch_array($rs))  {  $data[] = $row;  }
				break;
			case self::DBA_DS_ROW :
				while ($row = mssql_fetch_row($rs))    {  $data[] = $row;  }
				break;
			default:
				while ($row = mssql_fetch_assoc($rs))  {  $data[] = $row;  }
				break;
		}		
		$ds = array();
		switch ($dsType)
		{
			case self::DBA_DS_ALL :
				$ds['data'] = $data;
				$ds['info'] = $this->_parseSql($sql);
				$ds['info']['count'] = count($data);
				break;
			case self::DBA_DS_DATA :
				$ds = $data;
				break;
			case self::DBA_DS_INFO :
				$ds = $this->_parseSql($sql);
				$ds['count'] = count($data);
				$ds['data'] = $data;
		}
		unset($data);
		return $ds;		
	}
        function get_page_Ds($sql,$list_row = null,$offset = null)
        {
                $data = array(); $ds = array();
		$ds['info'] = $this->_count($sql,$list_row,$offset);
		
		if($ds['info']['tpage'] == (int)($offset / $list_row))
		{
                        $target_string = "top $list_row *";
			$target_row = $ds['info']['total'] - ((int)($offset / $list_row) - 1)*$list_row;
			$replace_string = "top $target_row *";
                        $sql = str_replace($target_string,$replace_string,$sql);
	                $rs = mssql_query($sql) ;
	                if ( false == $rs )        return self::DBA_FAIL ;
                        while($row = mssql_fetch_assoc($rs))
                        {       $data[] = $row;}
                        $ds['data'] = $data;
		}
		else
		{
	                $rs = mssql_query($sql) ;
        	        if ( false == $rs )        return self::DBA_FAIL ;
                	while($row = mssql_fetch_assoc($rs))
			{	$data[] = $row;}
                	$ds['data'] = $data;
		}
                unset($data);
                return $ds;
        }	
	/**
	* query sql and return first item .
	* @Param string $sql 
	* @param int $arType  see: getDs(),
	* @Return array $item
	*/
	function getItem( $sql,$arType=1 )
	{
		$rs = $this->query($sql) ;
		if ( false == $rs )  return self::DBA_NONE ;
		switch ( $arType )
		{
			case self::DBA_DS_ARRAY :
				return mssql_fetch_array($rs) ;	
				break;
			case self::DBA_DS_ASSOC:
				return mssql_fetch_assoc($rs) ;
				break;
			case self::DBA_DS_ROW:
				return mssql_fetch_row($rs) ;
				break;
			default:
				return mssql_fetch_assoc($rs) ;	
				break;
		}		
	}
	
	/**
	 * Parse sql to get pageitem info..
	 *
	 * @param: string $sql
	 * @return: array $pageinfo
	 */
	function _parseSql($sql)
	{
		$pageinfo = array();
		/** extract page used informaion**/
		$divide1 = explode("top",strtolower($sql));
		$divide2 = explode("*",$divide1[1]);
		$offset = trim($divide2[0]);
                $divide3 = explode("*",$divide1[2]);
                $list_row = trim($divide3[0]);
		/** extract count number used sql**/
		$part1 = explode("(",strtolower($sql));
		if(count($part1) == '3'){
			$part2 = explode(")",$part1[2]);
	 		$target_string = "/top $list_row \*/";
			$count_sql = preg_replace($target_string,"count(*) as [count(*)]",$part2[0]);}
		else{
			$part1[0] = null;
			$part1 = implode("(",$part1);
                        $part2 = explode(")",$part1);
			$num = count($part2);
			$part2[($num - 1)] = null;
			$part2 = implode(")",$part2);
                        $target_string = "top $list_row *";
                        $count_sql = str_replace($target_string,"count(*) as [count(*)]",$part2);}
		$count_sql = str_replace("order by 'id' desc"," ",$count_sql);	
		$pageinfo['sql'] = $sql;
		$rs = $this->getItem($count_sql);
		$pageinfo['total'] = $rs['count(*)'];		
		unset($rs);
		$pageinfo['offset']   = $offset;
		$pageinfo['list_row'] = $list_row;
		$pageinfo['tpage']    = ceil($pageinfo['total'] / $offset);
		$pageinfo['page']     = (int)($list_row / $offset);
		return $pageinfo;
	}
        function _count($sql,$list_row = null,$offset = null)
        {
                $pageinfo = array();
                $part1 = explode("(",strtolower($sql));
                if(count($part1) == '3'){
                        $part2 = explode(")",$part1[2]);
                        $target_string = "/top $offset \*/";
                        $count_sql = preg_replace($target_string,"count(*) as [count(*)]",$part2[0]);
			$count_sql = str_replace("order by 'id' desc"," ",$count_sql);
			$count_sql = str_replace("order by 'test_number' desc"," ",$count_sql);}
                else if(count($part1) == '4'){
                        $part1 = $part1[2]."(".$part1[3];
                        $part2 = explode(")",$part1);
			$part2 = $part2[0].")";
                        $target_string = "top $offset *";
                        $count_sql = str_replace($target_string,"count(*) as [count(*)]",$part2);}
                else if(count($part1) == '5'){
                        $part1 = $part1[2]."(".$part1[3]."(".$part1[4]."(".$part1[5];
                        $part2 = explode(")",$part1);
                        $part2 = $part2[0]."))";
                        $target_string = "top $offset *";
                        $count_sql = str_replace($target_string,"count(*) as [count(*)]",$part2);}
                $pageinfo['sql'] = $sql;
                $rs = $this->getItem($count_sql);
                $pageinfo['total'] = $rs['count(*)'];
                unset($rs);
                $pageinfo['tpage'] = ceil($pageinfo['total'] / $list_row);
                $pageinfo['page'] = (int)($offset / $list_row);
                return $pageinfo;
        }

}
?>
