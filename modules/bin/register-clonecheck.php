<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($_GET['upline']) && isset($_GET['position']) && !empty($user->id))
{
	$test = (array)$user;
	// UPLINE
	$test['params']['upline'] = $_GET['upline'];
	// POSITION
	$test['params']['position'] = $_GET['position'];

	// pr($test, __FILE__.':'.__LINE__);
	$out    = bin_user_create_validate($test, FALSE) ? 1 : 0;
	$output = array(
		'ok'      => 1,
		'message' => $out ? 'success' : user_create_validate_msg(),
		'result'  => $out
		);
	@unlink(_CACHE.'user_create_validate_msg.txt');
	output_json($output);
}
