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
	$r_level = $db->getAssoc("SELECT id, name FROM bin_level WHERE 1");
	$list = array();
	bin_downline_list($id);
	if (count($list) > 0)
	{
		echo table($list, array('Username', 'Nama', 'Jalur Kaki', 'Peringkat', 'Level', 'Bergabung'));
	}else{
		echo msg("Anda belum memiliki downline");
	}
}
function bin_downline_list($bin_id)
{
	global $sys, $list, $db, $r_level, $Bbc;
	$data = $sys->curl(site_url('bin/fetch').'/'.$bin_id.'?type=json');
	$data = json_decode($data,1);
	$downline = $data['downline'];
	foreach ($downline as $key => $row)
	{
		if (!empty($row))
		{
			$list[] =  array(
					'username' => $row['current']['username'],
					'name'     => $row['current']['name'],
					'position' => $row['current']['position']=='0' ? 'Kiri': 'Kanan',
					'level_id' => $r_level[$row['current']['level_id']],
					'level'    => money($row['current']['depth_upline']-$Bbc->member['depth_upline']),
					'created'  => date('M jS, Y', strtotime($row['current']['created']))
			);
			bin_downline_list($row['current']['user_id']);
		}
	}
}