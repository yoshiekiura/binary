<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($_POST['claim_id']))
{
	$claim_id = intval($_POST['claim_id']);
	$claim    = $db->getRow("SELECT * FROM `bin_reward_member` WHERE list_id={$claim_id} AND `active`=1");
	if (!empty($claim) && $claim['bin_id']==$Bbc->member['id'])
	{
		$db->Update('bin_reward_member', ['received'=>0], 'list_id='.$claim['list_id']);
		bin_finance(
			$claim['bin_id'],
			11,
			$claim['reward_amount'],
			array(
				'username' => $Bbc->member['username'],
				'reward'   => $claim['reward_name']
				)
		);
		echo msg(lang('Klaim reward telah di simpan, silahkan menunggu'), 'success');
	}
}
