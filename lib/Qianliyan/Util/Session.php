<?php
/**
 * Qianliyan Util
 *
 * @category   Qianliyan
 * @package    Qianliyan_Util
 * @version    $Id$
 */

/**
 * @package Qianliyan_Util
 */
class Qianliyan_Util_Session
{
	public function __construct() 
	{
		$sid = $_SERVER['HTTP_SID'] ? $_SERVER['HTTP_SID'] : @$_REQUEST['sid'];
		if ($sid) session_id($sid);
		self::autoStart();
	}
	
	static public function autoStart()
	{
		session_start();
		if (!session_id()) {
			Hush_Util::headerRedirect(Demos_Util_Url::format($_SERVER['REQUEST_URI']));
		}
	}
}