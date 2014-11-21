<?php
/**
 * Qianliyan Log
 *
 * @category   Qianliyan
 * @package    Qianliyan_Log
 * @version    $Id$
 */
 
require_once 'Hush/Log.php';

/**
 * @package Qianliyan_Log
 */
class Qianliyan_Log
{
	/**
	 * @var string
	 */
	private $_logger = null;
	
	/**
	 * Construct 
	 * Init logger instance
	 * @param string $logger Logger type
	 * @return void
	 */
	public function __construct ($logger = 'sys')
	{
		$this->_logger = Hush_Log::getInstance($logger);
	}
	
	/**
	 * Overload logger log interface
	 * @param string $msg Logging message
	 * @return void
	 */
	public function log ($name, $msg)
	{
		$this->_logger->log($name, $msg);
	}
}
