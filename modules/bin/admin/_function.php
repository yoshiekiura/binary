<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
UNTUK MEM-VERIFIKASI APAKAH VARIABLE $config SUDAH BENAR ATAUKAH TIDAK
*/
function bin_marketplan_validate($input)
{
	global $Bbc, $db;
	$output                = false;
	$Bbc->error_marketplan = '';
	if (isset($input['reward']) && is_array($input['reward']))
	{
		if (!empty($input['reward'])) // Jika tidak ada reward maka isi aray masih kosong dan tidak perlu di verifikasi
		{
			$reward = $input['reward'][0];
			$keys   = $db->getCol("SHOW FIELDS FROM `bin_reward`");
			foreach ($keys as $key)
			{
				if (!isset($reward[$key]))
				{
					$Bbc->error_marketplan = 'isi dari field "reward" tidak valid, mohon check ulang';
					return $output;
				}
			}
		}
		if (isset($input['level']) && is_array($input['level']))
		{
			if (!empty($input['level']))
			{
				$level = $input['level'][0];
				$keys   = $db->getCol("SHOW FIELDS FROM `bin_level`");
				foreach ($keys as $key)
				{
					if (!isset($level[$key]))
					{
						$Bbc->error_marketplan = 'isi dari field "level" tidak valid, mohon check ulang';
						return $output;;
					}
				}
			}
			if (isset($input['balance']) && is_array($input['balance']))
			{
				if (!empty($input['balance']))
				{
					$balance = $input['balance'][0];
					$keys   = $db->getCol("SHOW FIELDS FROM `bin_balance_type`");
					foreach ($keys as $key)
					{
						if (!isset($balance[$key]))
						{
							$Bbc->error_marketplan = 'isi dari field "balance" tidak valid, mohon check ulang';
							return $output;;
						}
					}
				}
				if (!empty($input['plan_a']))
				{
					$config  = $input['plan_a'];
					$default = array(
						'group_id'             => 3,
						'prefix'               => '',
						'price'                => 0,
						'serial_use'           => true,
						'serial_list'          => [],
						'serial_price'         => [],
						'serial_flushout'      => [],
						'serial_flushout_ok'   => 0,
						'serial_check'         => true,
						'is_withdraw'          => false,
						'min_transfer'         => 0,
						'surcharge'            => '0',
						'surcharge_npwp'       => '0',
						'surcharge_npwp_no'    => '0',
						'bonus_node'           => [],
						'bonus_gen_node'       => [],
						'bonus_sponsor'        => [],
						'bonus_sponsor_double' => false,
						'bonus_pair'           => [],
						'flushout_total'       => 0,
						'flushout_time'        => 0,
						'flushout_duration'    => '',
						'flushwait'            => 0,
						'flushwait_time'       => 0,
						'flushwait_duration'   => '',
						'reward_use'           => true,
						'reward_list'          => [],
						'reward_auto'          => true,
						'level_list'           => [],
						);
					$output = array();
					foreach ($default as $k => $dt)
					{
						if (is_string($dt))
						{
							if (preg_match('~^surcharge~s', $k))
							{
								$is_percent = preg_match('~\%$~s', $config[$k]);
								$output[$k] = preg_replace('~[^0-9]+~is', '', $config[$k]);
								if (empty($output[$k]))
								{
									$output[$k] = 0;
								}else{
									if ($is_percent)
									{
										$output[$k] .= '%';
									}else{
										$output[$k] = intval($output[$k]);
									}
								}
							}else{
								$output[$k] = $config[$k];
							}
						}else
						if (is_numeric($dt))
						{
							$output[$k] = @intval($config[$k]);
						}else
						if (is_bool($dt))
						{
							$output[$k] = !empty($config[$k]) ? 1 : 0;
						}else
						if (is_array($dt))
						{
							if (!empty($config[$k]) && is_array($config[$k]))
							{
								// convert string number into integer
								foreach ($config[$k] as $i => $dt)
								{
									if (is_numeric($dt))
									{
										$config[$k][$i] = intval($dt);
									}
								}
								$output[$k] = $config[$k];
							}else{
								$output[$k] = array();
							}
						}else{
							$Bbc->error_marketplan = 'isi dari field "plan_a" pada key "'.$k.'" tidak valid, mohon check ulang';
							return false;
						}
					}
					// Old Config
					if (empty($config['serial_name']) && !empty($config['serial_list']))
					{
						$config['serial_name'] = $config['serial_list'];
					}
					// pr($output, __FILE__.':'.__LINE__);die();
					// Single Price
					if (empty($output['serial_use']))
					{
						// No bonus pair
						if (empty($output['bonus_pair']))
						{
							$output['flushout_total']     = 0;
						}
						$output['serial_list']     = array('Reguler');
						$output['serial_price']    = array($output['price']);
						$output['serial_flushout'] = array($output['flushout_total']);
					}else{
						// Multi Price
						$output['serial_list']     = array();
						$output['serial_price']    = array();
						$output['serial_flushout'] = array();
						// No bonus pair
						if (empty($output['bonus_pair']))
						{
							$output['flushout_total'] = 0;
							foreach ($config['serial_name'] as $i => $v)
							{
								$output['serial_list'][]     = $v;
								$output['serial_price'][]    = intval(@$config['serial_price'][$i]);
								$output['serial_flushout'][] = 0;
							}
						}else{
							// Single flushout
							if (empty($output['serial_flushout_ok']))
							{
								foreach ($config['serial_name'] as $i => $v)
								{
									$output['serial_list'][]     = $v;
									$output['serial_price'][]    = intval(@$config['serial_price'][$i]);
									$output['serial_flushout'][] = $output['flushout_total'];
								}
							}else{
								// Multi flushout
								foreach ($config['serial_name'] as $i => $v)
								{
									$output['serial_list'][]     = $v;
									$output['serial_price'][]    = intval(@$config['serial_price'][$i]);
									$output['serial_flushout'][] = intval(@$config['serial_flushout'][$i]);;
								}
								$output['flushout_total'] = min($output['serial_flushout']);
							}
						}
					}
					$output['prefix'] = trim(strtoupper($output['prefix']));
					$output['price']  = min($output['serial_price']);
					$output           = array(
						'plan_a'  => $output,
						'reward'  => $input['reward'],
						'level'   => $input['level'],
						'balance' => $input['balance']
						);
				}
			}
		}
	}
	return $output;
}

