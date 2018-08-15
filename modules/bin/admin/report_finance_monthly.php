<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea',  'bin_finance_monthly');
_func('date');
$year  = date('Y');
$month = date('n');
if (!empty($_GET['month']))
{
	$month = intval($_GET['month']);
}else
if (!empty($_SESSION['search']['bin_finance_monthly']['search_month']))
{
	$month = $_SESSION['search']['bin_finance_monthly']['search_month'];
}

$form->initSearch();

$form->search->addInput('month','select');
$form->search->input->month->setDefaultValue($month);
$j = 0;
$r = month_r();
foreach ($r as $m)
{
	$j++;
	$form->search->input->month->addOption(ucwords($m), $j);
}

$first_year = $db->getOne("SELECT `year` FROM `bin_finance_monthly` WHERE 1 ORDER BY `id` ASC LIMIT 1");
$today_year = $year;
if (empty($first_year))
{
	$first_year = $today_year;
}
if (!empty($_GET['year']))
{
	$year = intval($_GET['year']);
}else
if (!empty($_SESSION['search']['bin_finance_monthly']['search_year']))
{
	$year = $_SESSION['search']['bin_finance_monthly']['search_year'];
}

$form->search->addInput('year','select');
$form->search->input->year->setDefaultValue($year);
$form->search->input->year->addOption($today_year);
while ($today_year > $first_year)
{
	$today_year--;
	$form->search->input->year->addOption($today_year);
}

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
if (empty($_GET['is_ajax']))
{
	echo $form->search->getForm();
}

if (empty($keyword))
{
	$add_sql = "WHERE `month`={$month} AND `year`={$year}";
}

/* MEMBUAT CHART */
$datas = $db->getAll("SELECT * FROM `bin_finance_monthly` {$add_sql}");
$date  = $year.'-'.sprintf('%02d', $month).'-01';
if (!empty($_GET['is_ajax']))
{
	$days   = bin_month_dates($year, $month);
	$output = array(
		'ok'     => 0,
		'msg'    => 'Sorry, no available data',
		'result' => array()
		);
	if (!empty($datas))
	{
		$table    = array();
		$rows     = array();
		$coloms   = array('Info', 'Amount', '#');
		$series   = array();
		$finances = $db->getAssoc("SELECT a.`type_id`, t.`name` FROM `bin_finance_monthly` AS a LEFT JOIN `bin_balance_type` AS t ON (a.`type_id`=t.`id`) WHERE a.`month`={$month} AND a.`year`={$year}");

		foreach ($finances as $type_id => $finance)
		{
			$data = array();
			foreach ($days as $i => $day)
			{
				$data[] = array(
					'y' => 0,
					'dataLabels' => 'Rp. 0'
					);
			}
			$series[$type_id] = array(
				'name' => $finance,
				'data' => $data
				);
		}
		$q = "SELECT * FROM `bin_finance_daily` {$add_sql}";
		$r = $db->getAll($q);
		foreach ($r as $d)
		{
			$i = $d['day']-1;
			$series[$d['type_id']]['data'][$i]['y'] += $d['amount'];
			$series[$d['type_id']]['data'][$i]['dataLabels'] = 'Rp. '.money($series[$d['type_id']]['data'][$i]['y']);
		}
		$series = array_values($series);
		$income = 0;
		foreach ($datas as $dt)
		{
			$rows[] = array($finances[$dt['type_id']], money($dt['amount']), ($dt['credit'] ? '-' : '+'));
			if ($dt['credit'])
			{
				$income -= $dt['amount'];
			}else{
				$income += $dt['amount'];
			}
		}
		$rows[] = array('Nilai Pendapatan', money($income), '');
		$colors = array();
		foreach ($days as $day)
		{
			$N = date('N', strtotime("{$year}-{$month}-{$day}"));
			switch ($N)
			{
				case 6:
					if (empty($colors['green']))
					{
						$colors['green'] = array();
					}
					$colors['green'][] = $day;
					break;
				case 7:
					if (empty($colors['red']))
					{
						$colors['red'] = array();
					}
					$colors['red'][] = $day;
					break;
			}
		}
		$report = array(
			'title' => array(
				'text' => 'Laporan Keuangan Bulanan '
				),
			'subtitle' => array(
				'text' => date('F Y', strtotime($date))
				),
			'xAxis' => array(
				'categories' => $days
				),
			'yAxis' => array(
				'title' => array(
					'text' => date('F Y', strtotime($date))
					)
				),
			'tooltip' => array(
				'headerFormat' => '<h4>'.date_month($month, 3).' {point.key}, '.$year.'</h4><table>',
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
			'series' => $series,
			'table'  => array(
				'coloms' => $coloms,
				'rows'   => $rows
				),
			'color' => $colors
			);
		$output = array(
			'ok'     => 1,
			'msg'    => 'success',
			'result' => $report
			);
	}
	$_url  = preg_replace(array('~&year\=[0-9]+~s', '~&month\=[0-9]+~s'), array('',''), seo_uri('is_ajax'));
	$month = sprintf("%'.02d", $month);
	$prev  = '';
	$next  = '';
	// is last month available
	$first = $year.'-'.$month.'-'.$days[0];
	$q0    = "SELECT 1 FROM bin_finance WHERE `ondate`<'{$first}' LIMIT 1";
	if ($db->getOne($q0))
	{
		$dt   = explode('-', date('Y-m', strtotime($date.' -1 MONTH')));
		$prev = $_url.'&year='.$dt[0].'&month='.$dt[1];
	}
	// is next month available
	$last = $year.'-'.$month.'-'.end($days);
	$q1   = "SELECT 1 FROM bin_finance WHERE `ondate`>'{$last}' LIMIT 1";
	if ($db->getOne($q1))
	{
		$dt   = explode('-', date('Y-m', strtotime($date.' +1 MONTH')));
		$next = $_url.'&year='.$dt[0].'&month='.$dt[1];
	}
	// pr($q0, $q1, __FILE__.':'.__LINE__);
	$output['prev'] = $prev;
	$output['next'] = $next;
	output_json($output);
}
$is_exists = $db->getOne("SELECT 1 FROM `bin_finance_monthly` WHERE 1 LIMIT 1");
if (!empty($is_exists))
{
	link_js(_ROOT.'modules/bin/images/chart.js');
	?>
	<div class="container" id="myreport">
		<span class="pull-left navi"><a href="" id="link_prev"><?php echo icon('fa-angle-left'); ?></a></span>
		<span class="pull-right navi"><a href="" id="link_next"><?php echo icon('fa-angle-right'); ?></a></span>
		<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"><center style="padding-top: 155px;"><i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i> <span class="sr-only">Loading...</span></center></div>
		<div class="table-responsive"></div>
	</div>
	<div class="clearfix"></div>
	<style type="text/css">
		.container {
			position: relative;;
		}
		.container .navi {
			position: absolute;
			top: 120px;
			z-index: 99;
			display: none;
			font-size: 60px;
		}
		.container .navi a {
			color: #ccc;
		}
		.container .navi a:hover {
			color: #333;
		}
		.container .pull-left {
			left: 10px;
		}
		.container .pull-right {
			right: 10px;
		}
		.table td {
			.pull-right()
		}
	</style>
	<?php
}else{
	echo msg('Maaf, data tidak ditemukan', 'warning');
}
