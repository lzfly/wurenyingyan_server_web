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

require_once 'Qianliyan/IGeTui/PushMessage.php';
require_once 'Qianliyan/IGeTui/PushNotification.php';

/**
 * @package Qianliyan_App_Server
 */
class NoticeServer extends Qianliyan_App_Server
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
	 * > 接口说明：获得用户未读通知总数接口
	 * <code>
	 * URL地址：/notice/getUnreadCount
	 * 提交方式：GET
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 获得用户未读通知总数接口
	 * @action /notice/getUnreadCount
	 * @method get
	 */
	public function getUnreadCountAction ()
	{
		$this->doAuth();
		
		if ($this->user)
		{
			$userId = $this->user['ID'];
			$smartcenterSN = $this->user['SMARTCENTER_SN'];
			
			$noticeDao = $this->dao->load('Core_Notice');
			$result = $noticeDao->getUnreadCount($smartcenterSN, $userId);
			$this->render('10000', 'Get notice unread count ok', array(
				'count' => $result[0]['UNREADCOUNT']
			));
		}
		$this->render('14001', 'Please login firstly by client user.');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：获取通知列表接口
	 * <code>
	 * URL地址：/notice/getPageList
	 * 提交方式：POST
	 * 参数#1：start_id，类型：INT，必须：YES
	 * 参数#2：count，类型：INT，必须：YES
	 * 参数#3：type，类型：STRING，必须：YES	0:报警  1:数据更新
	 * 参数#4：status，类型：INT，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 获取通知列表接口
	 * @action /notice/getPageList
	 * @params start_id 0 INT
	 * @params count 50 INT
	 * @params type '' STRING
	 * @params status '' INT
	 * @method post
	 */
	public function getPageListAction ()
	{
		$this->doAuth();
		
		$startId = $this->param('start_id');
		$count = $this->param('count');
		$type = $this->param('type');
		$status = $this->param('status');
		
		if (!isset($startId) || is_null($startId) || empty($startId))
		{
			$startId = 0;
		}
		
		if (!isset($count) || is_null($count) || empty($count))
		{
			$count = 50;
		}

		if (!isset($type) || is_null($type) || empty($type))
		{
			$type = null;
		}
		
		if (!isset($status) || is_null($status) || empty($status))
		{
			$status = -1;
		}
		
		if ($this->user)
		{
			$userId = $this->user['ID'];
			$smartcenterSN = $this->user['SMARTCENTER_SN'];
			
			$noticeDao = $this->dao->load('Core_Notice');
			$result = $noticeDao->getPageList($smartcenterSN, $userId, $startId, $count, $type, $status);
			$this->render('10000', 'Get notice page list ok', array(
				'notice.list' => $result
			));
		}
		$this->render('14001', 'Get notice page list failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：阅读通知接口
	 * <code>
	 * URL地址：/notice/read
	 * 提交方式：POST
	 * 参数#1：notice_code，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 阅读通知接口
	 * @action /notice/read
	 * @params notice_code '' STRING
	 * @method post
	 */
	public function readAction()
	{
		$this->doAuth();
	
		$noticeCode = $this->param('notice_code');
		
		if (!is_null($noticeCode) && isset($noticeCode) && !empty($noticeCode))
		{
			if ($this->user)
			{
				$userId = $this->user['ID'];
				
				$noticeStatusDao = $this->dao->load('Core_NoticeStatus');
				try
				{
					$noticeStatusDao->create(array(
						'USER_ID' => $userId,
						'NOTICE_CODE' => $noticeCode
					));
					$this->render('10000', 'Read notice ok');
				}
				catch (Exception $exp)
				{
					if ($exp->getCode() == 1062)
					{
						$this->render('10000', 'Read notice agin ok');
					}
				}
			}
		}
		$this->render('14001', 'Read notice failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：新建通知接口
	 * <code>
	 * URL地址：/notice/new
	 * 提交方式：POST
	 * 参数#1：device_sn，类型：STRING，必须：YES
	 * 参数#2：message，类型：STRING，必须：YES
	 * 参数#3：type，类型：STRING，必须：YES
	 * 参数#4：package_file，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 新建通知接口
	 * @action /notice/new
	 * @params device_sn '' STRING
	 * @params message '' STRING
	 * @params type '' STRING
	 * @params package_file '' STRING
	 * @method post
	 */
	public function newAction ()
	{
		$this->doAuth();
		
		$deviceSN = $this->param('device_sn');
		$message = $this->param('message');
		$type = $this->param('type');
		$packageFile = $this->param('package_file');
		
		if ($this->user)
		{
			$smartcenterSN = $this->user['SMARTCENTER_SN'];
		}
		else if ($this->smartcenter)
		{
			$smartcenterSN = $this->smartcenter['SN'];
		}
			
		if ($smartcenterSN)
		{
			if ($deviceSN && $message && $type) {
				
				$guid = $this->guid();
				$uptime = date('y-m-d h:i:s', time());
				
				$noticeDao = $this->dao->load('Core_Notice');
				$noticeDao->create(array(
					'CODE'	=> $guid,
					'SMARTCENTER_SN'	=> $smartcenterSN,
					'DEVICE_SN'	=> $deviceSN,
					'MESSAGE'	=> $message,
					'TYPE' => $type,
					'PICTURE_FILE' => $packageFile,
					'UPTIME'	=> $uptime
				));
				
				/* 推送消息 */
				$msg = '{"actionType": "notice_new", "noticeInfo": {';
				$msg .= ('"CODE":"' . $guid . '",');
				$msg .= ('"SMARTCENTER_SN":"' . $smartcenterSN . '",');
				$msg .= ('"DEVICE_SN":"' . $deviceSN . '",');
				$msg .= ('"TYPE":"' . $type . '",');
				$msg .= ('"MESSAGE":' . $message . ',');
				$msg .= ('"PICTURE_FILE":"' . $packageFile . '",');
				$msg .= ('"UPTIME":"' . $uptime . '"');
				$msg .= '}}';

				$userList = $this->getSmartCenterUserList();
				$pushMsg = new PushMessage();
				$pushMsg->pushToList(base64_encode($msg), $userList);

				$this->render('10000', 'Create notice ok');
			}
		}
		$this->render('14001', 'Create notice failed');
	}
}