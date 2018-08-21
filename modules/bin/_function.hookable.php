<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
FUNCTION YANG PERTAMA DIPANGGIL KETIKA REGISTRASI MEMBER TELAH DIMASUKKAN
*/
function bin_check(&$config, &$current, &$sponsor, &$upline)
{
	foreach (user_modules() as $mod)
	{
		$func = $mod.'_'.__FUNCTION__;
		if (function_exists($func))
		{
			$func($config, $current, $sponsor, $upline);
		}
	}
}

/*
BONUS TITIK
*/
function bin_bonus_node($config, $new, $current, $upline, $level)
{
	$type_id = $level ? 6 : 3;
	if ($level)
	{
		$params = array(
			'username' => $current['username'],
			'level'    => $level
			);
	}else{
		$params = array(
			'username' => $new['username'],
			'position' => ($new['position'] ? 'kanan' : 'kiri')
			);
	}
	foreach (user_modules() as $mod)
	{
		$func = $mod.'_'.__FUNCTION__;
		if (function_exists($func))
		{
			$func($config, $new, $current, $upline, $level);
		}
	}
	if (!empty($config['bonus_node'][$level]))
	{
		bin_finance($upline['id'], $type_id, $config['bonus_node'][$level], $params);
	}
	if ($level==0)
	{
		if ($upline['sponsor_id']!=$upline['id'])
		{
			$sponsor = bin_fetch($upline['sponsor_id']);
			if (bin_bonus_gen_node_validate($config, $new, $upline, $sponsor, 1))
			{
				bin_bonus_gen_node($config, $new, $upline, $sponsor, 1);
			}
		}
	}
}
function bin_bonus_node_validate($config, $new, $current, $upline, $level)
{
	$out = false;
	if (!empty($config['bonus_node'][$level]) && $upline['active'])
	{
		$out = true;
	}
	if ($out)
	{
		$out = user_call_func_validate(__FUNCTION__, $config, $new, $current, $upline, $level);
	}
	return $out;
}

/*
BONUS GENERASI TITIK
*/
function bin_bonus_gen_node($config, $new, $current, $sponsor, $level)
{
	$type_id = 7;
	$params  = array(
		'username' => $current['username'],
		'level'    => $level
		);
	foreach (user_modules() as $mod)
	{
		$func = $mod.'_'.__FUNCTION__;
		if (function_exists($func))
		{
			$func($config, $new, $current, $sponsor, $level);
		}
	}
	if (!empty($config['bonus_gen_node'][$level]))
	{
		bin_finance($sponsor['id'], $type_id, $config['bonus_gen_node'][$level], $params);
	}
	if ($sponsor['sponsor_id']!=$sponsor['id'])
	{
		$level++;
		$upsponsor = bin_fetch($sponsor['sponsor_id']);
		if (bin_bonus_gen_node_validate($config, $new, $sponsor, $upsponsor, $level))
		{
			call_user_func_array(__FUNCTION__, [$config, $new, $sponsor, $upsponsor, $level]);
		}
	}
}
function bin_bonus_gen_node_validate($config, $new, $current, $sponsor, $level)
{
	$out = false;
	if (!empty($config['bonus_gen_node'][$level]) && $sponsor['active'])
	{
		$out = true;
	}
	if ($out)
	{
		$out = user_call_func_validate(__FUNCTION__, $config, $new, $current, $sponsor, $level);
	}
	return $out;
}

/*
BONUS PASANGAN
*/
function bin_bonus_pair($config, $new, $current, $upline, $level, $match2)
{
	$type_id = $level ? 9 : 5;
	if ($level)
	{
		$params = array(
			'username' => $current['username'],
			'level'    => $level
			);
	}else{
		$params = array(
			'match1' => $new['username'],
			'match2' => $match2
			);
	}
	foreach (user_modules() as $mod)
	{
		$func = $mod.'_'.__FUNCTION__;
		if (function_exists($func))
		{
			$func($config, $new, $current, $upline, $level, $match2);
		}
	}
	if (!empty($config['bonus_pair'][$level]))
	{
		bin_finance($upline['id'], $type_id, $config['bonus_pair'][$level], $params);
	}
	if ($upline['sponsor_id']!=$upline['id'])
	{
		$level++;
		$sponsor = bin_fetch($upline['sponsor_id']);
		if (bin_bonus_pair_validate($config, $new, $upline, $sponsor, $level))
		{
			call_user_func_array(__FUNCTION__, [$config, $new, $upline, $sponsor, $level, $match2]);
		}
	}
}
function bin_bonus_pair_validate($config, $new, $current, $upline, $level)
{
	$out = false;
	if (!empty($config['bonus_pair'][$level]) && $upline['active'])
	{
		$out = true;
	}
	if ($out)
	{
		$out = user_call_func_validate(__FUNCTION__, $config, $new, $current, $upline, $level);
	}
	return $out;
}


