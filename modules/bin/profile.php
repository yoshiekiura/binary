<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if (empty($id))
{
	$id = $Bbc->member['id'];
}else{
	if ($id!=$Bbc->member['id'] && !bin_isDownline($id, $Bbc->member['id']))
	{
		$id = 0;
	}
}
if (empty($id))
{
	echo msg('Maaf, data yang anda akses bukan termasuk dalam jaringan anda', 'danger');
}else{
	$_GET['id'] = $id;
	$editbutton = 0;
	if ($id==$Bbc->member['id'])
	{
		$editbutton = 1;
	}
	$member  = $db->getRow("SELECT * FROM `bin` AS b LEFT JOIN `bbc_account` AS c ON (b.`user_id`=c.`user_id`) WHERE b.`id`={$id}");
	$account = config_decode($member['params']);
	// include 'admin/list_detail.php';
	include tpl('profile.html.php');
}
