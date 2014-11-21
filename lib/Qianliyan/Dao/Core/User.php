<?php
/**
 * Qianliyan Dao
 *
 * @category   Demos
 * @package    Demos_Dao_Core
 * @version    $Id$
 */

require_once 'Qianliyan/Dao/Core.php';

/**
 * @package Qianliyan_Dao_Core
 */
class Core_User extends Qianliyan_Dao_Core
{
	/**
	 * @static
	 */
	const TABLE_NAME = 'user';
	
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
	 * User login
	 * @param string $user
	 * @param string $pass
	 */
	public function doAuth ($user, $pass)
	{
		$sql = $this->select()
			->from($this->t1, '*')
			->where("{$this->t1}.NAME = ?", $user)
			->where("{$this->t1}.PASS = ?", $pass);
		
		$user = $this->dbr()->fetchRow($sql);
		if ($user) return $user;
		return false;
	}
	
	/**
	 * Get user by id
	 * @param int $id
	 */
	public function getById ($id) {
		$user = $this->read($id);
		return $user;
	}
	
	/**
	 * Get user by name
	 * @param var $name
	 */
	public function getByName ($name) {
		$sql = $this->select()
			->from($this->t1, '*')
			->where("{$this->t1}.NAME = ?", $name);
		
		$user = $this->dbr()->fetchRow($sql);
		return $user;
	}
	
	/**
	 * Get user by smart center
	 * @param var $smartcenterSN
	 */
	public function getListBySmartCenter($smartcenterSN)
	{
		$sql = $this->select()
			->from($this->t1, '*')
			->where("{$this->t1}.SMARTCENTER_SN = ?", $smartcenterSN);
		return $this->dbr()->fetchAll($sql);
	}
	
	/**
	 * Get user list 
	 * @param $userId User ID
	 */
	public function getListByPage ()
	{
		$sql = $this->select()
			->from($this->t1, '*')
			->order("{$this->t1}.UPTIME desc");
		
		return $this->dbr()->fetchAll($sql);
	}
}