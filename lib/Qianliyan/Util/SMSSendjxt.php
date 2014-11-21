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
class Qianliyan_Util_SMS_jxt
{
	static public function format ($url)
	{
		$url = parse_url($url);
		$url = $url['path'] . '?sid=' . session_id() . '&' . $url['query'];
		return $url;
	}
	
	
    public function SendSMSPost($strMobile,$content){
			$url="http://service.winic.org:8009/sys_port/gateway/?id=%s&pwd=%s&to=%s&content=%s&time=";
			$id = urlencode("luoie");
			$pwd = urlencode("luoie888");
			$to = urlencode($strMobile);
			$content = iconv("UTF-8","GB2312",$content); //œ«utf-8×ªÎªgb2312ÔÙ·¢
			$rurl = sprintf($url, $id, $pwd, $to, $content);
			
			//³õÊŒ»¯curl
   			$ch = curl_init() or die (curl_error());
  			//ÉèÖÃURL²ÎÊý
   			curl_setopt($ch,CURLOPT_URL,$rurl);
   			curl_setopt($ch, CURLOPT_POST, 1);
   			curl_setopt($ch, CURLOPT_HEADER, 0);
   			//ÖŽÐÐÇëÇó
   			$result = curl_exec($ch) ;
   			//È¡µÃ·µ»ØµÄœá¹û£¬²¢ÏÔÊŸ
   			//echo $result;
   			//echo curl_error($ch);
   			//¹Ø±ÕCURL
   			curl_close($ch);
    } 
    
    public function SendSMSGet($strMobile,$content){
			$url="http://service.winic.org:8009/sys_port/gateway/?id=%s&pwd=%s&to=%s&content=%s&time=";
			$id = urlencode("luoie");
			$pwd = urlencode("luoie888");
			$to = urlencode($strMobile);
			$content = urlencode($content);
			$rurl = sprintf($url, $id, $pwd, $to, $content);
			//printf("url=%s\n", $rurl);
			$ret = file($rurl);
			//print_r($ret);
    } 
    
}



