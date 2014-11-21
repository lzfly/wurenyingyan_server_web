<?php
/**
 * Demos Cli
 *
 * @category   Qianliyan
 * @package    Qianliyan_Cli
 * @version    $Id$
 */

/**
 * @package Qianliyan_Cli
 */
class Qianliyan_Cli_Help extends Qianliyan_Cli
{
	/**
	 * Document cli class instruction
	 * @return void
	 */
	public function helpAction ()
	{
		// Get cli class method list
		$cliMethodList = $this->_getCliMethodList();
		
		// print command list
		echo "\n-------------------------------------------------------\n";
		echo __APP_NAME . " Cli Command List :\n";
		echo "-------------------------------------------------------\n\n";
		foreach ($cliMethodList as $cliName => $cmdList) {
			foreach ($cmdList as $cmdLine => $cmdValue) {
				echo "./cli $cliName $cmdLine\n";
			}
		}
		echo "\n";
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////
	// protected methods
	
	protected function _getCliMethodList ()
	{
		$cliMethodList = array();
		foreach (glob(__LIB_PATH_CLI . '/*.php') as $classFile) {
			$cliName = strtolower(basename($classFile, '.php'));
			$className = 'Demos_Cli_' . basename($classFile, '.php');
			if ($classFile && $className) {
				require_once $classFile;
				$rClass = new ReflectionClass($className);
				$methodList = $rClass->getMethods(ReflectionMethod::IS_PUBLIC);
				foreach ($methodList as $method) {
					if (preg_match('/Action$/', $method->name)) {
						$cmdName = str_replace('Action', '', $method->name);
						$cliMethodList[$cliName][$cmdName] = 1;
					}
				}
			}
		}
//		Hush_Util::dump($cliMethodList);exit;
		return $cliMethodList;
	}
}
