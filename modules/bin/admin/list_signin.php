<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$member = bin_fetch_id($id);
if (!empty($member['user_id']))
{
	redirect('index.php?mod=_cpanel.user&act=force2Login&id='.$member['user_id']);
}else{
	echo msg('Maaf, member tidak ditemukan', 'danger');
}