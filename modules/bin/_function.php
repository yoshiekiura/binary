<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

require_once __DIR__.'/_function.hookable.php';
/*
DIPANGGIL UNTUK SCRAWLING DARI BAWAH KE ATAS SAMPAI ROOT SETELAH MASUK DATABASE
*/
function bin_up($config, $current, $sponsor, $upline)
{
	global $db;
	bin_check($config, $current, $sponsor, $upline);
	bin_path_create($current['id'], $current['username']);
	/* HAPUS bin_matching YANG SUDAH EXPIRE UNTUK MERINGANKAN PROSES */
	$del_sql = '';
	if (!empty($config['flushwait']))
	{
		$del_sql = '`created`<\''.date('Y-m-d H:i:s', strtotime('-'.$config['flushwait_time'].' '.$config['flushwait_duration']))."'";
	}
	$delimiter = !empty($del_sql) ? ['(', ')'] : ['', ''];
	$del_sql  .= !empty($del_sql) ? ' OR ' : '';
	$del_sql  .= $delimiter[0].'`created`<\''.date('Y-m-d H:i:s', strtotime('-'.$config['flushout_time'].' '.$config['flushout_duration'])).'\' AND `paired` > 0'.$delimiter[1];
	$db->Execute("DELETE FROM `bin_matching` WHERE {$del_sql}");

	/* MASUKKAN KE PENDAPATAN PERUSAHAAN */
	bin_finance(0, 1, $config['price'], $current);

	if ($current['id']!=$upline['id'])
	{
		bin_up_node($config, $current, $current, $upline);
	}
}
/*
SCRAWLING DARI REGISTRANT KE ROOT BERDASARKAN JALUR TITIK
membuat file text untuk jaringan titik, menentukan bonus titik, menentukan bonus generasi titik jika ada
serta menentukan bonus pasangan jika masih tersedia atau menyimpannya sebagai titik menunggu untuk dipasangkan
$level == di level keberapa function ini dieksekusi (ditambahkan jika hanya function berhasil di eksekusi)
$next == sudah brp kali function ini dieksekusi dalam satu kali registrasi
*/
function bin_up_node($config, $new, $current, $upline, $level = 0, $next = 0)
{
	global $db;
	$tasking = explode('|', $new['tasking']);
	$paused  = @intval($tasking[3]);
	$newtask = true;
	if (!bin_isDownline($new['id'], $upline['id']))
	{
		// create downline.txt
		file_write(bin_path($upline['id'], 'downline'), $new['username'].':'.$new['id']."\n", 'a+');

		// create downline_(left/right).txt
		$position = $current['position'] ? 'right' : 'left';
		$sqltotal = 'total_'.$position;
		$sqldepth = 'depth_'.$position;
		$sqladd   = '';
		file_write(bin_path($upline['id'], 'downline_'.$position), $new['username'].':'.$new['id']."\n", 'a+');

		// on the first call
		if ($next==0)
		{
			// create upline.txt
			@copy(bin_path($upline['id'], 'upline'), bin_path($new['id'], 'upline'));
			file_write(bin_path($new['id'], 'upline'), $upline['username'].':'.$upline['id']."\n", 'a+');

			// Add Depth
			$new['add_depth'] = 1; // tandai kalo proses ini menambah kedalaman
		}else{
			if ($current[$sqldepth] >= $upline[$sqldepth])
			{
				$new['add_depth'] = 1;
			}else{
				$new['add_depth'] = 0;
			}
		}
		if (!empty($new['add_depth']))
		{
			$j = $upline[$sqldepth]+1;
			$sqladd = ", `{$sqldepth}`={$j}";
		}

		// Update upline database
		$q = "UPDATE `bin` SET `total_downline`=(`total_downline`+1), `{$sqltotal}`=(`{$sqltotal}`+1) {$sqladd} WHERE `id`=".$upline['id'];
		if ($db->Execute($q))
		{
			$upline[$sqltotal]++;
			$upline['total_downline']++;
			if (!empty($new['add_depth']))
			{
				$upline[$sqldepth]++;
			}
		}

		// Masukkan bonus titik
		if (bin_bonus_node_validate($config, $new, $current, $upline, $level))
		{
			bin_bonus_node($config, $new, $current, $upline, $level);
			$level++;
		}

		/* PROSES BONUS PASANGAN */
		$day       = date('j');
		$month     = date('n');
		$year      = date('Y');
		$serial_id = $upline['serial_type_id']-1;
		$flushout  = !empty($config['serial_flushout'][$serial_id]) ? $config['serial_flushout'][$serial_id] : $config['flushout_total'];
		$Query     = '';
		// check apakah ada titik menunggu
		$r_match = $db->getAll("SELECT * FROM `bin_matching` WHERE `bin_id`=".$upline['id']." ORDER BY `id` ASC");
		if (!empty($r_match))
		{
			$matches = 0;
			$waiting = array(0, 0);
			$fmatch  = array();
			foreach ($r_match as $match)
			{
				switch ($match['paired'])
				{
					case '1': // matches
						$matches++;
						break;
					case '0': // waiting
						$waiting[$match['position']]++;
						if (empty($fmatch))
						{
							$fmatch = $match;
						}
						break;
					default: // flushout
						break;
				}
			}
			// Jika sudah melewati flushout maka pasangkan tapi dengan kondisi flushout
			if ($matches >= $flushout)
			{
				if (!empty($fmatch['id']))
				{
					$Query = "UPDATE `bin_matching` SET `paired`=2, `pair_bin_id`={$new['id']} WHERE `id`=".$fmatch['id'];
				}
			}else{
				// Masukkan bonus pasangan jika ada titik menunggu
				$new_position = $current['position'] ? 0 : 1;
				if ($waiting[$new_position] > 0)
				{
					if (bin_bonus_pair_validate($config, $new, $current, $upline, 0))
					{
						$member = bin_fetch_id($fmatch['wait_bin_id']);
						$db->Execute("UPDATE `bin_matching` SET `paired`=1, `pair_bin_id`={$new['id']} WHERE `id`=".$fmatch['id']);
						bin_bonus_pair($config, $new, $current, $upline, 0, $member['username']);
					}
				}else{
					$Query = "INSERT INTO `bin_matching` SET
						`bin_id`       = {$upline['id']},
						`wait_bin_id`  = {$new['id']},
						`pair_bin_id`  = 0,
						`paired`       = 0,
						`position`     = {$current['position']},
						`create_day`   = {$day},
						`create_month` = {$month},
						`create_year`  = {$year}
						";
				}
			}
		}else{
			$Query = "INSERT INTO `bin_matching` SET
				`bin_id`       = {$upline['id']},
				`wait_bin_id`  = {$new['id']},
				`pair_bin_id`  = 0,
				`paired`       = 0,
				`position`     = {$current['position']},
				`create_day`   = {$day},
				`create_month` = {$month},
				`create_year`  = {$year}
				";
		}
		if (!empty($Query))
		{
			$db->Execute($Query);
		}
	}
	$next++;
	// recall function
	$upupline = bin_fetch_id($upline['upline_id']);
	if (!empty($upupline) && $upupline['id']!=$upline['id'])
	{
		if ($newtask)
		{
			$new['tasking'] = __FUNCTION__.'|'.$new['id'].'|'.$upline['id'].'|'.$upupline['id'].'|'.$level.'|'.$next;
			$db->Execute("UPDATE `bin` SET `tasking`='{$new['tasking']}' WHERE `id`=".$new['id']);
		}
		_class('async')->run(__FUNCTION__, [$config, $new, $upline, $upupline, $level, $next]);
	}else{
		if ($new['sponsor_id']!=$new['id'])
		{
			$sponsor = bin_fetch_id($new['sponsor_id']);
			bin_up_sponsor($config, $new, $new, $sponsor);
		}else{
			$db->Execute("UPDATE `bin` SET `tasking`='', `done`=1 WHERE `id`=".$new['id']);
		}
	}
}
/*
SCRAWLING DARI REGISTRANT KE ROOT BERDASARKAN JALUR SPONSOR
membuat file text untuk jaringan sponsor serta menentukan bonus pasangan dan generasi pasangan jika ada
$level == di level keberapa function ini dieksekusi (ditambahkan jika hanya function berhasil di eksekusi)
$next == sudah brp kali function ini dieksekusi dalam satu kali registrasi
*/
function bin_up_sponsor($config, $new, $current, $sponsor, $level = 0, $next = 0)
{
	global $db;
	$tasking = explode('|', $new['tasking']);
	$paused  = @intval($tasking[3]);
	$newtask = true;
	if (!bin_isDownsponsor($new['id'], $sponsor['id']))
	{
		// $db->Execute("START TRANSACTION");

		// create downsponsor.txt
		file_write(bin_path($sponsor['id'], 'downsponsor'), $new['username'].':'.$new['id']."\n", 'a+');
		if ($next==0)
		{
			// create upsponsor.txt
			@copy(bin_path($sponsor['id'], 'upsponsor'), bin_path($new['id'], 'upsponsor'));
			file_write(bin_path($new['id'], 'upsponsor'), $sponsor['username'].':'.$sponsor['id']."\n", 'a+');

			// Update sponsor database
			$q = "UPDATE `bin` SET `total_sponsor`=(`total_sponsor`+1) WHERE `id`=".$sponsor['id'];
			if($db->Execute($q))
			{
				$sponsor['total_sponsor']++;
			}
		}

		// Masukkan bonus sponsor
		if (bin_bonus_sponsor_validate($config, $new, $current, $sponsor, $level))
		{
			bin_bonus_sponsor($config, $new, $current, $sponsor, $level);
			$level++;
		}
		// $db->Execute("COMMIT");
	}
	$next++;
	// jika bonus generasi sponsor level 1 di dapat oleh sponsor pembawa member itu sendiri
	if ($level==1 && !empty($config['bonus_sponsor_double']))
	{
		$upsponsor = $sponsor;
	}else{
		$upsponsor = bin_fetch_id($sponsor['sponsor_id']);
		if ($upsponsor['id'] == $sponsor['id'])
		{
			unset($upsponsor);
		}
	}
	// recall function
	if (!empty($upsponsor))
	{
		if ($newtask)
		{
			$new['tasking'] = __FUNCTION__.'|'.$new['id'].'|'.$sponsor['id'].'|'.$upsponsor['id'].'|'.$level.'|'.$next;
			$db->Execute("UPDATE `bin` SET `tasking`='{$new['tasking']}' WHERE `id`=".$new['id']);
		}
		_class('async')->run(__FUNCTION__, [$config, $new, $sponsor, $upsponsor, $level, $next]);
	}else{
		bin_up_reward($config, $new, $new);
	}
}
/*
DIGUNAKAN UNTUK MENENTUKAN REWARD DAN LEVEL JIKA TERSEDIA DI config('plan_a');
*/
function bin_up_reward($config, $new, $member, $next=0)
{
	global $db;
	$tasking   = explode('|', $new['tasking']);
	$paused    = @intval($tasking[2]);
	$is_recall = false;
	$newtask   = false;

	if (($paused <= $member['id'] && @$tasking[0]==__FUNCTION__) || @$tasking[0]!=__FUNCTION__)
	{
		$newtask = true;
		if (count($config['level_list']) > 1)
		{
			$is_recall = true;
			if (bin_level_validate($config, $member))
			{
				bin_level($config, $member);
			}
		}
		if ($config['reward_use']==1)
		{
			$is_recall = true;
			if (bin_reward_validate($config, $member))
			{
				bin_reward($config, $member);
			}
		}
	}
	$next++;
	if ($is_recall)
	{
		$upline = bin_fetch_id($member['upline_id']);
		if (!empty($upline) && $upline['id']!=$member['id'])
		{
			if ($newtask)
			{
				$new['tasking'] = __FUNCTION__.'|'.$new['id'].'|'.$upline['id'].'|0|0|'.$next;
				$db->Execute("UPDATE `bin` SET `tasking`='{$new['tasking']}' WHERE `id`=".$new['id']);
			}
			_class('async')->run(__FUNCTION__, [$config, $new, $upline, $next]);
		}else{
			$db->Execute("UPDATE `bin` SET `tasking`='', `done`=1 WHERE `id`=".$new['id']);
		}
	}else{
		$db->Execute("UPDATE `bin` SET `tasking`='', `done`=1 WHERE `id`=".$new['id']);
	}
}
/*
DIPANGGIL JIKA ADA USER YANG DIBUAT DAN DIMASUKKAN KE DALAM USER GROUP YANG SAMA DI config('plan_a', 'group_id');
*/
function bin_user_create($user_id)
{
	/* AMBIL DATA LALU PANGGIL bin_register() */
	global $db;
	get_config('bin');
	$user    = $db->getRow("SELECT * FROM `bbc_user` WHERE `id`={$user_id}");
	$account = $db->getRow("SELECT * FROM `bbc_account` WHERE `user_id`={$user_id}");
	$params  = config_decode($account['params']);
	$g_ids   = repairExplode($user['group_ids']);
	$do_next = in_array(config('plan_a', 'group_id'), $g_ids);
	if ($do_next)
	{
		$serial   = $db->getRow("SELECT * FROM `bin_serial` WHERE `code`='".$params['serial']."'");
		$sponsor  = bin_fetch_username($params['sponsor']);
		$upline   = bin_fetch_username($params['upline']);
		$location = $db->getRow("SELECT * FROM `bin_location` WHERE `id`=".intval($params['location_id']));
		if (empty($params['location_address']))
		{
			if (!empty($params['Alamat Lengkap']))
			{
				$params['location_address'] = $params['Alamat Lengkap'];
			}
		}
		if (empty($location['detail']) && !empty($location['title']))
		{
			$location['detail'] = $location['title'];
		}
		$q = "INSERT INTO `bin` SET
			`user_id`          = '".$user['id']."',
			`level_id`         = 1,
			`username`         = '".strtoupper($user['username'])."',
			`name`             = '".$account['name']."',
			`upline_id`        = '".$upline['id']."',
			`sponsor_id`       = '".$sponsor['id']."',
			`total_downline`   = 0,
			`total_left`       = 0,
			`depth_left`       = 0,
			`total_right`      = 0,
			`depth_right`      = 0,
			`total_sponsor`    = 0,
			`depth_upline`     = '".($upline['depth_upline']+1)."',
			`depth_sponsor`    = '".($sponsor['depth_sponsor']+1)."',
			`position`         = '".$params['position']."',
			`balance`          = 0,
			`serial_id`        = '".$serial['id']."',
			`serial_pin`       = '".$serial['pin']."',
			`serial_type_id`   = '".$serial['type_id']."',
			`location_id`      = '".$location['id']."',
			`location_name`    = '".$location['detail']."',
			`location_address` = '".@$params['location_address']."',
			`location_latlong` = '".@$params['location_latlong']."',
			`bank_name`        = '".@$params['Rekening Bank']."',
			`bank_no`          = '".@$params['No. Rekening']."',
			`tasking`          = '',
			`done`             = 0,
			`created`          = NOW(),
			`active`           = 1";
		if ($db->Execute($q))
		{
			$bin_id = $db->Insert_ID();
			bin_user_create_location($bin_id, $user_id, $location['id']);
			if (config('plan_a', 'serial_check')!='1' || $serial['buyer_date']=='0000-00-00 00:00:00')
			{
				$serial['buyer_date'] = date('Y-m-d H:i:s');
			}
			$q = "UPDATE `bin_serial` SET
				`user_id`      = '".$user_id."',
				`user_bin_id`  = '".$bin_id."',
				`user_date`    = NOW(),
				`buyer_id`     = '".$sponsor['user_id']."',
				`buyer_bin_id` = '".$sponsor['id']."',
				`buyer_date`   = '".$serial['buyer_date']."',
				`used`         = 1
				WHERE `id`=".$serial['id'];
			if ($db->Execute($q))
			{
				/* LIMIT MEMBER FIELDS */
				$cfg = config('bin_fields');
				if (!empty($cfg['limit']) && is_array($cfg['limit']))
				{
					foreach ($cfg['limit'] as $field => $limit)
					{
						if ($limit > 0)
						{
							$field_id = @intval($cfg['fields'][$field]);
							if (!empty($field_id))
							{
								$db->Execute("INSERT INTO `bin_field` SET `bin_id`={$bin_id}, `field_id`={$field_id}, `field_value`='{$params[$field]}'");
							}
						}
					}
				}

				/* UBAH PASSWORD */
				$db->Execute("UPDATE `bbc_user` SET `password`='".encode($serial['pin'])."' WHERE `id`={$user_id}");

				/* UNTUK LAPORAN JUMLAH AKTIFASI HARIAN */
				$date = date('Y-m-d');
				list($year, $month, $day) = explode('-', $date);

				$month  = intval($month);
				$day    = intval($day);
				$report = $db->getRow("SELECT `id`, `total` FROM `bin_activation` WHERE `create_day`={$day} AND `create_month`={$month} AND `create_year`={$year} ORDER BY `id` LIMIT 1");
				if (empty($report))
				{
					$q = "INSERT INTO `bin_activation` SET `total`=1, `ondate`='{$date}', `create_day`={$day}, `create_month`={$month}, `create_year`={$year}";
				}else{
					$total = $report['total']+1;
					$q = "UPDATE `bin_activation` SET `total`={$total} WHERE `id`=".$report['id'];
				}
				$db->Execute($q);

				/* EKSEKUSI FUNCTION SCRAWL UNTUK JARINGAN */
				$cfg          = config('plan_a');
				$member       = bin_fetch_id($bin_id);
				$cfg['price'] = $db->getOne("SELECT `price` FROM `bin_serial_type` WHERE `id`=".$serial['type_id']);
				bin_up($cfg, $member, $sponsor, $upline);
			}
		}
	}

}
/*
DIPANGGIL SETELAH MENG-EKSEKUSI bin_user_create()
untuk menyimpan data lokasi member
*/
function bin_user_create_location($bin_id, $user_id, $location_id)
{
	if ($location_id > 0)
	{
		global $db;
		$db->Execute("INSERT INTO `bin_location_member` SET `user_id`= {$user_id}, `bin_id` = {$bin_id}, `location_id` = {$location_id}");
		$par_id = $db->getOne("SELECT `par_id` FROM `bin_location` WHERE `id`={$location_id}");
		call_user_func(__FUNCTION__, $bin_id, $user_id, $par_id);
	}
}
/*
DIPANGGIL UNTUK VALIDASI DATA KETIKA ADA MEMBER YANG REGISTER SEBELUM DIMASUKKAN KE DALAM DATABASE
$data = array(
	'username'	=> 'username',
	'password'	=> '123456',
	'name'			=> 'Mr. Nice Guy',
	'email'			=> 'username@website.com',
	'params'		=> array(),// depends on user_field();
	'group_ids'	=> [2,1,4]
	);
return boolean;
*/
function bin_user_create_validate($data, $check_serial = true)
{
	global $db;
	get_config('bin');
	if (in_array(config('plan_a', 'group_id'), $data['group_ids']))
	{
		$mandatory_fields = ['serial', 'pin', 'sponsor', 'upline', 'position', 'location_id'];
		foreach ($mandatory_fields as $field)
		{
			if (!isset($data['params'][$field]))
			{
				user_create_validate_msg('field "'.$field.'" harus diisi!');
				return false;
				break;
			}
		}
		$params = $data['params'];

		/* CHECK SERIAL */
		if ($check_serial)
		{
			$serial = $db->getRow("SELECT * FROM `bin_serial` WHERE `code`='".$params['serial']."'");
			if (empty($serial))
			{
				user_create_validate_msg('serial "'.$params['serial'].'" tidak ditemukan!');
				return false;
			}
			// Serial harus kondisi aktif
			if (empty($serial['active']))
			{
				user_create_validate_msg('serial "'.$params['serial'].'" masih belum aktif!');
				return false;
			}
			// Serial belum expire
			if ($serial['expired']!='0000-00-00' && strtotime($serial['expired']) < time())
			{
				user_create_validate_msg('serial "'.$params['serial'].'" telah lewat mas berlaku!');
				return false;
			}
			// Serial sudah terbeli
			if (config('plan_a', 'serial_check')=='1' && empty($serial['buyer_id']))
			{
				user_create_validate_msg('serial "'.$params['serial'].'" masih dalam masa penjualan belum ada pembeli!');
				return false;
			}
			// Serial belum pernah dipakai
			if (!empty($serial['used']))
			{
				user_create_validate_msg('serial "'.$params['serial'].'" telah digunakan!');
				return false;
			}
			// Validasi PIN
			if ($serial['pin']!=$params['pin'])
			{
				user_create_validate_msg('Mohon masukkan PIN yang benar untuk serial "'.$params['serial'].'"!');
				return false;
			}
		}

		/* CHECK SPONSOR */
		$sponsor = bin_fetch_username($params['sponsor']);
		// Sponsor harus aktif
		if (empty($sponsor['active']))
		{
			user_create_validate_msg('sponsor "'.$params['sponsor'].'" harus dalam kondisi aktif dalam jaringan!');
			return false;
		}

		/* CHECK UPLINE */
		$upline = bin_fetch_username($params['upline']);
		// Upline harus di jaringan titik dari sponsor
		if ($sponsor['id']!='1' && $sponsor['id']!=$upline['id'] && !bin_isDownline($upline['username'], $sponsor['username']))
		{
			user_create_validate_msg('upline "'.$params['upline'].'" tidak dalam jaringan yang sama dengan sponsor "'.$params['sponsor'].'"!');
			return false;
		}

		/* CHECK POSITION */
		if ($params['position']==0 && $upline['total_left'] > 0)
		{
			user_create_validate_msg('posisi kiri telah diisi member lain untuk upline "'.$upline['username'].'"!');
			return false;
		}else
		if ($params['position']==1 && $upline['total_right'] > 0)
		{
			user_create_validate_msg('posisi kanan telah diisi member lain untuk upline "'.$upline['username'].'"!');
			return false;
		}

		/* CHECK LOCATION */
		if (empty($params['location_id']))
		{
			user_create_validate_msg('Mohon masukkan lokasi anda!');
			return false;
		}
		$location = $db->getRow("SELECT * FROM `bin_location` WHERE `id`=".intval($params['location_id']));
		if (empty($location))
		{
			user_create_validate_msg('Mohon masukkan lokasi anda yang sesuai yang telah terdaftar!');
			return false;
		}
		/* LIMIT MEMBER FIELDS */
		$cfg = config('bin_fields');
		if (!empty($cfg['limit']) && is_array($cfg['limit']))
		{
			foreach ($cfg['limit'] as $field => $limit)
			{
				if ($limit > 0)
				{
					$field_id = @intval($cfg['fields'][$field]);
					if (!empty($field_id))
					{
						$count = $db->getOne("SELECT COUNT(*) FROM `bin_field` WHERE `field_id`={$field_id} AND `field_value`='{$params[$field]}'");
						if ($count >= $limit)
						{
							user_create_validate_msg('Maaf, "'.lang($field).'" yang anda masukkan telah digunakan member sebelumnya sebanyak '.money($limit).' kali!');
							return false;
						}
					}
				}
			}
		}
	}
	return true;
}
/*
DIPANGGIL KETIKA USER MELAKUKAN PERUBAHAN PADA DATA DIRI
*/
function bin_user_change($user_id)
{
	global $db;
	$account = $db->getRow("SELECT * FROM `bbc_account` WHERE `user_id`={$user_id}");
	if (!empty($account))
	{
		$bin = $db->getRow("SELECT `id`, `location_id` FROM `bin` WHERE `user_id`={$user_id}");
		if (!empty($bin))
		{
			$p = str_replace("\n", '\n', $account['params']);
			$p = config_decode($p);
			$q = "UPDATE `bin` SET
				`name`             = '{$account['name']}',
				`location_address` = '{$p['location_address']}',
				`location_latlong` = '{$p['location_latlong']}',
				`bank_name`        = '{$p['Rekening Bank']}',
				`bank_no`          = '{$p['No. Rekening']}'
				WHERE `id`         = {$bin['id']}
				";
			$db->Execute($q);
			if ($bin['location_id']!=$p['location_id'])
			{
				$p['location_name'] = $db->getOne("SELECT `detail` FROM `bin_location` WHERE `id`={$p['location_id']}");
				$q = "UPDATE `bin` SET
					`location_id`      = '{$p['location_id']}',
					`location_name`    = '{$p['location_name']}'
					WHERE `id`         = {$bin['id']}
					";
				$db->Execute($q);
				$db->Execute("DELETE FROM `bin_location_member` WHERE `bin_id`={$bin['id']}");
				bin_user_create_location($bin['id'], $user_id, $p['location_id']);
			}
			$all_fields = $db->getAssoc("SELECT `title`, `id` FROM `bbc_user_field` WHERE `group_id`=".get_config('bin', 'plan_a', 'group_id'));
			$bin_fields = get_config('bin', 'bin_fields', 'limit');
			foreach ($bin_fields as $key => $field_id)
			{
				$db->Execute("UPDATE `bin_field` SET `field_value`='".@$p[$key]."' WHERE `bin_id`={$bin['id']} AND `field_id`={$field_id}");
			}
		}
	}
}
/*
MENGAMBIL DETAIL MEMBER DENGAN ID MAUPUN USERNAME
*/
function bin_fetch($id_or_username)
{
	return is_numeric($id_or_username) ? bin_fetch_id($id_or_username) : bin_fetch_username($id_or_username);
}
/*
MENGAMBIL DETAIL MEMBER BERDASARKAN bin_id
*/
function bin_fetch_id($bin_id)
{
	if (!empty($bin_id))
	{
		global $db;
		return $db->getRow("SELECT * FROM `bin` WHERE `id`={$bin_id}");
	}
	return array();
}
/*
MENGAMBIL DETAIL MEMBER BERDASARKAN username
*/
function bin_fetch_username($username)
{
	if (!empty($username))
	{
		global $db;
		return $db->getRow("SELECT * FROM `bin` WHERE `username`='{$username}'");
	}
	return array();
}
/*
Example
-- mengambil path utama
bin_path();                   == _ROOT.'images/modules/bin/files/'
-- mengambil path dari username GS100001
bin_path('GS100001');         == _ROOT.'images/modules/bin/files/username/GS/10/00/01/'
-- mengambil path dari bin_id 862394 (path nya sendiri hasil dr softlink username yg terhubung)
bin_path(862394);             == _ROOT.'images/modules/bin/files/id/86/23/94/'
-- mengambil filepath dari bin_id 862394
bin_path(862394, 'downline'); == _ROOT.'modules/bin/files/id/86/23/94/downline.txt'
*/
function bin_path($bin_id_or_usr='', $file = '')
{
	$out = _ROOT.'images/modules/bin/files/';
	if (!empty($bin_id_or_usr))
	{
		$out .= is_numeric($bin_id_or_usr) ? 'id/' : 'username/';
		$out .= $bin_id_or_usr.'/';
		if (!empty($file))
		{
			$files = ['downline', 'downline_left', 'downline_right', 'upline', 'upsponsor', 'downsponsor'];
			if (in_array($file, $files))
			{
				$out .= $file.'.txt';
			}
		}
	}
	return $out;
}
/*
DIPANGGIL PERTAMA KALI KETIKA MEMBER DIBUAT SEBELUM MENG-EKSEKUSI FUNCTION SCRAWLING (bin_up_node(), bin_up_pair(), bin_up_sponsor())
*/
function bin_path_create($bin_id, $username)
{
	_func('path');
	$path_bin = bin_path();
	file_write($path_bin.'username/'.$username.'/index.html');
	path_create($path_bin.'id/');
	if (!file_exists($path_bin.'id/'.$bin_id))
	{
		$path_cur = getcwd().'/';
		chdir($path_bin.'id/');
		symlink('../username/'.$username, $bin_id);
		chdir($path_cur);
	}
}
/*
BERFUNGSI UNTUK MENCHECK APAKAH ID ATAU USERNAME YANG DIMASUKKAN ADALAH DI DALAM JARINGAN DOWNLINE NYA
*/
function bin_isDownline($bin_id_or_username, $current_bin_id_or_username='')
{
	if (empty($current_bin_id_or_username))
	{
		global $Bbc;
		if (empty($Bbc->member))
		{
			return false;
		}
		$current_bin_id_or_username = $Bbc->member['id'];
	}
	if (is_numeric($bin_id_or_username))
	{
		$check = '~\:'.$bin_id_or_username.'\n~is';
	}else{
		$check = '~\n?'.$bin_id_or_username.'\:~is';
	}
	return preg_match($check, file_read(bin_path($current_bin_id_or_username, 'downline')));
}
/*
BERFUNGSI UNTUK MENCHECK APAKAH ID ATAU USERNAME YANG DIMASUKKAN ADALAH DI DALAM JARINGAN UPLINE NYA
*/
function bin_isUpline($bin_id_or_username, $current_bin_id_or_username='')
{
	if (empty($current_bin_id_or_username))
	{
		global $Bbc;
		if (empty($Bbc->member))
		{
			return false;
		}
		$current_bin_id_or_username = $Bbc->member['id'];
	}
	if (is_numeric($bin_id_or_username))
	{
		$check = '~\:'.$bin_id_or_username.'\n~is';
	}else{
		$check = '~\n?'.$bin_id_or_username.'\:~is';
	}
	return preg_match($check, file_read(bin_path($current_bin_id_or_username, 'upline')));
}
/*
BERFUNGSI UNTUK MENCHECK APAKAH ID ATAU USERNAME YANG DIMASUKKAN ADALAH DI DALAM JARINGAN SPONSOR KE ATAS NYA
*/
function bin_isDownsponsor($bin_id_or_username, $current_bin_id_or_username='')
{
	if (empty($current_bin_id_or_username))
	{
		global $Bbc;
		if (empty($Bbc->member))
		{
			return false;
		}
		$current_bin_id_or_username = $Bbc->member['id'];
	}
	if (is_numeric($bin_id_or_username))
	{
		$check = '~\:'.$bin_id_or_username.'\n~is';
	}else{
		$check = '~\n?'.$bin_id_or_username.'\:~is';
	}
	return preg_match($check, file_read(bin_path($current_bin_id_or_username, 'downsponsor')));
}
/*
BERFUNGSI UNTUK MENCHECK APAKAH ID ATAU USERNAME YANG DIMASUKKAN ADALAH DI DALAM JARINGAN SPONSOR KE BAWAH NYA
*/
function bin_isUpsponsor($bin_id_or_username, $current_bin_id_or_username='')
{
	if (empty($current_bin_id_or_username))
	{
		global $Bbc;
		if (empty($Bbc->member))
		{
			return false;
		}
		$current_bin_id_or_username = $Bbc->member['id'];
	}
	if (is_numeric($bin_id_or_username))
	{
		$check = '~\:'.$bin_id_or_username.'\n~is';
	}else{
		$check = '~\n?'.$bin_id_or_username.'\:~is';
	}
	return preg_match($check, file_read(bin_path($current_bin_id_or_username, 'upsponsor')));
}

