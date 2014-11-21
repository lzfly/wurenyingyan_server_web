<?php
/**
 * Qianliyan Dao
 *
 * @category   Qianliyan
 * @package    Qianliyan_Dao
 * @version    $Id$
 */
 
require_once 'Qianliyan/Dao.php';

/**
 * @package Qianliyan_Dao
 */
class Qianliyan_Dao_Core extends Qianliyan_Dao
{
	/**
	 * @static
	 */
	const DB_NAME = 'yingyan_core';
	
	/**
	 * Construct
	 */
	public function __construct ()
	{
		// initialize dao
		parent::__construct(MysqlConfig::getInstance());
		
		// set default dao settings
		$this->_bindDb(self::DB_NAME);
	}
}