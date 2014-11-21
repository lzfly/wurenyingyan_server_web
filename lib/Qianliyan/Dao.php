<?php
/**
 * Qianliyan Dao
 *
 * @category   Qianliyan
 * @package    Qianliyan_Dao
 * @version    $Id$
 */
 
require_once 'Hush/Db/Dao.php';

/**
 * @abstract
 * @package Qianliyan_Dao
 */
class Qianliyan_Dao extends Hush_Db_Dao
{
	/**
	 * Autoload Ihush Daos
	 * 
	 * @param string $dao
	 * @return Demos_Dao
	 */
	public static function load ($class_name)
	{
	    static $_model = array();
	    if(!isset($_model[$class_name])) {
	    	require_once 'Qianliyan/Dao/' . str_replace('_', '/', $class_name) . '.php';
	    	$_model[$class_name] = new $class_name();
	    }
	    return $_model[$class_name];
	}
}