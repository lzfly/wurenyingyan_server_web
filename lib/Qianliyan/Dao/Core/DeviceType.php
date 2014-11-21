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
class Core_DeviceType extends Qianliyan_Dao_Core
{
	/**
	 * @static
	 */
	const TABLE_NAME = 'device_type';
	
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
	
	/**
	 * Get device by serial_number
	 * @param var $code
	 */
	public function getByCode ($code) {
		$sql = $this->select()
			->from($this->t1, '*')
			->where("{$this->t1}.CODE = ?", $code);
		
		$deviceType = $this->dbr()->fetchRow($sql);
		return $deviceType;
	}
}