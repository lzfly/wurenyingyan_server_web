<?php
/**
 * Qianliyan Dao
 *
 * @category   Qianliyan
 * @package    Qianliyan_Dao_Core
 * @version    $Id$
 */

require_once 'Qianliyan/Dao/Core.php';

/**
 * @package Qianliyan_Dao_Core
 */
class Core_Notice extends Qianliyan_Dao_Core
{
	/**
	 * @static
	 */
	const TABLE_NAME = 'notice';
	
	/**
	 * @static
	 */
	const TABLE_PRIM = 'ID';
	
	/**
	 * Initialize
	 */
	public function __init () 
	{
		$this->t1 = self::TABLE_NAME;
		$this->k1 = self::TABLE_PRIM;
		$this->_bindTable($this->t1, $this->k1);
	}

	/**
	 * Get Notice Unread count
	 * @param var $smartcenterSN
	 * @param var $userId
	 */
	public function getUnreadCount($smartcenterSN, $userId)
	{
		$sql1 = "SELECT COUNT(ID) AS UNREADCOUNT FROM $this->t1 WHERE NOT EXISTS (SELECT NOTICE_CODE FROM notice_status WHERE USER_ID=";
		$sql2 = " AND NOTICE_CODE=$this->t1.CODE) AND SMARTCENTER_SN=";
		$cmdText = $sql1 . $userId . $sql2 . "'" . $smartcenterSN . "'";
		$result = $this->dbr()->fetchAll($cmdText);
		return $result;
	}
	
	public function getPageList($smartcenterSN, $userId, $startId, $count, $type, $status)
	{
		$sql = "SELECT a.*, b.USER_ID FROM (SELECT * FROM $this->t1 WHERE SMARTCENTER_SN='" . $smartcenterSN . "'";
		if ($startId > 0)
		{
			$sql .= (" AND ID<" . $startId);
		}
		if ($type <> null)
		{
			$sql .= (" AND TYPE='" .$type . "'");
		}
		$sql .= ") a LEFT JOIN (SELECT * FROM notice_status WHERE USER_ID=";
		$sql .= ($userId . ") b ON a.CODE=b.NOTICE_CODE");
		if ($status == 0)
		{
			$sql .= " WHERE USER_ID IS NULL";
		}
		elseif ($status == 1)
		{
			$sql .= " WHERE USER_ID IS NOT NULL";
		}
		$sql .= (" ORDER BY UPTIME DESC LIMIT 0," . $count);
		
		$result = $this->dbr()->fetchAll($sql);
		return $result;
	}	
	
}