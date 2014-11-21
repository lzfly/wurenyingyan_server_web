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

/**
 * @package Qianliyan_App_Server
 */
class SmartCenterServer extends Qianliyan_App_Server
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
	 * > 接口说明：智能中心初始化接口
	 * <code>
	 * URL地址：/smartCenter/init
	 * 参数#1：sn，类型：STRING，必须：YES，示例：wkfowkjd
	 * 参数#2：push_clientid，类型：STRING，必须：YES
	 * 提交方式：POST
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 智能中心初始化接口
	 * @action /smartCenter/init
	 * @params sn '' STRING
	 * @params push_clientid '' STRING
	 * @method POST
	 */
	public function initAction()
	{
		$sn = $this->param('sn');
		$pushClientId = $this->param('push_clientid');
		if ($sn)
		{
			$smartcenterDao = $this->dao->load('Core_SmartCenter');
			$smartcenterItem = $smartcenterDao->getBySN($sn);
			if ($smartcenterItem)
			{
				try {
					$smartcenterDao->update(array(
						'ID'	=> $smartcenterItem['ID'],
						'PASS'	=> '888888',
						'PUSH_CLIENTID' => $pushClientId
					));
				} catch (Exception $e) {
					$this->render('14003', 'Update smart center failed');
				}
				$this->render('10000', 'Init smart center done.');
			}
			else
			{
				$smartcenterDao->create(array(
					'SN'	=> $sn,
					'PUSH_CLIENTID' => $pushClientId
				));
				$this->render('10000', 'Init smart center done. (first init)');
			}
		}
		// return sid only for client
		$sid = array('sid' => session_id());
		$this->render('14002', 'Login failed', array(
			'session' => $sid
		));
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：智能中心登录接口
	 * <code>
	 * URL地址：/smartCenter/login
	 * 参数#1：sn，类型：STRING，必须：YES，示例：wkfowkjd
	 * 参数#2：pass，类型：STRING，必须：YES，示例：888888
	 * 提交方式：POST
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 智能中心登录接口
	 * @action /smartCenter/login
	 * @params sn '' STRING
	 * @params pass '' STRING
	 * @method POST
	 */
	public function loginAction ()
	{
		$sn = $this->param('sn');
		$pass = $this->param('pass');
		if ($sn && $pass)
		{
			$smartcenterDao = $this->dao->load('Core_SmartCenter');
			$smartcenterItem = $smartcenterDao->getBySN($sn);
			if ($smartcenterItem)
			{
				if ($smartcenterItem['PASS'] == $pass && $smartcenterItem['STATE'] == 1)
				{
					$_SESSION['smartcenter'] = $smartcenterItem;
					$this->render('10000', 'Login ok', array(
						'Session' => session_id()
					));
				}
				else
				{
					$sid = array('sid' => session_id());
					$this->render('14001', 'Login failed', array(
						'session' => $sid
					));
				}
			}
		}
		// return sid only for client
		$sid = array('sid' => session_id());
		$this->render('14002', 'Login failed', array(
			'session' => $sid
		));
	}
		
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：智能中心列表接口
	 * <code>
	 * URL地址：/smartCenter/smartcenterList
	 * 提交方式：GET
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 智能中心列表接口
	 * @action /smartCenter/smartcenterList
	 * @method get
	 */
	public function smartcenterListAction ()
	{
		$this->doAuthAdmin();
		
		$smartcenterDao = $this->dao->load('Core_SmartCenter');
		$smartcenterList = $smartcenterDao->getListAll();
		$this->render('10000', 'Get smart center list ok', array(
			'smartcenter.list' => $smartcenterList
		));
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：查看智能中心信息接口
	 * <code>
	 * URL地址：/smartCenter/smartcenterView
	 * 提交方式：POST
	 * 参数#1：sn，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 查看智能中心信息接口
	 * @action /smartCenter/smartcenterView
	 * @params sn '' STRING
	 * @method post
	 */
	public function smartcenterViewAction ()
	{
		$this->doAuthAdmin();
		$sn = $this->param('sn');
		// get extra smart center info
		$smartcenterDao = $this->dao->load('Core_SmartCenter');
		$smartcenterItem = $smartcenterDao->getBySN($sn);
		if ($smartcenterItem) {
			$this->render('10000', 'View smart center ok', array(
				'smartcenter' => $smartcenterItem
			));
		}
		$this->render('14002', 'View smart center failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：更新智能中心信息接口
	 * <code>
	 * URL地址：/smartCenter/smartcenterEdit
	 * 提交方式：POST
	 * 参数#1：sn，类型：STRING，必须：YES
	 * 参数#2：key，类型：STRING，必须：YES
	 * 参数#3：val，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 更新智能中心信息接口
	 * @action /smartCenter/smartcenterEdit
	 * @params sn '' STRING
	 * @params key '' STRING
	 * @params val '' STRING
	 * @method post
	 */
	public function smartcenterEditAction ()
	{
		$this->doAuthAdmin();
		
		$sn = $this->param('sn');
		$key = $this->param('key');
		$val = $this->param('val');
		if ($key) {
			$smartcenterDao = $this->dao->load('Core_SmartCenter');
			$smartcenterItem = $smartcenterDao->getBySN($sn);
			try {
				$smartcenterDao->update(array(
					'ID'	=> $smartcenterItem['ID'],
					$key	=> $val,
				));
			} catch (Exception $e) {
				$this->render('14003', 'Update smart center failed');
			}
			$this->render('10000', 'Update smart center ok');
		}
		$this->render('14004', 'Update smart center failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：新建智能中心接口
	 * <code>
	 * URL地址：/smartCenter/smartcenterCreate
	 * 提交方式：POST
	 * 参数#1：sn，类型：INT，必须：YES

	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 新建智能中心接口
	 * @action /smartCenter/smartcenterCreate
	 * @params sn '' STRING
	 * @method post
	 */
	public function smartcenterCreateAction ()
	{
		$this->doAuthAdmin();
		
		$sn = $this->param('sn');
		
		$smartcenterDao = $this->dao->load('Core_SmartCenter');
		$smartcenterItem = $smartcenterDao->getBySN($sn);
		if($smartcenterItem){
			$this->render('14005', 'smart center has exsited');
		}

		if ($sn) {
			$smartcenterDao->create(array(
				'SN'	=> $sn,
			));
			$this->render('10000', 'Create smart center ok');
		}
		else
		{
			$this->render('14006', 'Create smart center failed');
		}
	}

}