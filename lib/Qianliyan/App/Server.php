<?php
/**
 * Qianliyan App
 *
 * @category   Qianliyan
 * @package    Qianliyan_App
 * @version    $Id$
 */

require_once 'Hush/Service.php';

/**
 * @package Qianliyan_App
 */
class Qianliyan_App_Server extends Hush_Service
{
	/**
	 * @var array
	 */
	protected $_msgs = array();
	
	/**
	 * Initialize mongo dao
	 * 
	 * @return array
	 */
	public function __init ()
	{
		parent::__init();
		
		// init dao
		require_once 'Qianliyan/Dao.php';
		$this->dao = new Qianliyan_Dao();
		
		// init url
		require_once 'Qianliyan/Util/Url.php';
		$this->url = new Qianliyan_Util_Url();
		
		// init session
		require_once 'Qianliyan/Util/Session.php';
		$this->session = new Qianliyan_Util_Session();
	}
	
	/**
	 * Logging mongo dao
	 * 
	 * @return array
	 */
	public function __done ()
	{
		parent::__done();
	}
	
	/**
	 * Forward page by header redirection
	 * J2EE like method's name :)
	 * 
	 * @param string $url
	 * @return void
	 */
	public function forward ($url)
	{
		// append sid for url
		Hush_Util::headerRedirect($this->url->format($url));
		exit;
	}
	
	/**
	 * 
	 */
	public function render ($code, $message, $result = '')
	{
		// filter by datamap
		if (is_array($result)) {
			foreach ((array) $result as $name => $data) {
				// Object list
				if (strpos($name, '.list')) {
					$model = trim(str_replace('.list', '', $name));
					foreach ((array) $data as $k => $v) {
						$result[$name][$k] = M($model, $v);
					}
				// Object
				} else {
					$model = trim($name);
					$result[$name] = M($model, $data);
				}
			}
		}
		// print json code
		echo json_encode(array(
			'code'		=> $code,
			'message'	=> $message,
			'result'	=> $result
		));
		exit;
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////
	// protected method
	
	/**
	 * @ingore
	 */
	public function doAuth ()
	{
		if (!isset($_SESSION['user']) && !isset($_SESSION['smartcenter'])) {
			$this->render('10001', 'Please login firstly.');
		} elseif (!isset($_SESSION['smartcenter'])) {
			$this->user = $_SESSION['user'];
		} else {
			$this->smartcenter = $_SESSION['smartcenter'];
		}
	}
	
	/**
	 * @ingore
	 */
	public function guid(){
	    if (function_exists('com_create_guid')){
	        $result = com_create_guid();
	        $result = str_replace("{", "", $result);
	        $result = str_replace("}", "", $result);
	        $result = str_replace("-", "", $result);
	        return $result;
	    }else{
	        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
	        $charid = strtoupper(md5(uniqid(rand(), true)));
	        $hyphen = chr(45);// "-"
	        $uuid = chr(123)// "{"
	                .substr($charid, 0, 8).$hyphen
	                .substr($charid, 8, 4).$hyphen
	                .substr($charid,12, 4).$hyphen
	                .substr($charid,16, 4).$hyphen
	                .substr($charid,20,12)
	                .chr(125);// "}"
	        $result = $uuid;

	        $result = str_replace("{", "", $result);
	        $result = str_replace("}", "", $result);
	        $result = str_replace("-", "", $result);
	        return $result;
	    }
	}
	
	/**
	 * @ingore
	 */
	public function doAuthAdmin ()
	{
		if (!isset($_SESSION['admin'])) {
			$this->forward($this->apiAuth); // auth action
		} else {
			$this->admin = $_SESSION['admin'];
		}
	}
	
	/**
	 * @ingore
	 */
	public function getSmartCenterUserList()
	{
		$this->doAuth();
		
		if ($this->user)
		{
			$smartcenterSN = $this->user['SMARTCENTER_SN'];
		}
		else if ($this->smartcenter)
		{
			$smartcenterSN = $this->smartcenter['SN'];
		}
		
		$result = array();
		
		$userDao = $this->dao->load('Core_User');
		$userList = $userDao->getListBySmartCenter($smartcenterSN);
		foreach ($userList as $user)
		{
			array_push($result, $user['PUSH_CLIENTID']);
		}
		
		return $result;
	}
	
}
