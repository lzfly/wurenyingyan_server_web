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

require_once 'Zend/File/Transfer/Adapter/Http.php';

/**
 * @package Qianliyan_App_Server
 */
class CameraServer extends Qianliyan_App_Server
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
	 * > 接口说明：摄像头列表接口
	 * <code>
	 * URL地址：/camera/cameraList
	 * 提交方式：GET
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 设备列表接口
	 * @action /camera/cameraList
	 * @method get
	 */
	public function cameraListAction ()
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
		
		if($smartcenterSN){
			$cameraDao = $this->dao->load('Core_Camera');
		    $cameraList = $cameraDao->getListBySmartCenter($smartcenterSN);
		}else{
			$this->render('14002', 'User has not smart center');
		}

		$this->render('10000', 'Get camera list ok', array(
			'camera.list' => $cameraList
		));
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：查看摄像头信息接口
	 * <code>
	 * URL地址：/camera/cameraView
	 * 提交方式：POST
	 * 参数#1：camera_sn，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 查看摄像头信息接口
	 * @action /camera/cameraView
	 * @params camera_sn '' STRING
	 * @method post
	 */
	public function cameraViewAction ()
	{
		$this->doAuth();
		
		$cameraSN = $this->param('camera_sn');
		
		// get extra device info
		$cameraDao = $this->dao->load('Core_Camera');
		$cameraItem = $cameraDao->getBySN($cameraSN);
		if ($cameraItem) {
			$this->render('10000', 'View camera ok', array(
				'SN' => $cameraItem
			));
		}
		$this->render('14002', 'View camera failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：更新摄像头信息接口
	 * <code>
	 * URL地址：/camera/cameraEdit
	 * 提交方式：POST
	 * 参数#1：camera_sn，类型：STRING，必须：YES
	 * 参数#1：key，类型：STRING，必须：YES
	 * 参数#2：val，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 更新摄像头信息接口
	 * @action /camera/cameraEdit
	 * @params camera_sn '' STRING
	 * @params key '' STRING
	 * @params val '' STRING
	 * @method post
	 */
	public function cameraEditAction ()
	{
		$this->doAuth();
		
		$cameraSN = $this->param('camera_sn');
		$key = $this->param('key');
		$val = $this->param('val');
		if ($key) {
			$cameraDao = $this->dao->load('Core_Camera');
			$cameraItem = $cameraDao->getBySN($cameraSN);
			
			try {
				$cameraDao->update(array(
					'ID'	=> $cameraItem['ID'],
					$key	=> $val,
				));
			} catch (Exception $e) {
				$this->render('14003', 'Update camera failed');
			}
			$this->render('10000', 'Update camera ok');
		}
		$this->render('14004', 'Update camera failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：摄像头同步接口（有则修改，无则添加）
	 * <code>
	 * URL地址：/camera/cameraSync
	 * 提交方式：POST
	 * 参数#1：camera_sn，类型：STRING，必须：YES
	 * 参数#2：name，类型：STRING，必须：YES
	 * 参数#3：ip，类型：STRING，必须：YES
	 * 参数#4：port，类型：INT，必须：YES
	 * 参数#5：model，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 设备同步接口（有则修改，无则添加）
	 * @action /camera/cameraSync
	 * @params camera_sn '' STRING
	 * @params name '' STRING
	 * @params ip '' STRING
	 * @params port 8001 INT
	 * @params model '' STRING
	 * @method post
	 */
	public function cameraSyncAction()
	{
		$this->doAuth();
		
		$cameraSN = $this->param('camera_sn');
		$name = $this->param('name');
		$ip = $this->param('ip');
		$port = $this->param('port');
		$model = $this->param('model');
		
		$cameraDao = $this->dao->load('Core_Camera');
		$cameraItem = $cameraDao->getBySN($cameraSN);
		if ($cameraItem)
		{
			$hasChanged = false;
		
			$msg = '{"actionType": "camera_sync", "cameraInfo": {';
			$msg .= ('"SN":"' . $cameraSN . '"');

			$syncData = array();
			$syncData['ID'] = $cameraItem['ID'];

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
				$msg .= (',"SMARTCENTER_SN":"' . $smartcenterSN . '"');
				if (!$hasChanged)
				{
					$hasChanged = $smartcenterSN <> $cameraItem['SMARTCENTER_SN'];
				}
			
				$syncData['SMARTCENTER_SN'] = $smartcenterSN;
			}
			
			if ($name)
			{
				$msg .= (',"NAME":"' . $name . '"');
				if (!$hasChanged)
				{
					$hasChanged = $name <> $cameraItem['NAME'];
				}
			
				$syncData['NAME'] = $name;
			}
			else
			{
				$msg .= (',"NAME":"' . $cameraItem['NAME'] . '"');
			}
			
			if ($ip)
			{
				$msg .= (',"IP":"' . $ip . '"');
				if (!$hasChanged)
				{
					$hasChanged = $ip <> $cameraItem['IP'];
				}
				
				$syncData['IP'] = $ip;
			}
			else
			{
				$msg .= (',"IP":"' . $cameraItem['IP'] . '"');
			}
			
			if ($model)
			{
				$msg .= (',"MODEL":"' . $model . '"');
				if (!$hasChanged)
				{
					$hasChanged = $model <> $cameraItem['MODEL'];
				}
				
				$syncData['MODEL'] = $model;
			}
			else
			{
				$msg .= (',"MODEL":"' . $cameraItem['MODEL'] . '"');
			}
			
			if (is_numeric($port))
			{
				$syncData['port'] = $port;
				$msg .= (',"PORT":' . $port);
				
				if (!$hasChanged)
				{
					$hasChanged = $port <> $cameraItem['PORT'];
				}
			}
			else
			{
				$msg .= (',"PORT":' . $cameraItem['PORT']);
			}
			
			try {
				$cameraDao->update($syncData);
			} catch (Exception $e) {
				$this->render('14001', 'Sync camera failed');
			}
			
			$msg .= '}}';
			
			if (!$hasChanged)
			{
				$hasChanged = $port <> $cameraItem['PORT'];
			}			
			
			/* 推送消息 */
			if ($hasChanged)
			{
				$userList = $this->getSmartCenterUserList();
				$pushMsg = new PushMessage();
				$pushMsg->pushToList(base64_encode($msg), $userList);
			}
			
			$this->render('10000', 'Sync camera ok');
		}
		else
		{
			if ($cameraSN && $name && $ip && $model)
			{
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
					$smartcenterDao = $this->dao->load('Core_SmartCenter');
					$smartcenter = $smartcenterDao->getBySN($smartcenterSN);
					if ($smartcenter)
					{
						;
					}
					else
					{
						$this->render('14003', 'The camera has not smart center');
					}

					$cameraDao->create(array(
						'SN'	=> $cameraSN,
						'SMARTCENTER_SN'	=> $smartcenterSN,
						'NAME'	=> $name,
						'IP' => $ip,
						'PORT' => $port,
						'MODEL' => $model
					));	

					/* 推送消息 */
					$msg = '{"actionType": "camera_sync", "cameraInfo": {';
					$msg .= ('"SN":"' . $cameraSN . '",');
					$msg .= ('"SMARTCENTER_SN":"' . $smartcenterSN . '",');
					$msg .= ('"NAME":"' . $name . '",');
					$msg .= ('"IP":"' . $ip . '",');
					$msg .= ('"PORT":' . $port . ',');
					$msg .= ('"MODEL":"' . $model . '",');
					$msg .= '}}';
					
					$userList = $this->getSmartCenterUserList();
					$pushMsg = new PushMessage();
					$pushMsg->pushToList(base64_encode($msg), $userList);

					$this->render('10000', 'Sync camera ok');
				}
			}			
		}
		$this->render('14005', 'Sync camera failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：获取指定摄像头的多张连拍图
	 * <code>
	 * URL地址：/camera/getCamera5Lianpai
	 * 提交方式：POST
	 * 参数#1：camera_sn，类型：STRING，必须：YES
	 * 参数#2：num，类型：INT，必须：YES
	 * 参数#3：notice_id，类型：STRINg，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 获取指定摄像头的多张连拍图
	 * @action /camera/getCamera5Lianpai
	 * @params camera_sn '' STRING
	 * @params num 5 INT
	 * @params notice_code '' STRING
	 * @method post
	 */
	 /*
	public function getCamera5LianpaiAction()
	{
		$this->doAuth();
		
		if ($this->user)
		{
			$cameraSN = $this->param('camera_sn');
			$num = $this->param('num');
			$noticeCode = $this->param('notice_code');
			
			$cameraDao = $this->dao->load('Core_Camera');
			$cameraItem = $cameraDao->getBySN($cameraSN);
			if($cameraItem){
				$smartcenterSN = $this->user['SMARTCENTER_SN'];
				
				$smartcenterDao = $this->dao->load('Core_SmartCenter');
				$smartcenterItem = $smartcenterDao->getBySN($smartcenterSN);
				if ($smartcenterItem)
				{
					$pushClientId = $smartcenterItem['PUSH_CLIENTID'];
					
					$msg = '{"actionType": "camera_5lianpai", "cameraInfo": {';
					$msg .= ('"cameraSN":"' . $cameraSN . '",');
					$msg .= ('"num":' . $num . ',');
					$msg .= ('"noticeCode":"' . $noticeCode . '"');
					$msg .= '}}';

					$receivers = array($pushClientId);
					$pushMsg = new PushMessage();
					$pushMsg->pushToList(base64_encode($msg), $receivers);
					
					$this->render('10000', 'Get camera 5 lianpai ok');
				}
				else
				{
					$this->render('14004', 'Get camera 5 lianpai failed, not exists smartcenter');
				}
			}
			else
			{
				$this->render('14005', 'Get camera 5 lianpai failed, not exists camera');
			}
		}
	}
	*/
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：返回指定摄像头多张连拍图接口
	 * <code>
	 * URL地址：/camera/uploadCamera5Lianpai
	 * 提交方式：POST
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 返回指定摄像头多张连拍图接口
	 * @action /camera/uploadCamera5Lianpai
	 * @method post
	 */
	public function uploadCamera5LianpaiAction()
	{
		$this->doAuth();

       	$upload = new Zend_File_Transfer_Adapter_Http();
       	$upload->setDestination(__WWW_PICTURES);
       	$fileName = $upload->getFileName();
		if ($upload->receive()) {
			/* 推送消息 */
/*
			$msg = '{"actionType": "camera_5lianpai_reply", "cameraInfo": {';
			$msg .= ('"cameraSN":"' . $cameraSN . '",');
			$msg .= ('"noticeCode":"' . $noticeCode . '",');
			$msg .= ('"package":"' . basename($fileName) . '"');
			$msg .= '}}';

			$userList = $this->getSmartCenterUserList();
			$pushMsg = new PushMessage();
			$pushMsg->pushToList(base64_encode($msg), $userList);
*/			
			$this->render('10000', 'Upload camera 5 lianpai ok' . $fileName);
		}
		else
		{
			$this->render('14005', 'Upload camera 5 lianpai failed.');
		}
	}

	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：返回指定摄像头实时截图接口
	 * <code>
	 * URL地址：/camera/uploadCameraScreenshot
	 * 提交方式：POST
	 * 参数#1：camera_sn，类型：STRING，必须：YES
	 * 参数#2：to_user，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 返回指定摄像头实时截图接口
	 * @action /camera/uploadCameraScreenshot
	 * @params camera_sn '' STRING
	 * @params to_user '' STRING
	 * @method post
	 */
	public function uploadCameraScreenshotAction()
	{
		$this->doAuth();

       	$upload = new Zend_File_Transfer_Adapter_Http();
       	$upload->setDestination(__WWW_PICTURES);
       	$fileName = $upload->getFileName();
		if ($upload->receive()) {
			$cameraSN = $this->param('camera_sn');
			$toUserName = $this->param('to_user');
			
			$userDao = $this->dao->load('Core_User');
			$userItem = $userDao->getByName($toUserName);
			if ($userItem)
			{
				/* 推送消息 */
				$msg = '{"actionType": "camera_screenshot_reply", "cameraInfo": {';
				$msg .= ('"cameraSN":"' . $cameraSN . '",');
				$msg .= ('"from":"' . $toUserName . '",');
				$msg .= ('"image":"' . __HOST_WEBSITE . '/pictures/' . basename($fileName) . '"');
				$msg .= '}}';

				$receivers = array($userItem['PUSH_CLIENTID']);
				$pushMsg = new PushMessage();
				$pushMsg->pushToList(base64_encode($msg), $receivers);

				$this->render('10000', 'Upload camera screenshot ok' . $fileName);
			}
			else
			{
				$this->render('14004', 'Upload camera screenshot failed, no receiver exists.');
			}
		}
		else
		{
			$this->render('14005', 'Upload camera screenshot failed.');
		}
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：获取指定摄像头实时截图接口
	 * <code>
	 * URL地址：/camera/getCameraScreenshot
	 * 提交方式：POST
	 * 参数#1：camera_sn，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 获取指定摄像头实时截图接口
	 * @action /camera/getCameraScreenshot
	 * @params camera_sn '' STRING
	 * @method post
	 */
	public function getCameraScreenshotAction()
	{
		$this->doAuth();
		
		if ($this->user)
		{
			$cameraSN = $this->param('camera_sn');
			
			$cameraDao = $this->dao->load('Core_Camera');
			$cameraItem = $cameraDao->getBySN($cameraSN);
			if($cameraItem){
				$smartcenterSN = $this->user['SMARTCENTER_SN'];
				
				$smartcenterDao = $this->dao->load('Core_SmartCenter');
				$smartcenterItem = $smartcenterDao->getBySN($smartcenterSN);
				if ($smartcenterItem)
				{
					$pushClientId = $smartcenterItem['PUSH_CLIENTID'];
					
					/* 推送消息 */
					$msg = '{"actionType": "camera_screenshot", "cameraInfo": {';
					$msg .= ('"cameraSN":"' . $cameraSN . '",');
					$msg .= ('"from":"' . $this->user['NAME'] . '"');
					$msg .= '}}';

					$receivers = array($pushClientId);
					$pushMsg = new PushMessage();
					$pushMsg->pushToList(base64_encode($msg), $receivers);
					
					$this->render('10000', 'Get camera screenshot ok');
				}
				else
				{
					$this->render('14004', 'Get camera screenshot failed, not exists smartcenter');
				}
			}
			else
			{
				$this->render('14005', 'Get camera screenshot failed, not exists camera');
			}			
		}
		else
		{
			$this->render('14003', 'Please login first by user.');
		}
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：新建摄像头接口
	 * <code>
	 * URL地址：/camera/cameraCreate
	 * 提交方式：POST
	 * 参数#1：camera_sn，类型：STRING，必须：YES
	 * 参数#2：name，类型：STRING，必须：YES
	 * 参数#3：ip，类型：STRING，必须：YES
	 * 参数#2：port，类型：INT，必须：YES
	 * 参数#2：model，类型：STRING，必须：YES 
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 新建摄像头接口
	 * @action /camera/cameraCreate
	 * @params camera_sn '' STRING
	 * @params name '' STRING
	 * @params ip '' STRING
	 * @params port '' INT
	 * @params model '' STRING
	 * @method post
	 */
	public function cameraCreateAction ()
	{
		$this->doAuth();
		
		$cameraSN = $this->param('camera_sn');
		$name = $this->param('name');
		$ip = $this->param('ip');
		$port = $this->param('port');
		$model = $this->param('model');
		
		$cameraDao = $this->dao->load('Core_Camera');
		$cameraItem = $cameraDao->getBySN($cameraSN);
		if($cameraItem){
			$this->render('14001', 'camera has exsited');
		}
		else
		{
			if ($cameraSN && $name && $ip) {
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
					$smartcenterDao = $this->dao->load('Core_SmartCenter');
					$smartcenter = $smartcenterDao->getBySN($smartcenterSN);
					if ($smartcenter)
					{
						;
					}
					else
					{
						$this->render('14003', 'The camera has not smart center');
					}
	
					$cameraDao->create(array(
						'SN'	=> $cameraSN,
						'SMARTCENTER_SN'	=> $smartcenterSN,
						'NAME'	=> $name,
						'IP' => $ip,
						'PORT' => $port
					));	
					$this->render('10000', 'Create camera ok');
				}
			}
		}
		$this->render('14005', 'Create camera failed');
	}
	
}