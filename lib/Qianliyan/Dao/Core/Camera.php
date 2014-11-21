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
class Core_Camera extends Qianliyan_Dao_Core
{
	/**
	 * @static
	 */
	const TABLE_NAME = 'camera';
	
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
	 * @param var $camera_sn
	 */
	public function getBySN ($camera_sn) {
		$sql = $this->select()
			->from($this->t1, '*')
			->where("{$this->t1}.SN = ?", $camera_sn);
		
		$camera = $this->dbr()->fetchRow($sql);
		return $camera;
	}
	
	/**
	 * Get camera  by smart cneter
	 * @param var $smartcenter_sn
	 */
	public function getListBySmartCenter ($smartcenter_sn) {
		if ($smartcenter_sn)
		{
			$sql = $this->select()
				->from($this->t1, '*')
				->where("{$this->t1}.SMARTCENTER_SN = ?", $smartcenter_sn);
		}
		
		$cameraList = $this->dbr()->fetchAll($sql);
		return $cameraList;
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