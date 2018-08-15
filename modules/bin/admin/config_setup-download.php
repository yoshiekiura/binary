<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$filename = 'plan_a.json';
$filepath = _CACHE.$filename;
$output   = array(
	'plan_a'  => config('plan_a'),
	'reward'  => $db->getAll("SELECT * FROM `bin_reward` WHERE 1 ORDER BY id ASC"),
	'level'   => $db->getAll("SELECT * FROM `bin_level` WHERE 1 ORDER BY id ASC"),
	'balance' => $db->getAll("SELECT * FROM `bin_balance_type` WHERE 1 ORDER BY id ASC")
	);
if(file_write($filepath, json_encode($output, JSON_PRETTY_PRINT)))
{
	_func('download');
	download_file($filename, $filepath, false);
	@unlink($filepath);
	die();
}