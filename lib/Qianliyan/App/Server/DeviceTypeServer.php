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
class DeviceTypeServer extends Qianliyan_App_Server
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
	 * > 接口说明：设备类型列表接口
	 * <code>
	 * URL地址：/deviceType/deviceTypeList
	 * 提交方式：GET
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 设备类型列表接口
	 * @action /deviceType/deviceTypeList
	 * @method get
	 */
	public function deviceTypeListAction ()
	{
		$this->doAuth();
		
		$deviceTypeDao = $this->dao->load('Core_DeviceType');
		$deviceTypeList = $deviceTypeDao->getListAll();
		$this->render('10000', 'Get device type list ok', array(
			'deviceType.list' => $deviceTypeList
		));
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：查看设备类型信息接口
	 * <code>
	 * URL地址：/deviceType/deviceTypeView
	 * 提交方式：POST
	 * 参数#1：device_code，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 查看设备信息接口
	 * @action /deviceType/deviceTypeView
	 * @params device_code '' STRING
	 * @method post
	 */
	public function deviceTypeViewAction ()
	{
		$this->doAuthAdmin();
		
		$deviceCode = $this->param('device_code');
		// get extra device type info
		$deviceTypeDao = $this->dao->load('Core_DeviceType');
		$deviceTypeItem = $deviceTypeDao->getByCode($deviceCode);
		if ($deviceTypeItem) {
			$this->render('10000', 'View device type ok', array(
				'deviceType' => $deviceTypeItem
			));
		}
		$this->render('14002', 'View device type failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：更新设备类型信息接口
	 * <code>
	 * URL地址：/deviceType/deviceTypeEdit
	 * 提交方式：POST
	 * 参数#1：device_code，类型：STRING，必须：YES
	 * 参数#2：key，类型：STRING，必须：YES
	 * 参数#3：val，类型：STRING，必须：YES
	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 更新设备类型信息接口
	 * @action /deviceType/deviceTypeEdit
	 * @params device_code '' STRING
	 * @params key '' STRING
	 * @params val '' STRING
	 * @method post
	 */
	public function deviceTypeEditAction ()
	{
		$this->doAuthAdmin();
		
		$deviceCode = $this->param('device_code');
		$key = $this->param('key');
		$val = $this->param('val');
		if ($key) {
			$deviceTypeDao = $this->dao->load('Core_DeviceType');
			$deviceTypeItem = $deviceTypeDao->getByCode($deviceCode);
			try {
				$deviceTypeDao->update(array(
					'ID'	=> $deviceTypeItem['ID'],
					$key	=> $val,
				));
			} catch (Exception $e) {
				$this->render('14003', 'Update device type failed');
			}
			$this->render('10000', 'Update device type ok');
		}
		$this->render('14004', 'Update device type failed');
	}
	
	/**
	 * ---------------------------------------------------------------------------------------------
	 * > 接口说明：新建设备类型接口
	 * <code>
	 * URL地址：/deviceType/deviceTypeCreate
	 * 提交方式：POST
	 * 参数#1：device_code，类型：STRING，必须：YES
	 * 参数#2：name，类型：STRING，必须：YES

	 * </code>
	 * ---------------------------------------------------------------------------------------------
	 * @title 新建设备类型接口
	 * @action /deviceType/deviceTypeCreate
	 * @params device_code '' STRING
	 * @params name '' STRING
	 * @method post
	 */
	public function deviceTypeCreateAction ()
	{
		$this->doAuthAdmin();
		
		$deviceCode = $this->param('device_code');
		$name = $this->param('name');
		
		$deviceTypeDao = $this->dao->load('Core_DeviceType');
		$deviceTypeItem = $deviceTypeDao->getByCode($deviceCode);
		if($deviceTypeItem){
			$this->render('14005', 'device type has exsited');
		}

		if ($deviceCode && $name) {
			$deviceTypeDao->create(array(
				'CODE'	=> $deviceCode,
				'NAME'	=> $name
			));
			$this->render('10000', 'Create device type ok');
		}
		else
		{
			$this->render('14006', 'Create device type failed');
		}
	}
}