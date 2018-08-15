<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($_GET['act']))
{
	switch ($_GET['act'])
	{
		case 'picker':
			include 'register-picker.php';
			die();
			break;
		case 'clonecheck':
			include 'register-clonecheck.php';
			break;
	}
}

if (!empty($_POST['params']))
{
	$output = array(
		'ok' => 0,
		'msg' => '',
		);
	$_POST['params']['serial']  = trim(strtoupper($_POST['params']['serial']));
	$_POST['params']['sponsor'] = trim(strtoupper($_POST['params']['sponsor']));
	$_POST['params']['upline']  = trim(strtoupper($_POST['params']['upline']));
	$_POST['params']['Phone']   = preg_replace('~[^\+0-9]+~', '', $_POST['params']['Phone']);
	if (substr($_POST['params']['Phone'], 0, 1) == '0')
	{
		$_POST['params']['Phone'] = '+62'.substr($_POST['params']['Phone'], 1, strlen($_POST['params']['Phone'])-1);
	}
	$params = array(
		'username'  => $_POST['params']['serial'],
		'password'  => $_POST['params']['pin'],
		'name'      => $_POST['name'],
		'email'     => strtolower($_POST['params']['serial'].'@'.config('site', 'url')),
		'params'    => $_POST['params'],
		'group_ids' => get_config('bin', 'plan_a', 'group_id')
		);
	$user_id = user_create($params);
	if (!empty($user_id))
	{
		$_POST = array();
		$output = array(
			'ok' => 1,
			'msg' => msg(lang('New member has been created'), 'success')
			);
	}else{
		$output = array(
			'ok' => 0,
			'msg' => msg(user_create_validate_msg(), 'danger')
			);
	}
	if (!empty($_GET['is_ajax']))
	{
		unset($_GET['is_ajax']); // supaya class bin berjalan layaknya di halaman biasa
		if ($output['ok'])
		{
			$member = $db->getRow("SELECT * FROM `bin` WHERE `user_id`={$user_id}");
			$upline = bin_fetch_id($member['upline_id']);

			$bin = _class('bin');
			$bin->setMaxlevel(1);
			$output['html'] = $bin->tpl($member, $upline, $member['position'], 1);
		}else{
			$output['html'] = '';
		}

		output_json($output);
	}else{
		echo $output['msg'];
	}
}
include tpl('register.html.php');