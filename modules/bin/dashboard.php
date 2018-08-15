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
	echo msg(lang('Maaf, data yang anda akses bukan termasuk dalam jaringan anda'), 'danger');
}else{
	$member  = $db->getRow("SELECT * FROM `bin` AS b LEFT JOIN `bbc_account` AS c ON (b.`user_id`=c.`user_id`) WHERE b.`id`={$id}");
	$account = config_decode($member['params']);

	/* FORMAT JSON FOR CHART */
	$date = date('Y-m-d');
	if (!empty($_GET['date']))
	{
		$time = strtotime($_GET['date']);
		if (!empty($time))
		{
			$date = $_GET['date'];
		}
	}
	$time = strtotime($date);
	list($year, $month, $day) = array_map('intval', explode('-', $date));
	$exists  = $db->getAll("SELECT * FROM `bin_bonus_daily` WHERE `bin_id`={$id} LIMIT 1");
	if ($exists && !empty($_GET['is_ajax']))
	{
		$output = array(
			'ok'     => 0,
			'msg'    => 'Sorry, no available data',
			'result' => array()
			);
		// DEFINE DAYS
		$times  = array();
		$days   = array();
		$dates  = array();
		$colors = array();
		$coloms = array();
		for ($i =6; $i >= 0; $i--)
		{
			$cur      = $time-($i*86400);
			$iday     = date('D - M jS, Y', $cur);
			$times[]  = $cur;
			$days[]   = $iday;
			$dates[]  = array_map('intval', explode('-', date('Y-m-d', $cur)));
			$coloms[] = array(
				'y'          => 0,
				'dataLabels' => 'Rp. 0'
				);
			$N = date('N', $cur);
			switch ($N)
			{
				case 6:
					if (empty($colors['green']))
					{
						$colors['green'] = array();
					}
					$colors['green'][] = $iday;
					break;
				case 7:
					if (empty($colors['red']))
					{
						$colors['red'] = array();
					}
					$colors['red'][] = $iday;
					break;
			}
		}
		// CREATE SERIES
		$series   = array();
		$types    = $db->getAssoc("SELECT id, name FROM `bin_balance_type` WHERE `active`=1");
		$query    = "SELECT * FROM `bin_bonus_daily` WHERE `bin_id`={$id} AND `credit`=0 AND `year`=%s AND `month`=%s AND `day`=%s";
		foreach ($dates as $i => $dts)
		{
			$datas = $db->getAll(vsprintf($query, $dts));
			foreach ($datas as $data)
			{
				if (empty($series[$data['type_id']]))
				{
					$series[$data['type_id']] = array(
						'name' => $types[$data['type_id']],
						'data' => $coloms
						);
				}
				$series[$data['type_id']]['data'][$i]['y'] += $data['amount'];
				$series[$data['type_id']]['data'][$i]['dataLabels'] = 'Rp. '.money($series[$data['type_id']]['data'][$i]['y']);
			}
		}
		// CREATE ARRAY REPORT
		$report = array(
			'title' => array(
				'text' => lang('Laporan Mingguan').' '
				),
			'subtitle' => array(
				'text' => $days[6]
				),
			'xAxis' => array(
				'categories' => $days
				),
			'yAxis' => array(
				'title' => array(
					'text' => lang('Rupiah')
					)
				),
			'tooltip' => array(
				'headerFormat' => '<h4> {point.key}</h4><table>',
				'pointFormat'  => '<tr><td>{series.name}</td><td>: {point.dataLabels}</td></tr>',
				'footerFormat' => '</table>',
				'shared'       => 1,
				'useHTML'      => 1,
				),
			'plotOptions' => array(
				'column' => array(
					'pointPadding' => 0.2,
					'borderWidth' => 0,
					)
				),
			'series' => array_values($series),
			'color'  => $colors
			);
		$output = array(
			'ok'     => 1,
			'msg'    => 'success',
			'result' => $report
			);
		$_url  = preg_replace(array('~&date\=[^&]+~s', '~/?date,[^/]+~s'), '', seo_uri('is_ajax'));
		$_url .= preg_match('~\?~is', $_url) ? '&' : '?';
		$prev  = '';
		$next  = '';
		// is last week available
		$first  = implode('-', $dates[0]);
		$q0     = "SELECT 1 FROM `bin_bonus_daily` WHERE `bin_id`={$id} AND `created`<'{$first} 00:00:00' LIMIT 1";
		if ($db->getOne($q0))
		{
			$prev = $_url.'date='.date('Y-m-d', strtotime($first.' -1 DAY'));
		}
		// is next week available
		$last = implode('-', $dates[6]);
		$q1   = "SELECT 1 FROM `bin_bonus_daily` WHERE `bin_id`={$id} AND `created`>'{$last} 23:59:59' LIMIT 1";
		if ($db->getOne($q1))
		{
			$next = $_url.'date='.date('Y-m-d', strtotime($last.' +7 DAYS'));
		}
		$output['prev'] = $prev;
		$output['next'] = $next;
		output_json($output);
	}
	include tpl('dashboard.html.php');
}