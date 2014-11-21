<?php

require_once("IGt.Push.php");

define("APPID", "JAlsuoOtgg7yGyghqlYuE8");
define("APPKEY", "XjZhYo0inu7ckQONCQ2OR3");
define("MASTERSECRET", "CyzMEiT0ht9VurO349EQR5");
define("HOST", "http://sdk.open.api.igexin.com/apiex.htm");
define("TITLE", "五人鹰眼");

class PushNotification
{

	public function pushToList($msg, $clientids)
	{
		if (is_null($clientids) || !isset($clientids) || empty($clientids))
		{
			return false;
		}

		putenv("needDetails=true");

		$igt = new IGeTui(HOST, APPKEY, MASTERSECRET);

		$template = $this->IGtNotificationTemplate($msg);
		
		$message = new IGtSingleMessage();
		$message->set_isOffline(true);
		$message->set_offlineExpireTime(3600 * 24 * 1000);
		$message->set_data($template);

		$contentId = $igt->getContentId($message);

		$receiverList = array();
		if (is_array($clientids))
		{
			foreach ($clientids as $cid)
			{
				if (!is_null($cid) && isset($cid) && !empty($cid))
				{
					$receiver = new IGtTarget();
					$receiver->set_appId(APPID);
					$receiver->set_clientId($cid);
					array_push($receiverList, $receiver);
				}
			}
		}
		else
		{
			$cids = split(",", $clientids);
			foreach ($cids as $cid)
			{
				if (!is_null($cid) && isset($cid) && !empty($cid))
				{
					$receiver = new IGtTarget();
					$receiver->set_appId(APPID);
					$receiver->set_clientId($cid);
					array_push($receiverList, $receiver);
				}
			}
		}

		$resp = $igt->pushMessageToList($contentId, $receiverList);
		if (is_array($resp))
		{
			if (array_key_exists("result", $resp))
			{
				if ($resp["result"] == "ok")
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function IGtNotificationTemplate($msg)
	{
		$template = new IGtNotificationTemplate();
		$template->set_appId(APPID);
		$template->set_appkey(APPKEY);
		$template->set_transmissionType(2);
		$template->set_title(TITLE);
		$template->set_text($msg);
		$template->set_logo("");
		$template->set_isRing(true);
		$template->set_isVibrate(true);
		$template->set_isClearable(true);
		return $template;
	}
}

?>