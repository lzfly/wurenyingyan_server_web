<?php
/**
 * Qianliyan Cli
 *
 * @category   Qianliyan
 * @package    Qianliyan_Cli
 * @version    $Id$
 */
 
require_once 'Hush/Cli.php';

/**
 * @package Qianliyan_Cli
 */
class Qianliyan_Cli extends Hush_Cli
{	
	/**
	 * Implement Hush_Cli run method
	 * Used by bin/cli.php
	 * @return void
	 */
	public function run ()
	{
		$classFile = 'Qianliyan/Cli/' . ucfirst(strtolower($this->_className)) . '.php';
		$className = 'Qianliyan_Cli_' . ucfirst(strtolower($this->_className));
		require_once $classFile;
		$cli = new $className();
		$cli->start();
	}

	/**
	 * Print header
	 */
	protected function _printHeader ()
	{
		// print command list
		echo "\n----------------------------------------------------------\n";
		echo "Cli Class : " . get_class($this) . "\n";
		echo "----------------------------------------------------------\n\n";
	}
	
	/**
	 * Get config file
	 * @param string $dbName
	 * @param string $dbType
	 * @return string
	 */
	protected function _getCmdParams ($dbName = '', $dbType = 'mysql')
	{
		switch ($dbType) {
			case 'mysql':
				return ' -h' . __MYSQL_HOST
					 . ' -P' . __MYSQL_PORT
					 . ' -u' . __MYSQL_USER
					 . ' -p' . __MYSQL_NAME
					 . ' ' . $dbName;
			default:
				return '';
		}
	}
	
	/**
	 * Get backup sql file
	 * @param string $dbName
	 * @param string $dbType
	 * @return string
	 */
	protected function _getSqlBackup ($dbName, $dbType = 'mysql')
	{
		switch ($dbType) {
			case 'mysql':
				return __DAT_DIR . '/dbsql/mysql.' . $dbName . '.' . date('Y-m-d') . '.sql';
			default:
				return '';
		}
	}
}