/*
DIGUNAKAN UNTUK MENGIHTUNG JUMLAH MEMBER YANG DIPERLUKAN UNTUK MENCAPAI KEDALAMAN TITIK TERTENTU
berfungsi untuk development atau menentukan keputusan saja
*/
function bin_calc_member($level=1)
{
	$total = 1;
	$output = array(0,$total);
	for($i=1;$i < $level; $i++)
	{
	  $total *= 2;
	  $total++;
	  $output[] = money($total);
	}
	return $output;
}

function bin_percent($num=0, $total=0)
{
	return round(($num/$total*100), 2).' %';
}

function bin_charge($total, $charge)
{
  $output = $total;
  if(preg_match_all('~(([^0-9]+)?([0-9]+(?:\.[0-9]+)?)([^0-9]+)?)~s', $charge, $m))
  {
    foreach ((array)@$m[3] as $i => $d)
    {
    	$is_percent = (substr($m[4][$i],0,1) == '%');
      if ($i == 0)
      {
      	$charge = $is_percent ? $output*$d/100 : $d;
        switch(substr($m[2][$i],-1))
        {
          case '-':
          	$output -= $charge;
            break;
          default:
          	$output += $charge;
            break;
        }
      }else{
      	$charge = $is_percent ? $output*$d/100 : $d;
        switch (substr($m[4][--$i],-1))
        {
          case '-':
          	$output -= $charge;
            break;
          default:
          	$output += $charge;
            break;
        }
      }
    }
  }
  $output -= $total;
  return $output;
}

