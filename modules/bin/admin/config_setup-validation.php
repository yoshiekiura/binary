<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$tmpfile = _CACHE.'marketplan.json';
if (file_exists($tmpfile))
{
	$json      = file_read($tmpfile);
	$config    = json_decode($json, 1);
	if (!empty($_POST['submit']) && $_POST['submit']=='SAVE')
	{
		$Bbc->bin_execute = array();
		$config = bin_marketplan_reset($config);
		if($config)
		{
			@unlink($tmpfile);
			$is_ok = true;
			$db->Execute('START TRANSACTION');
			$db->Execute('SET foreign_key_checks = 0');
			foreach ($Bbc->bin_execute as $q)
			{
				if(!$db->Execute($q))
				{
					$is_ok = false;
				}
				if (!$is_ok)
				{
					break;
				}
			}
			$db->Execute('SET foreign_key_checks = 1');
			if ($is_ok)
			{
				$db->Execute('COMMIT');
				@rename(bin_path(), _CACHE.'files_'.date('YmdHis'));
				set_config('plan_a', $config['plan_a']);
				bin_path_create(1, $config['plan_a']['prefix'].'100001');
				$db->cache_clean();
				if (!empty($_POST['uri'])) // Dari halaman uji market
				{
					redirect($_POST['uri']);
				}else{
					redirect();
				}
			}else{
				$db->Execute('ROLLBACK');
				echo msg('Maaf, perubahan marketplan tidak bisa disimpan', 'danger');
			}
		}else{
			echo msg('Maaf, perubahan marketplan tidak bisa disimpan', 'danger');
		}
	}
	if (!empty($config['plan_a']))
	{
		$config = $config['plan_a'];
	}
	include 'config_marketplan.php';
}else{
	// $uri = !empty($_POST['uri']) ? $_POST['uri'] : '';
	redirect('index.php?mod=bin.config_marketplan');
}
