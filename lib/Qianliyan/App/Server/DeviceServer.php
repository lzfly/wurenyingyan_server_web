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
class DeviceServer extends Qianliyan_App_Server
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
	 * > 接口说明：设备列表接口
	 * <code>
	 * URL地址：/device/deviceList
	 * 提交方式：GET
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 设备列表接口
	 * @action /device/deviceList
	 * @params is_open '' INT
	 * @params is_online '' INT
	 * @method get
	 */
	public function deviceListAction ()
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
			$isOpen = $this->param('is_open');
			$isOnline = $this->param('is_online');
		
			$deviceDao = $this->dao->load('Core_Device');
		    $deviceList = $deviceDao->getListBySmartCenter($smartcenterSN, $isOpen, $isOnline);
		}else{
			$this->render('14002', 'User has not smart center');
		}

		$this->render('10000', 'Get device list ok', array(
			'device.list' => $deviceList
		));
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：解除绑定到指定摄像头接口
	 * <code>
	 * URL地址：/device/unbindCamera
	 * 提交方式：POST
	 * 参数#1：device_sn，类型：STRING，必须：YES
	 * 参数#2：camera_sn，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 解除绑定到指定摄像头接口
	 * @action /device/unbindCamera
	 * @params device_sn '' STRING
	 * @params camera_sn '' STRING
	 * @method post
	 */
	public function unbindCameraAction()
	{
		$this->doAuth();
		
		if ($this->user)
		{
			$deviceSN = $this->param('device_sn');
			$cameraSN = $this->param('camera_sn');
			
			if ($deviceSN && $cameraSN)
			{
				$devCameraDao = $this->dao->load('Core_DeviceBindCamera');
				$devCameraDao->delete($deviceSN, $cameraSN);

				$msg = '{"actionType": "unbind_camera", "bindInfo": {';
				$msg .= ('"deviceSN":"' . $deviceSN . '",');
				$msg .= ('"cameraSN":"' . $cameraSN . '"');
				$msg .= '}}';

				$smartcenterSN = $this->user['SMARTCENTER_SN'];				
				$smartcenterDao = $this->dao->load('Core_SmartCenter');
				$smartcenterItem = $smartcenterDao->getBySN($smartcenterSN);
				if ($smartcenterItem)
				{
					$pushClientId = $smartcenterItem['PUSH_CLIENTID'];
					
					$receivers = array($pushClientId);
					$pushMsg = new PushMessage();
					$pushMsg->pushToList(base64_encode($msg), $receivers);
				}
				
				$userList = $this->getSmartCenterUserList();
				$pushMsg = new PushMessage();
				$pushMsg->pushToList(base64_encode($msg), $userList);
	
				$this->render('10000', 'Unbind camera ok.');
			}
		}
		$this->render('14005', 'Unbind camera failed.');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：绑定到指定摄像头接口
	 * <code>
	 * URL地址：/device/bindCamera
	 * 提交方式：POST
	 * 参数#1：device_sn，类型：STRING，必须：YES
	 * 参数#2：camera_sn，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 绑定到指定摄像头接口
	 * @action /device/bindCamera
	 * @params device_sn '' STRING
	 * @params camera_sn '' STRING
	 * @method post
	 */
	public function bindCameraAction()
	{
		$this->doAuth();
		
		if ($this->user)
		{
			$deviceSN = $this->param('device_sn');
			$cameraSN = $this->param('camera_sn');
			
			if ($deviceSN && $cameraSN)
			{
				$devCameraDao = $this->dao->load('Core_DeviceBindCamera');
				$devBindCameraItem = $devCameraDao->getByDeviceSN($deviceSN);
				if ($devBindCameraItem)
				{
					try {
						$devCameraDao->update(array(
							'ID'	=> $devBindCameraItem['ID'],
							'CAMERA_SN'	=> $cameraSN
						));
					} catch (Exception $e) {
						$this->render('14003', 'Update device bind camera failed');
					}

					$msg = '{"actionType": "bind_camera", "bindInfo": {';
					$msg .= ('"deviceSN":"' . $deviceSN . '",');
					$msg .= ('"cameraSN":"' . $cameraSN . '"');
					$msg .= '}}';
					
					$smartcenterSN = $this->user['SMARTCENTER_SN'];
					$smartcenterDao = $this->dao->load('Core_SmartCenter');
					$smartcenterItem = $smartcenterDao->getBySN($smartcenterSN);
					if ($smartcenterItem)
					{
						$pushClientId = $smartcenterItem['PUSH_CLIENTID'];
						
						$receivers = array($pushClientId);
						$pushMsg = new PushMessage();
						$pushMsg->pushToList(base64_encode($msg), $receivers);
					}
					
					$userList = $this->getSmartCenterUserList();
					$pushMsg = new PushMessage();
					$pushMsg->pushToList(base64_encode($msg), $userList);
					
					$this->render('10000', 'Bind camera ok');
				}
				else
				{
					$devCameraDao->create(array(
						'DEVICE_SN'	=> $deviceSN,
						'CAMERA_SN' => $cameraSN
					));	
					$this->render('10000', 'Bind camera ok(create).');
				}
			}
		}
		$this->render('14005', 'Bind camera failed.');
	}

	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：获得所有设备绑定的摄像头接口
	 * <code>
	 * URL地址：/device/getBindCameraList
	 * 提交方式：POST
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 获得所有设备绑定的摄像头接口
	 * @action /device/getBindCameraList
	 * @method post
	 */
	public function getBindCameraListAction()
	{
		$this->doAuth();
		
		$devBindCameraDao = $this->dao->load('Core_DeviceBindCamera');
		$result = $devBindCameraDao->getListAll();
		$this->render('10000', 'Get camera of device bind ok', array(
			'deviceBindCamera.list' => $result
		));
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：获得设备绑定的摄像头
	 * <code>
	 * URL地址：/device/getBindCamera
	 * 提交方式：POST
	 * 参数#1：device_sn，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 获得设备绑定的摄像头
	 * @action /device/getBindCamera
	 * @params device_sn '' STRING
	 * @method post
	 */
	public function getBindCameraAction()
	{
		$this->doAuth();
		
		$devSN = $this->param('device_sn');
		if ($devSN)
		{
			$devBindCameraDao = $this->dao->load('Core_DeviceBindCamera');
			$devBindCameraItem = $devBindCameraDao->getByDeviceSN($devSN);
			if ($devBindCameraItem)
			{
				$this->render('10000', 'Get camera of device bind ok', array(
					'deviceBindCamera' => $devBindCameraItem
				));
			}
			else
			{
				$this->render('14001', 'Get bind camera failed.');
			}
		}
		$this->render('14002', 'Get bind camera failed.');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：查看设备信息接口
	 * <code>
	 * URL地址：/device/deviceView
	 * 提交方式：POST
	 * 参数#1：device_sn，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 查看设备信息接口
	 * @action /device/deviceView
	 * @params device_sn '' STRING
	 * @method post
	 */
	public function deviceViewAction ()
	{
		$this->doAuth();
		
		$device_sn = $this->param('device_sn');
		
		// get extra device info
		$deviceDao = $this->dao->load('Core_Device');
		$deviceItem = $deviceDao->getBySN($device_sn);
		if ($deviceItem) {
			$this->render('10000', 'View device ok', array(
				'SN' => $deviceItem
			));
		}
		$this->render('14002', 'View device failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：更新设备信息接口
	 * <code>
	 * URL地址：/device/deviceEdit
	 * 提交方式：POST
	 * 参数#1：device_sn，类型：STRING，必须：YES
	 * 参数#1：key，类型：STRING，必须：YES
	 * 参数#2：val，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 更新设备信息接口
	 * @action /device/deviceEdit
	 * @params device_sn '' STRING
	 * @params key '' STRING
	 * @params val '' STRING
	 * @method post
	 */
	public function deviceEditAction ()
	{
		$this->doAuth();
		
		$device_sn = $this->param('device_sn');
		$key = $this->param('key');
		$val = $this->param('val');
		if ($key) {
			$deviceDao = $this->dao->load('Core_Device');
			$deviceItem = $deviceDao->getBySN($device_sn);
			
			try {
				$deviceDao->update(array(
					'ID'	=> $deviceItem['ID'],
					$key	=> $val,
				));
			} catch (Exception $e) {
				$this->render('14003', 'Update device failed');
			}
			$this->render('10000', 'Update device ok');
		}
		$this->render('14004', 'Update device failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：设备同步接口（有则修改，无则添加）
	 * <code>
	 * URL地址：/device/deviceSync
	 * 提交方式：POST
	 * 参数#1：device_sn，类型：STRING，必须：YES
	 * 参数#2：type_code，类型：STRING，必须：YES
	 * 参数#3：name，类型：STRING，必须：YES
	 * 参数#4：is_open，类型：INT，必须：YES
	 * 参数#5：is_online，类型：INT，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 设备同步接口（有则修改，无则添加）
	 * @action /device/deviceSync
	 * @params device_sn '' STRING
	 * @params type_code '' STRING
	 * @params name '' STRING
	 * @params is_open '' INT
	 * @params is_online '' INT
	 * @method post
	 */
	public function deviceSyncAction()
	{
		$this->doAuth();
		
		$deviceSN = $this->param('device_sn');
		$typeCode = $this->param('type_code');
		$name = $this->param('name');
		$isOpen = $this->param('is_open');
		$isOnline = $this->param('is_online');
		
		$deviceDao = $this->dao->load('Core_Device');
		$deviceItem = $deviceDao->getBySN($deviceSN);
		if ($deviceItem)
		{
			$hasChanged = false;
		
			$msg = '{"actionType": "device_sync", "deviceInfo": {';
			$msg .= ('"SN":"' . $deviceSN . '"');
		
			$syncData = array();
			$syncData['ID'] = $deviceItem['ID'];
		
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
					$hasChanged = $smartcenterSN <> $deviceItem['SMARTCENTER_SN'];
				}
			
				$syncData['SMARTCENTER_SN'] = $smartcenterSN;
			}
			
			if ($typeCode)
			{
				$msg .= (',"TYPE_CODE":"' . $typeCode . '"');
				if (!$hasChanged)
				{
					$hasChanged = $typeCode <> $deviceItem['TYPE_CODE'];
				}
			
				$deviceTypeDao = $this->dao->load('Core_DeviceType');
				$deviceType = $deviceTypeDao->getByCode($typeCode);
				if($deviceType)
				{
				    ;
				}
				else
				{
				    $this->render('14001', 'The device has not type');
				}
			
				$syncData['TYPE_CODE'] = $typeCode;
			}
			else
			{
				$msg .= (',"TYPE_CODE":"' . $deviceItem['TYPE_CODE'] . '"');
			}
			
			if ($name)
			{
				$msg .= (',"NAME":"' . $name . '"');
				if (!$hasChanged)
				{
					$hasChanged = $name <> $deviceItem['NAME'];
				}
			
				$syncData['NAME'] = $name;
			}
			else
			{
				$msg .= (',"NAME":"' . $deviceItem['NAME'] . '"');
			}
			
			$openStateChanged = false;
			if (is_numeric($isOpen))
			{
				$syncData['IS_OPEN'] = $isOpen;
				$msg .= (',"IS_OPEN":' . $isOpen);

				if (!$hasChanged)
				{
					$hasChanged = $isOpen <> $deviceItem['IS_OPEN'];
					$openStateChanged = true;
				}
			}
			else
			{
				$msg .= (',"IS_OPEN":' . $deviceItem['IS_OPEN']);
			}
			
			if (is_numeric($isOnline))
			{
				$syncData['IS_ONLINE'] = $isOnline;
				$msg .= (',"IS_ONLINE":' . $isOnline);
				
				if (!$hasChanged)
				{
					$hasChanged = $isOnline <> $deviceItem['IS_ONLINE'];
				}
			}
			else
			{
				$msg .= (',"IS_ONLINE":' . $deviceItem['IS_ONLINE']);
			}
			
			try {
				$deviceDao->update($syncData);
			} catch (Exception $e) {
				$this->render('14001', 'Sync device failed');
			}
			
			$msg .= '}}';
			
			/* 推送消息 */
			if ($hasChanged)
			{
				$userList = $this->getSmartCenterUserList();
				$pushMsg = new PushMessage();
				$pushMsg->pushToList(base64_encode($msg), $userList);

				if ($openStateChanged)
				{
					if ($isOpen == "1")
					{
						$msg2 = '{"actionType":"open_device", "deviceSN":["' . $deviceSN . '"]}';
					}
					else
					{
						$msg2 = '{"actionType":"close_device", "deviceSN":["' . $deviceSN . '"]}';
					}
					$smartcenterDao = $this->dao->load('Core_SmartCenter');
					$smartcenter = $smartcenterDao->getBySN($smartcenterSN);
					if ($smartcenter)
					{
						$pushClientId = $smartcenter['PUSH_CLIENTID'];
						$receivers = array($pushClientId);
						$pushMsg = new PushMessage();
						$pushMsg->pushToList(base64_encode($msg2), $receivers);
					}
				}
				
				$this->render('10000', 'Sync device ok & message pushed');
			}
			
			$this->render('10000', 'Sync device ok');
		}
		else
		{
			if ($deviceSN && $typeCode && $name)
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
					$deviceTypeDao = $this->dao->load('Core_DeviceType');
					$deviceType = $deviceTypeDao->getByCode($typeCode);
					if($deviceType)
					{
					    ;
					}
					else
					{
					    $this->render('14002', 'The device has not type');
					}
					
					$smartcenterDao = $this->dao->load('Core_SmartCenter');
					$smartcenter = $smartcenterDao->getBySN($smartcenterSN);
					if ($smartcenter)
					{
						;
					}
					else
					{
						$this->render('14003', 'The device has not smart center');
					}

					$deviceDao->create(array(
						'SN'	=> $deviceSN,
						'TYPE_CODE' => $typeCode,
						'SMARTCENTER_SN'	=> $smartcenterSN,
						'NAME'	=> $name,
						'IS_OPEN' => $isOpen,
						'IS_ONLINE' => $isOnline
					));	

					/* 推送消息 */
					$msg = '{"actionType": "device_sync", "deviceInfo": {';
					$msg .= ('"SN":"' . $deviceSN . '",');
					$msg .= ('"TYPE_CODE":"' . $typeCode . '",');
					$msg .= ('"SMARTCENTER_SN":"' . $smartcenterSN . '",');
					$msg .= ('"NAME":"' . $name . '",');
					$msg .= ('"IS_OPEN":' . $isOpen . ',');
					$msg .= ('"IS_ONLINE":' . $isOnline);
					$msg .= '}}';
					
					$userList = $this->getSmartCenterUserList();
					$pushMsg = new PushMessage();
					$pushMsg->pushToList(base64_encode($msg), $userList);

					$this->render('10000', 'Sync device ok');
				}
			}			
		}
		$this->render('14005', 'Sync device failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：新建设备接口
	 * <code>
	 * URL地址：/device/deviceCreate
	 * 提交方式：POST
	 * 参数#1：device_sn，类型：STRING，必须：YES
	 * 参数#2：type_code，类型：STRING，必须：YES
	 * 参数#3：name，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 新建设备接口
	 * @action /device/deviceCreate
	 * @params device_sn '' STRING
	 * @params type_code '' STRING
	 * @params name '' STRING
	 * @method post
	 */
	public function deviceCreateAction ()
	{
		$this->doAuth();
		
		$deviceSN = $this->param('device_sn');
		$typeCode = $this->param('type_code');
		$name = $this->param('name');
		
		$deviceDao = $this->dao->load('Core_Device');
		$device = $deviceDao->getBySN($deviceSN);
		if($device){
			$this->render('14001', 'device has exsited');
		}
		else
		{
			if ($deviceSN && $typeCode && $name) {
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
					$deviceTypeDao = $this->dao->load('Core_DeviceType');
					$deviceType = $deviceTypeDao->getByCode($typeCode);
					if($deviceType)
					{
					    ;
					}
					else
					{
					    $this->render('14002', 'The device has not type');
					}
					
					$smartcenterDao = $this->dao->load('Core_SmartCenter');
					$smartcenter = $smartcenterDao->getBySN($smartcenterSN);
					if ($smartcenter)
					{
						;
					}
					else
					{
						$this->render('14003', 'The device has not smart center');
					}
	
					$deviceDao->create(array(
						'SN'	=> $deviceSN,
						'TYPE_CODE' => $typeCode,
						'SMARTCENTER_SN'	=> $smartcenterSN,
						'NAME'	=> $name
					));	
					$this->render('10000', 'Create device ok');
				}
			}
		}
		$this->render('14005', 'Create device failed');
	}
	
}