/*
DIEKSEKUSI KETIKA KONFIGURASI MARKET PLAN BARU TELAH DI KONFIRMASI
Example $config value is in file ../config.sample.json
*/
function bin_marketplan_reset($input)
{
	$input = bin_marketplan_validate($input);
	if (!empty($input))
	{
		global $db;
		$config = $input['plan_a'];
		/* SAVE USER */
		$user = $db->getRow("SELECT * FROM `bbc_user` WHERE 1 ORDER BY `id` ASC LIMIT 1");
		if (empty($user))
		{
			$user = array(
				'id'          => 1,
				'group_ids'   => ',1,2,3,4,',
				'password'    => encode('123456'),
				'exp_checked' => '0000-00-00 00:00:00'
				);
		}
		$account = $db->getRow("SELECT * FROM `bbc_account` WHERE `user_id`={$user['id']} LIMIT 1");
		if (empty($account))
		{
			$account = array(
				'id'      => 1,
				'user_id' => 1,
				'name'    => 'Administrator',
				'image'   => '',
				'params'  => '{"Alamat Lengkap":"Indonesia","Phone":"0818550122"}'
				);
		}
		$del_tables = array(
			'bbc_account',
			'bbc_account_temp',
			'bbc_alert',
			'bbc_user',
			'bin',
			'bin_activation',
			'bin_balance',
			'bin_balance_type',
			'bin_bonus',
			'bin_bonus_daily',
			'bin_bonus_monthly',
			'bin_claim',
			'bin_field',
			'bin_finance',
			'bin_finance_all',
			'bin_finance_daily',
			'bin_finance_monthly',
			'bin_level',
			// 'bin_location',
			'bin_location_member',
			'bin_matching',
			'bin_message',
			'bin_reward',
			'bin_reward_member',
			'bin_serial',
			'bin_serial_type',
			'bin_testimonial'
			);
		foreach ($del_tables as $table)
		{
			// bin_marketplan_execute("DELETE FROM `{$table}` WHERE 1");
			bin_marketplan_execute("TRUNCATE TABLE `{$table}`");
			// bin_marketplan_execute("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
		}
		bin_marketplan_execute("DROP TABLE IF EXISTS `bbc_async`");
		bin_marketplan_execute("DROP TABLE IF EXISTS `bin_aaaaaaaaaa`");
		if (!empty($input['balance']))
		{
			/* MASUKAN DATA KE TABLE DARI $INPUT['BALANCE'] */
			$col = '`'.implode('`, `', $db->getCol("SHOW FIELDS FROM `bin_balance_type`")).'`';
			$q   = 'INSERT INTO `bin_balance_type` ('.$col.') VALUES ';
			$arr = array();
			foreach ($input['balance'] as $d)
			{
				$arr[] = "('".implode("', '", $d)."')";
			}
			$q .= implode(', ', $arr).';';
			bin_marketplan_execute($q);
		}else{
			bin_marketplan_execute("INSERT INTO `bin_balance_type` (`id`, `credit`, `finance`, `balance`, `name`, `description`, `message`, `active`) VALUES
			(1,0,1,1,'aktifasi member','aktifasi [username]','aktifasi [username]',1),
			(2,1,1,1,'pembayaran','pembayaran dari admin [bank_info]','pembayaran dari admin [bank_info]',1),
			(3,1,0,1,'bonus titik','bonus titik dari ID [username] di [position]','bonus titik dari ID [username] di [position]',0),
			(4,1,0,1,'bonus sponsor','bonus sponsor dari ID [username]','bonus sponsor dari ID [username]',1),
			(5,1,0,1,'bonus pasangan','bonus pasangan dari ID [match1] dan [match2] ','bonus pasangan dari ID [match1] dan [match2] ',1),
			(6,1,0,1,'bonus level titik','bonus level titik dari ID [username] di level [level]','bonus level titik dari ID [username] di level [level]',0),
			(7,1,0,1,'bonus generasi titik','bonus generasi titik dari ID [username] di level [level]','bonus generasi titik dari ID [username] di level [level]',0),
			(8,1,0,1,'bonus generasi sponsor','bonus generasi sponsor dari ID [username] di level [level]','bonus generasi sponsor dari ID [username] di level [level]',0),
			(9,1,0,1,'bonus generasi pasangan','Bonus generasi pasangan dari ID [username] di level [level]','Bonus generasi pasangan dari ID [username] di level [level]',0),
			(10,1,1,0,'biaya operasional','pengeluaran biaya untuk keperluan [operasional]','pengeluaran biaya untuk keperluan [operasional]',1),
			(11,1,0,0,'kualifikasi reward','ID [username] kualifikasi atas [reward]','ID [username] kualifikasi atas [reward]',1),
			(12,1,1,0,'penerimaan reward','ID [username] telah mendapatkan [reward]','ID [username] telah mendapatkan [reward]',1),
			(13,0,0,1,'Klaim Perusahaan','klaim perusahaan atas bonus dan reward dari [member]','klaim perusahaan atas bonus dan reward dari [member]',0)");
		}
		if (!empty($input['level']))
		{
			/* MASUKAN DATA KE TABLE DARI $INPUT['LEVEL'] */
			$col = '`'.implode('`, `', $db->getCol("SHOW FIELDS FROM `bin_level`")).'`';
			$q   = 'INSERT INTO `bin_level` ('.$col.') VALUES ';
			$arr = array();
			foreach ($input['level'] as $d)
			{
				$arr[] = "('".implode("', '", $d)."')";
			}
			$q .= implode(', ', $arr).';';
			bin_marketplan_execute($q);
		}else{
			if (empty($config['level_list']))
			{
				$config['level_list'] = array('Member');
			}
			$levels = $db->getAll("SELECT * FROM `bin_level` WHERE 1 ORDER BY `id` ASC");
			foreach ($config['level_list'] as $i => $level)
			{
				$j              = $i+1;
				$is_old_data    = ($level == @$levels[$i]['name']);
				$total_sponsor  = $is_old_data ? @$levels[$i]['total_sponsor'] : ($j*2);
				$total_left     = $is_old_data ? @$levels[$i]['total_left'] : $j;
				$total_right    = $is_old_data ? @$levels[$i]['total_right'] : $j;
				$serial_type_id = $is_old_data ? @$levels[$i]['serial_type_id'] : 0;
				bin_marketplan_execute("INSERT INTO `bin_level` SET
					`name`           = '{$level}',
					`total_sponsor`  = {$total_sponsor},
					`total_left`     = {$total_left},
					`total_right`    = {$total_right},
					`serial_type_id` = {$serial_type_id}
					");
			}
		}
		if (!empty($config['reward_use']))
		{
			if (!empty($input['reward']))
			{
				/* MASUKAN DATA KE TABLE DARI $INPUT['REWARD'] */
				$col = '`'.implode('`, `', $db->getCol("SHOW FIELDS FROM `bin_reward`")).'`';
				$q   = 'INSERT INTO `bin_reward` ('.$col.') VALUES ';
				$arr = array();
				foreach ($input['reward'] as $d)
				{
					$arr[] = "('".implode("', '", $d)."')";
				}
				$q .= implode(', ', $arr).';';
				bin_marketplan_execute($q);
			}else
			if(!empty($config['reward_list']))
			{
				$rewards = $db->getAll("SELECT * FROM `bin_reward` WHERE 1 ORDER BY `id` ASC");
				foreach ($config['reward_list'] as $i => $reward)
				{
					$j              = $i+1;
					$is_old_data    = ($reward == @$rewards[$i]['name']);
					$image          = $is_old_data ? $rewards[$i]['image'] : '';
					$images         = $is_old_data ? $rewards[$i]['images'] : '';
					$description    = $is_old_data ? $rewards[$i]['description'] : '';
					$amount         = $is_old_data ? $rewards[$i]['amount'] : 0;
					$total_sponsor  = $is_old_data ? $rewards[$i]['total_sponsor'] : ($j*2);
					$total_left     = $is_old_data ? $rewards[$i]['total_left'] : $j;
					$total_right    = $is_old_data ? $rewards[$i]['total_right'] : $j;
					$level_id       = $is_old_data ? $rewards[$i]['level_id'] : 0;
					$serial_type_id = $is_old_data ? $rewards[$i]['serial_type_id'] : 0;
					$accumulate     = $is_old_data ? $rewards[$i]['accumulate'] : 0;
					$active         = $is_old_data ? $rewards[$i]['active'] : 1;
					bin_marketplan_execute("INSERT INTO `bin_reward` SET
						`name`           = '{$reward}',
						`image`          = '{$image}',
						`images`         = '{$images}',
						`description`    = '{$description}',
						`amount`         = {$amount},
						`total_sponsor`  = {$total_sponsor},
						`total_left`     = {$total_left},
						`total_right`    = {$total_right},
						`level_id`       = {$level_id},
						`serial_type_id` = {$serial_type_id},
						`accumulate`     = {$accumulate},
						`active`         = {$active}
						");
				}
			}
		}
		if (empty($config['serial_use']))
		{
			$config['serial_use'] = 0;
		}
		if (empty($config['serial_list']))
		{
			$config['serial_list'] = array('Reguler');
			$config['serial_price'] = array($config['price']);
		}
		if (empty($config['serial_price']))
		{
			$config['serial_price'] = array($config['price']);
		}
		foreach ($config['serial_list'] as $i => $serial)
		{
			$j = $i+1;
			$p = @intval($config['serial_price'][$i]);
			$f = @intval($config['serial_flushout'][$i]);
			bin_marketplan_execute("INSERT INTO `bin_serial_type` SET `name`='{$serial}', `price`={$p}, `flushout`={$f}");
		}
		/* Recreate First User */
		$code     = $config['prefix'].'100001';
		$username = 'admin';//strtolower($code);
		bin_marketplan_execute("INSERT INTO `bbc_user` SET
			`group_ids`   = '{$user['group_ids']}',
			`username`    = '{$username}',
			`password`    = '{$user['password']}',
			`exp_checked` = '{$user['exp_checked']}',
			`login_time`  = 0,
			`created`     = NOW(),
			`active`      = 1");
		bin_marketplan_execute("INSERT INTO `bbc_account` SET
			`user_id`  = 1,
			`username` = '{$username}',
			`name`     = '{$account['name']}',
			`image`    = '{$account['image']}',
			`email`    = '".config('email', 'address')."',
			`params`   = '{$account['params']}'");
		bin_marketplan_execute("INSERT INTO `bin_serial` SET
			`code`         = '{$code}',
			`pin`          = '123456',
			`type_id`      = 1,
			`user_id`      = 1,
			`user_bin_id`  = 1,
			`user_date`    = NOW(),
			`buyer_id`     = 1,
			`buyer_bin_id` = 1,
			`buyer_date`   = NOW(),
			`created`      = NOW(),
			`expired`      = NOW(),
			`used`         = 1,
			`active`       = 1");
		bin_marketplan_execute("INSERT INTO `bin` SET
			`user_id`        = 1,
			`level_id`       = 1,
			`username`       = '{$code}',
			`name`           = '{$account['name']}',
			`upline_id`      = 1,
			`sponsor_id`     = 1,
			`total_downline` = 0,
			`total_left`     = 0,
			`total_right`    = 0,
			`total_sponsor`  = 0,
			`depth_upline`   = 0,
			`depth_sponsor`  = 0,
			`position`       = 0,
			`balance`        = 0,
			`serial_id`      = 1,
			`serial_pin`     = '123456',
			`serial_type_id` = 1,
			`location_id`    = 1,
			`location_name`  = 'Indonesia',
			`done`           = 1,
			`active`         = 1");
		bin_marketplan_execute("INSERT INTO `bin_location_member` SET
			`user_id`     = 1,
			`bin_id`      = 1,
			`location_id` = 1");

		/* AKTIFKAN / NONAKTIFKAN BONUS YANG TAK TERPAKAI */
		// Bonus titik
		$i = empty($config['bonus_node']) ? 0 : 1;
		bin_marketplan_execute("UPDATE `bin_balance_type` SET `active`={$i} WHERE `id`=3");
		// Bonus generasi titik
		$i = (!empty($config['bonus_node']) && count($config['bonus_node']) > 1) ? 1 : 0;
		bin_marketplan_execute("UPDATE `bin_balance_type` SET `active`={$i} WHERE `id`=7");
		// Bonus level titik
		$i = empty($config['bonus_gen_node']) ? 0 : 1;
		bin_marketplan_execute("UPDATE `bin_balance_type` SET `active`={$i} WHERE `id`=6");

		// Bonus sponsor
		$i = empty($config['bonus_sponsor']) ? 0 : 1;
		bin_marketplan_execute("UPDATE `bin_balance_type` SET `active`={$i} WHERE `id`=4");
		// Bonus generasi sponsor
		$i = empty($config['bonus_gen_node']) ? 0 : 1;
		bin_marketplan_execute("UPDATE `bin_balance_type` SET `active`={$i} WHERE `id`=8");

		// Bonus pasangan
		$i = empty($config['bonus_pair']) ? 0 : 1;
		bin_marketplan_execute("UPDATE `bin_balance_type` SET `active`={$i} WHERE `id`=5");
		// Bonus generasi pasangan
		$i = (!empty($config['bonus_pair']) && count($config['bonus_pair']) > 1) ? 1 : 0;
		bin_marketplan_execute("UPDATE `bin_balance_type` SET `active`={$i} WHERE `id`=9");

		if (!empty($config['reward_use']))
		{
			// kualifikasi reward
			bin_marketplan_execute("UPDATE `bin_balance_type` SET `active`=1 WHERE `id`=11");
			// penyerahan reward
			bin_marketplan_execute("UPDATE `bin_balance_type` SET `active`=1 WHERE `id`=12");
		}else{
			// kualifikasi reward
			bin_marketplan_execute("UPDATE `bin_balance_type` SET `active`=0 WHERE `id`=11");
			// penyerahan reward
			bin_marketplan_execute("UPDATE `bin_balance_type` SET `active`=0 WHERE `id`=12");
		}
		// Klaim Perusahaan
		bin_marketplan_execute("UPDATE `bin_balance_type` SET `active`=0 WHERE `id`=13");
		return array(
			'plan_a'  => $config,
			'reward'  => $input['reward'],
			'level'   => $input['level'],
			'balance' => $input['balance']
			);
	}else{
		return false;
	}
}
function bin_marketplan_execute($value='')
{
	global $Bbc, $db;
	if (empty($Bbc->bin_execute))
	{
		$Bbc->bin_execute = array();
	}
	$q = preg_replace('~\s+~is', ' ', $value);
	$Bbc->bin_execute[] = $q;
}

/*
DIGUNAKAN UNTUK MELIHAT FUNCTION HOOKABLE DI MODULE LAIN BESERTA COMMENT MARK NYA
*/
function bin_check_func($func)
{
	$path   = _ROOT.'modules/';
	$mods   = user_modules();
	$output = array();
	if (!preg_match('~_validate$~is', $func))
	{
		$func2 = $func.'_validate';
		foreach ($mods as $mod)
		{
			if ($mod!='bin' && function_exists($mod.'_'.$func2))
			{
				$output[$mod]['validasi'] = $mod.'_'.$func2.'()';
				if(preg_match('~(?:/\*\*?(.*?)\*/\s{0,}\n+\s{0,})?function\s{0,}'.$mod.'_'.$func2.'\s{0,}\(~is', file_read($path.$mod.'/_function.php'), $match))
				{
					$output[$mod]['validasi'] = @trim($match[1]);
				}
			}
		}
	}
	foreach ($mods as $mod)
	{
		if ($mod!='bin' && function_exists($mod.'_'.$func))
		{
			$key = 'eksekusi';
			if (preg_match('~^bin_([^_]+)~is', $func, $m))
			{
				$key = $m[1];
			}
			$output[$mod][$key] = $mod.'_'.$func.'()';
			if(preg_match('~(?:/\*\*?(.*?)\*/\s{0,}\n+\s{0,})?function\s{0,}'.$mod.'_'.$func.'\s{0,}\(~is', file_read($path.$mod.'/_function.php'), $match))
			{
				$output[$mod][$key] = @trim($match[1]);
			}
		}
	}
	if (!empty($output))
	{
		$text = '<ul class="list-group">';
		foreach ($output as $module => $values)
		{
			$text .= '<li class="list-group-item list-group-item"> <h4 class="list-group-item-heading">'.$module.'</h4>';
			$text .= '<ul class="list-group">';
			foreach ($values as $key => $msg)
			{
				$cls = $key=='validasi' ? 'warning' : 'success';
				$text .= '<li class="list-group-item list-group-item-'.$cls.'">'.$key.': '.$msg.'</li>';
			}
			$text .= '</li>';
		}
		$text .= '</ul>';
		$output = '<div class="help-block">'.nl2br($text).'</div>';
	}else{
		$output = '';
	}
	return $output;
}

/*
DIGUNAKAN UNTUK MEMBUAT MEMBER SECARA OTOMATIS
*/
function bin_create_member($sponsor='', $upline='', $type_id=1, $position='')
{
	global $db, $_CONFIG;
	get_config('bin');
	$output = array(
		'ok'      => false,
		'serial'  => '',
		'user_id' => 0,
		'message' => ''
		);
	$_CONFIG['plan_a']['serial_check'] = 0;
	// ambil member terdalam untuk upline
	if (empty($upline))
	{
		$upline = $db->getRow("SELECT * FROM `bin` WHERE `active`=1 AND `total_downline` < 2 ORDER BY `depth_upline` ASC, `id` ASC LIMIT 1");
	}

	// ambil upline dari member terdalam untuk sponsor
	if (empty($sponsor))
	{
		$sponsor = $db->getRow("SELECT * FROM `bin` WHERE `id`={$upline['upline_id']} LIMIT 1");
	}

	// check apakah ada serial untuk dipakai
	$serial = $db->getRow("SELECT * FROM `bin_serial` WHERE `active`=1 AND `used`=0 AND `type_id`={$type_id} ORDER BY `id` ASC LIMIT 1");

	// buat serial jika tidak ada
	if (empty($serial))
	{
		$q = "INSERT INTO `bin_serial` SET
			`code`    = '".rand()."',
			`pin`     = '".substr(rand(), 0, 6)."',
			`type_id` = ".$type_id.",
			`used`    = 0,
			`active`  = 1
			";
		if ($db->Execute($q))
		{
			$ai   = $db->Insert_ID();
			$idx  = 100000+$ai;
			$code = config('plan_a', 'prefix').$idx;
			$db->Execute("UPDATE `bin_serial` SET `code`='{$code}' WHERE `id`={$ai}");

			$serial         = $db->getRow("SELECT * FROM `bin_serial` WHERE `active`=1 AND `used`=0 AND `type_id`={$type_id} ORDER BY `id` ASC LIMIT 1");
			$serial['code'] = $code; // untuk menjaga delay dari mysql
		}
	}
	$idx = 100000+intval($serial['id']);

	// gunakan serial (hasil temuan atau hasil buatan)
	if (!empty($serial))
	{
		// buat params user
		/*
		{
			"username": "gs100002",
			"password": "123456",
			"name": "member 100002",
			"email": "gs100002@bin.com",
			"params": {
				"serial": "GS100002",
				"pin": "463358",
				"sponsor": "GS100001",
				"upline": "GS100001",
				"position": 0,
				"location_id": 1,
				"No. KTP": "3319010100002",
				"Rekening Bank": "BCA",
				"No. Rekening": "1354800100002",
				"Phone": "081100002",
				"location_address": "",
				"location_latlong": ""
			},
			"group_ids": "3"
		}
		*/
		$config   = config('bin_fields');
		$position = is_numeric($position) ? $position : (empty($upline['total_left']) ? 0 : 1);
		$params   = array(
			'username' => strtolower($serial['code']),
			'password' => '123456',
			'name'     => 'member '.$idx,
			'email'    => strtolower($serial['code'].'@'.config('site', 'url')),
			'params'   => array(
				'serial'      => $serial['code'],
				'pin'         => $serial['pin'],
				'sponsor'     => $sponsor['username'],
				'upline'      => $upline['username'],
				'position'    => $position,
				'location_id' => 1
				),
			'group_ids' => get_config('bin', 'plan_a', 'group_id')
			);
		foreach ($config['fields'] as $field => $field_id)
		{
			if (!isset($params['params'][$field]))
			{
				if (!empty($config['limit'][$field]))
				{
					switch (strtolower($field))
					{
						case 'phone':
							$pre = '081';
							break;
						case 'no. ktp':
							$pre = '3319010';
							break;
						default:
							$pre = '1354800';
							break;
					}
					$params['params'][$field] = $pre.$idx;
				}else{
					switch (strtolower($field))
					{
						case 'rekening bank':
							$pre = 'BCA';
							break;
						case 'alamat lengkap':
							$pre = 'Kidul mesjid kulon kali';
							break;
						default:
							$pre = '';
							break;
					}
					$params['params'][$field] = $pre;
				}
			}
		}
		$user_id = 0;
		$user_id = user_create($params);
		if ($user_id > 0)
		{
			$output = array(
				'ok'      => true,
				'serial'  => $serial['code'],
				'user_id' => $user_id,
				'message' => ''
				);
		}else{
			$output['message'] = user_create_validate_msg();
		}
	}
	return $output;
}