/*
BONUS SPONSOR
*/
function bin_bonus_sponsor($config, $new, $current, $sponsor, $level)
{
	$type_id = $level ? 8 : 4;
	if ($level)
	{
		$params = array(
			'username' => $current['username'],
			'level'    => $level
			);
	}else{
		$params = array(
			'username' => $new['username']
			);
	}
	foreach (user_modules() as $mod)
	{
		$func = $mod.'_'.__FUNCTION__;
		if (function_exists($func))
		{
			$func($config, $new, $current, $sponsor, $level);
		}
	}
	if (!empty($config['bonus_sponsor'][$level]))
	{
		bin_finance($sponsor['id'], $type_id, $config['bonus_sponsor'][$level], $params);
	}
}
function bin_bonus_sponsor_validate($config, $new, $current, $sponsor, $level)
{
	$out = false;
	if (!empty($config['bonus_sponsor'][$level]) && $sponsor['active'])
	{
		$out = true;
	}
	if ($out)
	{
		$out = user_call_func_validate(__FUNCTION__, $config, $new, $current, $sponsor, $level);
	}
	return $out;
}


/*
KETIKA PEMBAYARAN DISIMPAN KE DALAM DATABASE
$type_id: ID dari table `bin_balance_type`
$params: berisi array yang akan digunakan untuk me-replace text "[...]" sesuai dengan key di params
*/
function bin_finance($bin_id, $type_id, $amount, $params=array(), $time = '')
{
	foreach (user_modules() as $mod)
	{
		$func = $mod.'_'.__FUNCTION__;
		if (function_exists($func))
		{
			$func($bin_id, $type_id, $amount, $params);
		}
	}
	global $db, $sys;
	$day     = date('j');
	$month   = date('n');
	$year    = date('Y');
	$amount  = intval($amount);
	$total   = intval($db->getOne("SELECT `total` FROM `bin_finance` WHERE 1 ORDER BY `id` DESC LIMIT 1"));
	$type_id = intval($type_id);
	$q       = "SELECT * FROM `bin_balance_type` WHERE `id`={$type_id}";
	$type    = $db->getRow($q);
	if (empty($type))
	{
		return false;
	}
	$tpl = 'transaksi baru';
	if (!empty($type['message']))
	{
		$tpl = $type['message'];
	}else
	if (!empty($type['name']))
	{
		$tpl = $type['name'];
	}
	$title = $sys->text_replace($tpl, $params);
	if ($type['credit'])
	{
		$total -= $amount;
		$credit = 1;
	}else{
		$total += $amount;
		$credit = 0;
	}
	/* FINANCE TRANSACTION */
	// yang disimpan hanya aktifasi dan transfer
	$finance = $type['finance'] ? 1 : 0;
	if (!empty($finance))
	{
		$time = empty($time) ? 'NOW()' : "'{$time}'";
		$db->Execute("INSERT INTO `bin_finance` SET
		  `title`        = '{$title}',
		  `ondate`       = {$time},
		  `credit`       = {$credit},
		  `amount`       = {$amount},
		  `total`        = {$total},
		  `create_day`   = {$day},
		  `create_month` = {$month},
		  `create_year`  = {$year}
			 ");
	}
	/* FINANCE ALL */
	$d = $db->getRow("SELECT `id`, `amount` FROM `bin_finance_all` WHERE `type_id`={$type_id}");
	if (!empty($d))
	{
		$q = "UPDATE `bin_finance_all` SET `amount`=(`amount`+{$amount}) WHERE `id`=".$d['id'];
	}else{
		$q = "INSERT INTO `bin_finance_all` SET `type_id`={$type_id}, `credit`={$credit}, `finance`={$finance}, `amount`={$amount}";
	}
	$db->Execute($q);

	/* FINANCE MONTHLY */
	$d = $db->getRow("SELECT `id`, `amount` FROM `bin_finance_monthly` WHERE `type_id`={$type_id} AND `month`={$month} AND `year`={$year}");
	if (!empty($d))
	{
		$q = "UPDATE `bin_finance_monthly` SET `amount`=(`amount`+{$amount}) WHERE `id`=".$d['id'];
	}else{
		$q = "INSERT INTO `bin_finance_monthly` SET
	  `type_id` = {$type_id},
	  `credit`  = {$credit},
	  `finance` = {$finance},
	  `amount`  = {$amount},
	  `month`   = {$month},
	  `year`    = {$year} ";
	}
	$db->Execute($q);

	/* FINANCE DAILY */
	$d = $db->getRow("SELECT `id`, `amount` FROM `bin_finance_daily` WHERE `type_id`={$type_id} AND `day`={$day} AND `month`={$month} AND `year`={$year}");
	if (!empty($d))
	{
		$q = "UPDATE `bin_finance_daily` SET `amount`=(`amount`+{$amount}) WHERE `id`=".$d['id'];
	}else{
		$q = "INSERT INTO `bin_finance_daily` SET
	  `type_id` = {$type_id},
	  `credit`  = {$credit},
	  `finance` = {$finance},
	  `amount`  = {$amount},
	  `day`     = {$day},
	  `month`   = {$month},
	  `year`    = {$year} ";
	}
	$db->Execute($q);

	/* JIKA INI PENERIMAAN REWARD, MAKA HAPUS KUALIFIKASI REWARD */
	if ($type_id == 12)
	{
		$m  = date('n');
		$y  = date('Y');
		// Hapus finance monthly untuk kualifikasi reward
		$dt = $db->getRow("SELECT * FROM `bin_finance_monthly` WHERE `type_id`=11 AND `month`={$m} AND `year`={$y}");
		if (!empty($dt))
		{
			$db->Update('bin_finance_monthly', ['amount' => ($dt['amount']-$amount)], $dt['id']);
		}else{
			$db->Insert('bin_finance_monthly',[
				'type_id' => 11,
				'credit'  => 1,
				'finance' => 0,
				'amount'  => '-'.$amount,
				'month'   => $m,
				'year'    => $y
			]);
		}
		// Hapus finance All untuk kualifikasi reward
		$dt = $db->getRow("SELECT * FROM `bin_finance_all` WHERE `type_id`=11");
		if (!empty($dt))
		{
			$db->Update('bin_finance_all', ['amount' => ($dt['amount']-$amount)], $dt['id']);
		}else{
			$db->Insert('bin_finance_all',[
				'type_id' => 11,
				'credit'  => 1,
				'finance' => 0,
				'amount'  => '-'.$amount,
			]);
		}
	}

	if (!empty($type['balance']))
	{
		$member = bin_fetch_id($bin_id);
		if (!empty($member))
		{
			if ($finance)
			{
				$credit = $credit ? 0 : 1;
			}
			if ($credit)
			{
				$query = "UPDATE `bin` SET `balance`=(`balance`+{$amount}) WHERE `id`={$bin_id}";
				$mcredit = 0;
			}else{
				$query = "UPDATE `bin` SET `balance`=(`balance`-{$amount}) WHERE `id`={$bin_id}";
				$mcredit = 1;
			}
			$db->Execute($query);
			$total = $db->getOne("SELECT `balance` FROM `bin` WHERE `id`={$bin_id}");
			if (!is_numeric($total))
			{
				$total = intval($total);
			}
			$db->Execute("INSERT INTO `bin_balance` SET
			  `bin_id`       = {$bin_id},
			  `type_id`      = {$type_id},
			  `username`     = '{$member['username']}',
			  `title`        = '{$title}',
			  `ondate`       = NOW(),
			  `credit`       = {$mcredit},
			  `amount`       = {$amount},
			  `total`        = {$total},
			  `create_day`   = {$day},
			  `create_month` = {$month},
			  `create_year`  = {$year}
			  ");

			/* MEMBER BONUS ALL */
			$d = $db->getRow("SELECT `id`, `amount` FROM `bin_bonus` WHERE `bin_id`={$bin_id} AND `type_id`={$type_id}");
			if (!empty($d))
			{
				$q = "UPDATE `bin_bonus` SET `amount`=(`amount`+{$amount}) WHERE `id`=".$d['id'];
			}else{
				$q = "INSERT INTO `bin_bonus` SET `bin_id`={$bin_id}, `type_id`={$type_id}, `credit`={$mcredit}, `amount`={$amount}";
			}
			$db->Execute($q);

			/* MEMBER BONUS MONTHLY */
			$d = $db->getRow("SELECT `id`, `amount` FROM `bin_bonus_monthly` WHERE `bin_id`={$bin_id} AND `type_id`={$type_id} AND `month`={$month} AND `year`={$year}");
			if (!empty($d))
			{
				$q = "UPDATE `bin_bonus_monthly` SET `amount`=(`amount`+{$amount}) WHERE `id`=".$d['id'];
			}else{
				$q = "INSERT INTO `bin_bonus_monthly` SET
			  `bin_id`  = {$bin_id},
			  `type_id` = {$type_id},
			  `credit`  = {$mcredit},
			  `amount`  = {$amount},
			  `month`   = {$month},
			  `year`    = {$year} ";
			}
			$db->Execute($q);

			/* MEMBER BONUS DAILY */
			$d = $db->getRow("SELECT `id`, `amount` FROM `bin_bonus_daily` WHERE `bin_id`={$bin_id} AND `type_id`={$type_id} AND `day`={$day} AND `month`={$month} AND `year`={$year}");
			if (!empty($d))
			{
				$q = "UPDATE `bin_bonus_daily` SET `amount`=(`amount`+{$amount}) WHERE `id`=".$d['id'];
			}else{
				$q = "INSERT INTO `bin_bonus_daily` SET
			  `bin_id`  = {$bin_id},
			  `type_id` = {$type_id},
			  `credit`  = {$mcredit},
			  `amount`  = {$amount},
			  `day`     = {$day},
			  `month`   = {$month},
			  `year`    = {$year} ";
			}
			$db->Execute($q);
		}
	}
}


/*
MENENTUKAN LEVEL MEMBER
*/
function bin_level($config, $member)
{
	global $db;
	$level_id = $member['level_id'] + 1;
	foreach (user_modules() as $mod)
	{
		$func = $mod.'_'.__FUNCTION__;
		if (function_exists($func))
		{
			$func($config, $member);
		}
	}
	$db->Execute("UPDATE `bin` SET `level_id`={$level_id} WHERE `id`=".$member['id']);
}
function bin_level_validate($config, $member)
{
	if (count($config['level_list']) > $member['level_id'])
	{
		global $db;
		$level = $db->getRow("SELECT * FROM `bin_level` WHERE `id`=".($member['level_id']+1));
		if (!empty($level))
		{
			// check serial
			if (!empty($level['serial_type_id']))
			{
				$serial = $db->getRow("SELECT * FROM `bin_serial` WHERE `id`=".$member['serial_id']);
				if (!empty($serial))
				{
					if ($serial['type_id'] < $level['serial_type_id'])
					{
						return false;
					}
				}
			}
			// check total_sponsor && total_left && total_right
			if ($member['total_sponsor'] < $level['total_sponsor']
				|| $member['total_left'] < $level['total_left']
				|| $member['total_right'] < $level['total_right'] )
			{
				return false;
			}
		}
	}else{
		return false;
	}
	return user_call_func_validate(__FUNCTION__, $config, $member);
}


/*
MEMASUKKAN REWARD JIKA DIBUAT AUTO TANPA HARUS MENUNGGU KLAIM DARI MEMBER
*/
function bin_reward($config, $member)
{
	global $db;
	foreach (user_modules() as $mod)
	{
		$func = $mod.'_'.__FUNCTION__;
		if (function_exists($func))
		{
			$func($config, $member);
		}
	}
	// Reward terakhir yang pernah didapatkan
	$last_reward = $db->getRow("SELECT * FROM `bin_reward_member` WHERE `bin_id`={$member['id']} ORDER BY `list_id` DESC LIMIT 1");
	if (!empty($last_reward))
	{
		// Ambil reward di atasnya
		$reward = $db->getRow("SELECT * FROM `bin_reward` WHERE `active`=1 AND `id`>{$last_reward['reward_id']} ORDER BY `id` ASC LIMIT 1");
	}else{
		// Ambil reward pertama
		$reward = $db->getRow("SELECT * FROM `bin_reward` WHERE `active`=1 ORDER BY `id` ASC LIMIT 1");
	}
	// Jika ada reward yang tersedia
	if (!empty($reward))
	{
		// `received`==2 adalah potensi reward jika auto reward tidak aktif
		$received = !empty($config['reward_auto']) ? 0 : 2;
		/*
		Hapus potensi reward sebelumnya, karena jika dia punya potensi lebih dari satu
		maka kemungkinan dia akan ambil reward dengan nilai paling besar
		dan fungsi ini hanya digunakan untuk perkiraan keuangan perusahaan saja
		*/
		if ($received == 2)
		{
			$q = "DELETE FROM `bin_reward_member` WHERE `bin_id`={$member['id']} AND `received`=2";
			$db->Execute($q);
		}else{
			// Kualifikasi Reward
			bin_finance(
				$member['id'],
				11,
				$reward['amount'],
				array(
					'username' => $member['username'],
					'reward'   => $reward['name']
					)
			);
		}
		$q = "INSERT INTO `bin_reward_member` SET
			`user_id`       = {$member['user_id']},
			`bin_id`        = {$member['id']},
			`username`      = '{$member['username']}',
			`reward_id`     = {$reward['id']},
			`reward_name`   = '{$reward['name']}',
			`reward_amount` = '{$reward['amount']}',
			`total_sponsor` = '{$reward['total_sponsor']}',
			`total_left`    = '{$reward['total_left']}',
			`total_right`   = '{$reward['total_right']}',
			`accumulate`    = '{$reward['accumulate']}',
			`received`      = {$received}
			";
		$db->Execute($q);
	}
}
function bin_reward_validate($config, $member)
{
	if (!$member['active'])
	{
		return false;
	}
	global $db;
	$last_reward = $db->getRow("SELECT * FROM `bin_reward_member` WHERE `bin_id`={$member['id']} ORDER BY `list_id` DESC LIMIT 1");
	if (!empty($last_reward))
	{
		$reward = $db->getRow("SELECT * FROM `bin_reward` WHERE `active`=1 AND `id`>{$last_reward['reward_id']} ORDER BY `id` ASC LIMIT 1");
	}else{
		$reward = $db->getRow("SELECT * FROM `bin_reward` WHERE `active`=1 ORDER BY `id` ASC LIMIT 1");
	}
	if (!empty($reward))
	{
		// check serial
		if (!empty($reward['serial_type_id']))
		{
			$serial = $db->getRow("SELECT * FROM `bin_serial` WHERE `id`=".$member['serial_id']);
			if (!empty($serial))
			{
				if ($serial['type_id'] < $reward['serial_type_id'])
				{
					return false;
				}
			}
		}
		// check level
		if (!empty($reward['level_id']))
		{
			if ($member['level_id'] < $reward['level_id'])
			{
				return false;
			}
		}
		if (empty($reward['accumulate']))
		{
			// `received`==2 adalah potensi reward jika auto reward tidak aktif
			$q = "SELECT
				SUM(`total_sponsor`) AS `sponsor`,
				SUM(`total_left`) AS `left`,
				SUM(`total_right`) AS `right`
				FROM `bin_reward_member`
				WHERE `bin_id`={$member['id']} AND `accumulate`=0 AND `received` < 2";
			$use = $db->getRow($q);
			$member['total_sponsor'] -= intval($use['sponsor']);
			$member['total_left']    -= intval($use['left']);
			$member['total_right']   -= intval($use['right']);

		}
		// check total_sponsor && total_left && total_right
		if ($member['total_sponsor'] < $reward['total_sponsor']
			|| $member['total_left'] < $reward['total_left']
			|| $member['total_right'] < $reward['total_right'] )
		{
			return false;
		}
	}else{
		return false;
	}
	return user_call_func_validate(__FUNCTION__, $config, $member);
}