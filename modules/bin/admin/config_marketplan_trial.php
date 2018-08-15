<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Uji Market Plan');
if (empty($db->debug))
{
	echo msg('Maaf, fitur ini hanya bisa digunakan ketika masa development', 'danger');
}else{
	include dirname($Bbc->mod['root']).'/tools/member.php';
}