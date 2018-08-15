<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id   = @intval($_GET['id']);
$data = $db->getRow('SELECT * FROM bin_reward WHERE `id`='.$id.' AND `active`=1 ORDER BY id ASC');
if (!empty($data))
{
	include 'reward_claim.php';
	$data['image']         = bin_reward_src($data['image'],$data['id']);
	$data['amount']        = money($data['amount']);
	$data['total_sponsor'] = money($data['total_sponsor']);
	$data['total_left']    = money($data['total_left']);
	$data['total_right']   = money($data['total_right']);
	$data['accumulate']    = $data['accumulate'] ? lang('Ya') : lang('Tidak');

	$serials  = $db->getAssoc("SELECT id, name FROM bin_serial_type WHERE 1");
	$cserial  = count($serials);
	if ($cserial > 1)
	{
		$data['serial'] = $data['serial_type_id'] ? $serials[$data['serial_type_id']] : lang('any serial');
	}
	$levels   = $db->getAssoc("SELECT id, name FROM bin_level WHERE 1");
	$clevel   = count($levels);
	if ($clevel > 1)
	{
		$data['level'] = $data['level_id'] ? $levels[$data['level_id']] : lang('any level');
	}
	$myreward = array();
	/* SIAPKAN POTENTIAL REWARD */
	if (empty($plan_a['reward_auto']))
	{
		$myreward = $db->getAssoc("SELECT `reward_id`, `list_id` FROM `bin_reward_member` WHERE `bin_id`={$Bbc->member['id']} AND `reward_id`={$data['id']} AND `received`=2 AND `active`=1");
	}else{
		$myreward = $db->getAssoc("SELECT `reward_id`, `received` FROM `bin_reward_member` WHERE `bin_id`={$Bbc->member['id']} AND `reward_id`={$data['id']} AND `active`=1");
	}
	if (empty($plan_a['reward_auto']))
	{
		$data['claim_id'] = !empty($myreward[$data['id']]) ? $myreward[$data['id']] : 0;
	}else{
		if (isset($myreward[$data['id']]))
		{
			$data['status']  = !empty($myreward[$data['id']]) ? lang('reward received') : lang('reward qualified');
			$data['cstatus'] = !empty($myreward[$data['id']]) ? 'text-success' : 'text-primary';
		}else{
			$data['status']  = lang('reward waiting');
			$data['cstatus'] = 'text-muted';
		}
	}
	$sys->nav_add($data['name']);
	meta_title(lang('Detail Reward').' '. $data['name'], 2);
	include 'reward_list_detail.html.php';
}else{
	echo msg('Maaf, Data Reward tidak ditemukan', 'danger');
}