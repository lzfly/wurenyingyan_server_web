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
class Core_DeviceBindCamera extends Qianliyan_Dao_Core
{
	/**
	 * @static
	 */
	const TABLE_NAME = 'device_bind_camera';
	
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
	 * Get DeviceType list 
	 */
	public function getListAll ()
	{
		$list = array();
		$sql = $this->select()
			->from($this->t1, '*');
		
		return $this->dbr()->fetchAll($sql);
	}
	
	public function delete($deviceSN, $cameraSN)
	{
		$wheresql = "DEVICE_SN = '$deviceSN' and CAMERA_SN = '$cameraSN'";
		return $this->dbw()->delete($this->t1, $wheresql);	
	}
	
	/**
	 * Get device by serial_number
	 * @param var $deviceSN
	 */
	public function getByDeviceSN ($deviceSN) {
		$sql = $this->select()
			->from($this->t1, '*')
			->where("{$this->t1}.DEVICE_SN = ?", $deviceSN);
		
		$deviceBindCamera = $this->dbr()->fetchRow($sql);
		return $deviceBindCamera;
	}
}