function bin_bonus_list()
{
	global $Bbc, $db;
	if (!empty($Bbc->bin_bonus_list))
	{
		return $Bbc->bin_bonus_list;
	}
	$Bbc->bin_bonus_list = array();
	$plan_a = get_config('bin', 'plan_a');
	$r = $db->cacheGetAssoc("SELECT id, name, message FROM `bin_balance_type` WHERE `credit`=1 AND `finance`=0 AND `balance`=1 AND `active`=1 ORDER BY id ASC");
	if (!empty($r))
	{
		$Bbc->bin_bonus_list = $r;
	}
	return $Bbc->bin_bonus_list;
}

function bin_month_dates($year, $month)
{
	$month  = sprintf("%'.02d", $month);
	$max    = date('j', strtotime("{$year}-{$month}-01 +1 MONTH -1 DAY"));
	$output = array();
	$i      = 0;
	while ($i < $max)
	{
		$i++;
		$output[] = sprintf("%'.02d", $i);
	}
	return $output;
}

function bin_reward_src($src, $id, $is_imgsrc = false, $is_large_image = false)
{
	$output = '';
	$path   = 'images/modules/bin/images/'.$id.'/';
	if (is_url($src))
	{
		$output = $src;
	}else
	if (is_file(_ROOT.$src))
	{
		$output = _URL.$src;
	}else
	if ($is_large_image && is_file(_ROOT.$path.'p_'.$src))
	{
		$output = _URL.$path.'p_'.$src;
	}else
	if (is_file(_ROOT.$path.$src))
	{
		$output = _URL.$path.$src;
	}else{
		$p = '';
		$n = get_config('content', 'manage', 'images');
		if (!empty($n)){
			$p = 'images/modules/content/'.$n;
		}else{
			$p = 'images/modules/content/none.gif';
		}
		if (is_file(_ROOT.$p))
		{
			$output = _URL.$p;
		}
	}
	if ($is_imgsrc)
	{
		$tag = is_string($is_imgsrc) ? $is_imgsrc : ' class="img-thumbnail img-responsive"';
		$output = image($output, '', $tag);
	}
	return $output;
}