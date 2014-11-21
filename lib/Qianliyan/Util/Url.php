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
class Qianliyan_Util_Url
{
	static public function format ($url)
	{
		$url = parse_url($url);
		$url = $url['path'] . '?sid=' . session_id() . '&' . $url['query'];
		return $url;
	}
}