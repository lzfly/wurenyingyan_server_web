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
class Core_Device extends Qianliyan_Dao_Core
{
	/**
	 * @static
	 */
	const TABLE_NAME = 'device';
	
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
	 * Get customer by id
	 * @param int $id
	 */
	public function getById ($id) {
		$smartcenter = $this->read($id);
		return $smartcenter;
	}
	
	/**
	 * Get device by serial_number
	 * @param var $device_sn
	 */
	public function getBySN ($device_sn) {
		$sql = $this->select()
			->from($this->t1, '*')
			->where("{$this->t1}.SN = ?", $device_sn);
		
		$smartcenter = $this->dbr()->fetchRow($sql);
		return $smartcenter;
	}
	
	/**
	 * Get device  by smart cneter
	 * @param var $smartcenter_sn
	 * @param var $is_open
	 * @param var $is_online
	 */
	public function getListBySmartCenter ($smartcenter_sn, $is_open, $is_online) {
		if ($smartcenter_sn && is_numeric($is_open) && is_numeric($is_online))
		{
			$sql = "SELECT * FROM $this->t1 WHERE SMARTCENTER_SN='$smartcenter_sn' AND IS_OPEN=$is_open AND IS_ONLINE=$is_online";
		}
		else if ($smartcenter_sn && is_numeric($is_open))
		{
			$sql = "SELECT * FROM $this->t1 WHERE SMARTCENTER_SN='$smartcenter_sn' AND IS_OPEN=$is_open";
		}
		else if ($smartcenter_sn && is_numeric($is_online))
		{
			$sql = "SELECT * FROM $this->t1 WHERE SMARTCENTER_SN='$smartcenter_sn' AND IS_ONLINE=$is_online";
		}
		else
		{
			$sql = "SELECT * FROM $this->t1 WHERE SMARTCENTER_SN='$smartcenter_sn'";
		}
		$smartcenterList = $this->dbr()->fetchAll($sql);
		return $smartcenterList;
	}
	
	/**
	 * Get device list 
	 */
	public function getListByPage ()
	{
		$list = array();
		$sql = $this->select()
			->from($this->t1, '*');
		
		return $this->dbr()->fetchAll($sql);
	}
}