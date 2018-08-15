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
	$r_type  = bin_bonus_list();
	$r_bonus = $db->getAll("SELECT * FROM `bin_bonus` WHERE `bin_id`={$id}");
	$tables  = array();
	$total   = 0;
	foreach ($r_bonus as $bonus)
	{
		if ($bonus['credit'])
		{
			$total -= $bonus['amount'];
		}else{
			$total += $bonus['amount'];
		}
		$tables[] = array(ucwords($r_type[$bonus['type_id']]['name']), money($bonus['amount']));
	}
	$tables[] = array('<b>Sisa Saldo</b>', '<b>'.money($total).'</b>');
	echo table($tables, array('Keterangan', 'Total (Rp.)'));
	if (!empty($plan_a['is_withdraw']) && !empty($plan_a['min_transfer']))
	{
		if ($Bbc->member['balance'] >= $plan_a['min_transfer'])
		{
			if (!empty($Bbc->member['active']))
			{
				?>
				<a href="index.php?mod=bin.bonus_status_withdraw" class="btn btn-default"><?php echo icon('fa-money').' '.lang('Tarik Dana'); ?></a>
				<?php
			}else{
				echo msg(lang('Maaf, saat ini anda tidak memiliki akses untuk menarik bonus anda'), 'danger');
			}
		}
	}
}