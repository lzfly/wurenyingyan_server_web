<?php 

$_DataMap = array(
	'Notice' => array(
		'id' => 'id',
		'device_sn' => 'device_sn',
		'message' => 'message',
		'type' => 'type',
		'status' => 'status',
		'uptime' => 'uptime',
	),
);

function M ($model, $data)
{
	global $_DataMap;
	
	$dataMap = isset($_DataMap[$model]) ? $_DataMap[$model] : null;
	if ($dataMap) {
		$dataRes = array();
		foreach ((array) $data as $k => $v) {
			if (array_key_exists($k, $dataMap)) {
				$mapKey = $dataMap[$k];
				$dataRes[$mapKey] = $v;
			}
		}
		return $dataRes;
	}
	
	return $data;
}