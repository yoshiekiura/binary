<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($_GET['id']))
{
	$id  = intval($_GET['id']);
	$sql = "`id`={$id}";
}else{

	$late = date('Y-m-d H:i:s', strtotime('-5 MINUTE'));
	$sql  = "`updated`<'{$late}'";
}
$r_late = $db->getAll("SELECT * FROM `bin` WHERE `done`=0 AND {$sql} ORDER BY `id` ASC LIMIT 20");
if (!empty($r_late))
{
	$config = get_config('bin', 'plan_a');
	foreach ($r_late as $late)
	{
		if (!empty($late['tasking']))
		{
			list($func, $i2, $i3, $i4, $i5, $i6) = explode('|', $late['tasking']);
			if ($i2==$late['id'])
			{
				$member = $late;
			}else{
				$member = bin_fetch($i2);
			}
			if (empty($member) && function_exists('iLog'))
			{
				iLog($late);
			}
			echo $member['username'].' -> '.$member['tasking']."\n";
			if ($func=='bin_up_reward')
			{
				_class('async')->run($func, [$config, $member, bin_fetch($i3), @intval($i6)]);
			}else{
				_class('async')->run($func, [$config, $member, bin_fetch($i3), bin_fetch($i4), $i5, @intval($i6)]);
			}
		}else{
			$db->Execute("UPDATE `bin` SET `tasking`='', `done`=1 WHERE `id`={$late['id']}");
		}
	}
}else{
	$i = $db->getOne("SELECT COUNT(*) FROM `bin` WHERE `done`=0");
	if ($i==0 && $db->getRow("SHOW TABLES LIKE 'bbc_async'"))
	{
		$db->Execute("TRUNCATE TABLE `bbc_async`");
	}
}
die();