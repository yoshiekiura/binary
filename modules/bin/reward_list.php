<?php if (!defined('_VALID_BBC')) {exit('No direct script access allowed');}

if (!empty($plan_a['reward_use']))
{
	include 'reward_claim.php';
	$rewards  = $db->getAll('SELECT * FROM bin_reward WHERE `active`=1 ORDER BY id ASC');
	$levels   = $db->getAssoc("SELECT id, name FROM bin_level WHERE 1");
	$clevel   = count($levels);
	$serials  = $db->getAssoc("SELECT id, name FROM bin_serial_type WHERE 1");
	$cserial  = count($serials);
	$r_data   = array();
	$myreward = array();
	/* SIAPKAN POTENTIAL REWARD */
	if (empty($plan_a['reward_auto']))
	{
		$myreward = $db->getAssoc("SELECT `reward_id`, `list_id` FROM `bin_reward_member` WHERE `bin_id`={$Bbc->member['id']} AND `received`=2 AND `active`=1");
	}else{
		$myreward = $db->getAssoc("SELECT `reward_id`, `received` FROM `bin_reward_member` WHERE `bin_id`={$Bbc->member['id']} AND `active`=1");
	}
	foreach ($rewards as $reward)
	{
		$data                  = $reward;
		$data['link']          = site_url('bin/reward_list_detail/').$data['id'];
		$data['image']         = bin_reward_src($data['image'],$data['id']);
		$data['amount']        = money($data['amount']);
		$data['total_sponsor'] = money($data['total_sponsor']);
		$data['total_left']    = money($data['total_left']);
		$data['total_right']   = money($data['total_right']);
		$data['accumulate']    = $data['accumulate'] ? lang('Ya') : lang('Tidak');
		if ($cserial > 1)
		{
			$data['serial'] = $data['serial_type_id'] ? $serials[$data['serial_type_id']] : lang('any serial');
		}
		if ($clevel > 1)
		{
			$data['level'] = $data['level_id'] ? $levels[$data['level_id']] : lang('any level');
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
		$r_data[] = $data;
	}
	include tpl('reward_list.html.php');
}else{
	echo msg(lang('Maaf, jaringan ini tidak menyediakan reward'), 'danger');
}
