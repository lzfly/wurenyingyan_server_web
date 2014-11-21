<?php
/**
 * Qianliyan App
 *
 * @category   Qianliyan
 * @package    Qianliyan_App_Server
 * @version    $Id$
 */

require_once 'Qianliyan/App/Server.php';
require_once 'Qianliyan/Util/MemCached.php';
require_once 'Qianliyan/Util/SMSSendhx.php';
require_once 'Qianliyan/Util/SMSSendjxt.php';
require_once 'Qianliyan/Util/RandChar.php';

/**
 * @package Qianliyan_App_Server
 */
class UserServer extends Qianliyan_App_Server
{
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 全局设置：
	 * <code>
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 */
	public function __init ()
	{
		parent::__init();
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////
	// service api methods
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：用户列表接口
	 * <code>
	 * URL地址：/user/userList
	 * 提交方式：GET
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 用户列表接口
	 * @action /user/userList
	 * @method get
	 */
	public function userListAction ()
	{
		$this->doAuth();
		
		$userDao = $this->dao->load('Core_User');
		$userList = $userDao->getListByPage();
		$this->render('10000', 'Get user list ok', array(
			'User.list' => $userList
		));
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：查看用户信息接口
	 * <code>
	 * URL地址：/user/userView
	 * 提交方式：POST
	 * 参数#1：device_code，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 查看用户信息接口
	 * @action /user/userView
	 * @params name '' STRING
	 * @method post
	 */
	public function userViewAction ()
	{
		$this->doAuth();
		
		$name = $this->param('name');
		
		// get extra user info
		$userDao = $this->dao->load('Core_User');
		$userItem = $userDao->getByName($name);
		if ($userItem) {
			$this->render('10000', 'View user ok', array(
				'User' => $userItem
			));
		}
		$this->render('14002', 'View user failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：更新用户信息接口
	 * <code>
	 * URL地址：/user/userEdit
	 * 提交方式：POST
	 * 参数#1：name，类型：STRING，必须：YES
	 * 参数#1：key，类型：STRING，必须：YES
	 * 参数#2：val，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 更新用户信息接口
	 * @action /user/userEdit
	 * @params name '' STRING
	 * @params key '' STRING
	 * @params val '' STRING
	 * @method post
	 */
	public function userEditAction ()
	{
		$this->doAuth();
		
		$name = $this->param('name');
		$key = $this->param('key');
		$val = $this->param('val');
		
		$userDao = $this->dao->load('Core_User');
		$userItem = $userDao->getByName($name);
		if ($userItem) {
			if ($key) {
				try {
					$userDao->update(array(
						'ID'	=> $userItem['ID'],
						$key	=> $val,
					));
				} catch (Exception $e) {
					$this->render('14003', $e->getMessage());
				}
				$this->render('10000', 'Update user ok');
			}
		}
		$this->render('14004', 'Update user failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：新建用户接口
	 * <code>
	 * URL地址：/user/userCreate
	 * 提交方式：POST
	 * 参数#1：name，类型：STRING，必须：YES
	 * 参数#2：pass，类型：STRING，必须：YES
	 * 参数#3：phone，类型：STRING，必须：YES

	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 新建用户接口
	 * @action /user/userCreate
	 * @params name '' STRING
	 * @params pass '' STRING
	 * @params phone '' STRING
	 * @method post
	 */
	public function userCreateAction ()
	{
		$this->doAuthAdmin();
		
		$name = $this->param('name');
		$pass = $this->param('pass');
		$phone = $this->param('phone');
		
		if ($_SESSION['user'])
		{
			$smartcenterSN = $_SESSION['user']['SMARTCENTER_SN'];
		}
		elseif ($_SESSION['smartcenter']);
		{
			$smartcenterSN = $_SESSION['smartcenter']['SN'];
		}
		
		if ($smartcenterSN)
		{
			$userDao = $this->dao->load('Core_User');
			$user = $userDao->getByName($name);
			if($user){
				$this->render('14001', 'name has exsited');
			}
			
			if ($name && $pass) {
				$userDao->create(array(
					'SMARTCENTER_SN' => $smartcenterSN,
					'NAME'	=> $name,
					'PASS'	=> $pass,
					'PHONE'	=> $phone
				));
				$this->render('10000', 'Create user ok');
			}
		}
		$this->render('14005', 'Create user failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：找回密码
	 * <code>
	 * URL地址：/user/userGetBackPassword
	 * 提交方式：POST
	 * 参数#1：name，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 找回密码
	 * @action /user/userGetBackPassword
	 * @params name '' STRING
	 * @method post
	 */
	public function userGetBackPasswordAction ()
	{
		$name = $this->param('name');
		// get extra user info
		$userDao = $this->dao->load('Core_User');
		$userItem = $userDao->getByName($name);
		$randchar = new Qianliyan_Util_RandChar; 
		$newpass = $randchar->getRandChar(8);
		
		$userDao->update(array(
					'id'	=> $userItem['id'],
					'pass'	=> $newpass,
				));
		
		if ($userItem) {
			
			$sms = new Qianliyan_Util_SMS_hx; 
			$sms->SendSMSPost($userItem['phone'], $newpass);
			
			$this->render('10000', 'Get password ok');
		}
		$this->render('14002', 'Get password failed');
	}
	
}