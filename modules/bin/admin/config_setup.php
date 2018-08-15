<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
1. alert
2. form
3. validation

*/
$sys->nav_add('Reset Market Plan');
if ($db->debug)
{
	if (empty($_GET['type']))
	{
		include 'config_setup-alert.php';
	}else{
		switch ($_GET['type'])
		{
			case 'download':
				include 'config_setup-download.php';
				break;
			case 'validation':
				include 'config_setup-validation.php';
				break;
			default:
				include 'config_setup-form.php';
				break;
		}
	}
}else{
	echo msg('Maaf, anda tidak memiliki privilege yang cukup untuk merubah marketplan', 'danger');
}