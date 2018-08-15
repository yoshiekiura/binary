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
	$types = bin_bonus_list();
	// Buat Kolom Table
	$coloms = array(lang('Tipe'));
	$dates  = array();

	for ($i=0; $i < 7; $i++)
	{
		$date     = date('Y-m-d', strtotime('-'.$i.' DAYS'));
		$dates[]  = $date;
		$coloms[] = date("D d/m/y", strtotime($date));
	}
	$rows  = array();
	$last  = array('Total');
	$total = array();
	$bonus = array();


	$q = "SELECT `type_id`, `create_day` AS `day`, SUM(`amount`) AS `amount`
				FROM `bin_balance`
				WHERE `bin_id` = {$id}
				AND `ondate`  >= ".end($dates)."
				GROUP BY type_id
				";
	$r = $db->getAll($q);
	foreach ($r as $d)
	{
		$bonus[$d['type_id']][$d['day']] = $d['amount'];
	}

	foreach ($types as $type_id => $type)
	{
		$row = array($type['name']);
		foreach ($dates as $date)
		{
			list($year, $month, $day) = array_map('intval', explode('-', $date));
			$amount = 0;
			if (!empty($bonus[$type_id][$day]))
			{
				$amount = $bonus[$type_id][$day];
			}
			$row[]   = money($amount);
			if (!isset($total[$date]))
			{
				$total[$date] = 0;
			}
			$total[$date] += $amount;
		}
		$rows[] = $row;
	}
	foreach ($total as $amount)
	{
		$last[] = money($amount);
	}
	$rows[] = $last;
	?>
	<div class="table-responsive"><?php echo table($rows, $coloms); ?></div>
	<?php
}
