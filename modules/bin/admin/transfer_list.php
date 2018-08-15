<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$config = config('plan_a');
if (!empty($config['is_withdraw']))
{
	include 'transfer_list-withdraw.php';
}else{
	include 'transfer_list-auto.php';
}