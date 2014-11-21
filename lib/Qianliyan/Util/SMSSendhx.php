<?php

/**
 * Qianliyan Util
 *
 * @category   Qianliyan
 * @package    Qianliyan_Util
 * @version    $Id$
 */

/**
 * @package Qianliyan_Util
 */
class Qianliyan_Util_SMS_hx  {

	function getSend($url,$param)
	{
		$ch = curl_init($url."?".$param);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ;
		
		$output = curl_exec($ch);
		
		return $output;	
	}
	
	function postSend($url,$param){
	
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$param);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
	}
	
	function gbkToUtf8($str)
	{
		return rawurlencode(iconv('GB2312','UTF-8',$str));	
	}


    function SendSMSPost($strMobile,$content){

	$strReg = "101100-WEB-HUAX-572308";   
	$strPwd = "LMABODOE";                 
	$strSourceAdd = "";               
    
	$strPhone = $strMobile;
	
	$strContent = "您的密码是： " . $content . "【五人科技】";
	
	//$strContent = $this->gbkToUtf8($content);  
	
	$strSmsUrl = "http://www.stongnet.com/sdkhttp/sendsms.aspx";

	$strSmsParam = "reg=" . $strReg . "&pwd=" . $strPwd . "&sourceadd=" . $strSourceAdd . "&phone=" . $strPhone . "&content=" . $strContent;

	
	$strRes = "";

	$strRes = $this->postSend($strSmsUrl,$strSmsParam);
	
	//echo $strRes;
    }
    
    function SendSMSGet($strMobile,$content){

	$strReg = "101100-WEB-HUAX-572308";   
	$strPwd = "LMABODOE";                 
	$strSourceAdd = "";               
    
	$strPhone = $strMobile;
	$strContent = $this->gbkToUtf8($content);  
	
	$strSmsUrl = "http://www.stongnet.com/sdkhttp/sendsms.aspx";

	$strSmsParam = "reg=" . $strReg . "&pwd=" . $strPwd . "&sourceadd=" . $strSourceAdd . "&phone=" . $strPhone . "&content=" . $strContent;

	
	$strRes = "";

	$strRes = $this->getSend($strSmsUrl,$strSmsParam);
	
	//echo $strRes;
    }    

}



