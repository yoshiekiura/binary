<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea',  'bin_finance_yearly');
_func('date');
$year  = date('Y');

$form->initSearch();

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
if (!empty($_SESSION['search']['bin_finance_yearly']['search_year']))
{
	$year = $_SESSION['search']['bin_finance_yearly']['search_year'];
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
	$add_sql = "WHERE `year`={$year}";
}

/* MEMBUAT CHART */
$r_data = $db->getAll("SELECT * FROM `bin_finance_monthly` {$add_sql} ORDER BY `month` ASC, `id` ASC");
$date   = $year.'-01-01';
if (!empty($_GET['is_ajax']))
{
	if (!empty($r_data))
	{
		$types   = $db->getAssoc("SELECT * FROM `bin_balance_type` WHERE 1 ORDER BY `id` ASC");
		$datas   = array();
		$series  = array();
		$reports = array();
		$month_r = array_map(function($a) {
			return ucwords(substr($a, 0,3));
		}, month_r());
		$maximum = 0;
		$table  = array();
		$rows   = array();
		$incomes = array('Pendapatan');

		foreach ($r_data as $data)
		{
			if ($data['amount'] > $maximum)
			{
				$maximum = $data['amount'];
			}
			$datas[$data['month']][$data['type_id']] = $data;
		}
		foreach ($types as $type_id => $type)
		{
			$data = array();
			$row  = array($type['name']);
			$show = false;
			foreach ($month_r as $i => &$month)
			{
				$i++;
				$amount       = @intval($datas[$i][$type_id]['amount']);
				$amount_total = @intval($datas[$i][1]['amount']);
				$data[]       = array(
					'y'          => round($amount/$maximum*100, 2),
					'dataLabels' => money($amount)
					);
				if (!empty($amount) && $type_id!=1)
				{
					$row[] = money($amount).'<br/>('.bin_percent($amount, $amount_total).')';
				}else{
					$row[] = money($amount);
				}

				if (empty($incomes[$i]))
				{
					$incomes[$i] = 0;
				}
				if (!empty($type['active']))
				{
					if ($type['credit']==0)
					{
						$incomes[$i] += $amount;
					}else
					if ($type['finance']==0)
					{
						$incomes[$i] -= $amount;
					}
				}
				if (!$show && $amount > 0)
				{
					$show = true;
				}
			}
			if ($show)
			{
				$rows[] = $row;
				$series[]  = array(
					'name' => $type['name'],
					'data' => $data
					);
			}
		}
		foreach ($incomes as $i => &$income)
		{
			if ($i > 0)
			{
				$income = money($income);
			}
		}
		$rows[] = $incomes;
		$report = array(
			'title' => array(
				'text' => 'Laporan Keuangan Tahunan'
				),
			'subtitle' => array(
				'text' => 'Tahun '.$year
				),
			'xAxis' => array(
				'categories' => $month_r
				),
			'yAxis' => array(
				'title' => array(
					'text' => 'laporan tahunan'
					)
				),
			'tooltip' => array(
				'headerFormat' => '<h4>{point.key}</h4><table>',
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
				'coloms' => array_merge(['Info'], $month_r),
				'rows'   => $rows
				)
			);
		$output = array(
			'ok'     => 1,
			'msg'    => 'success',
			'result' => $report
			);
	}else{
		$output = array(
			'ok'     => 0,
			'msg'    => 'Sorry, no available data',
			'result' => array()
			);
	}
	$_url = preg_replace(array('~&year\=[0-9]+~s', '~&month\=[0-9]+~s'), array('',''), seo_uri('is_ajax'));
	$prev = '';
	$next = '';
	$q    = "SELECT 1 FROM `bin_finance_monthly` WHERE `year`<{$year} LIMIT 1";
	if ($db->getOne($q))
	{
		$prev = $_url.'&year='.($year-1);
	}
	$q = "SELECT 1 FROM `bin_finance_monthly` WHERE `year`>{$year} LIMIT 1";
	if ($db->getOne($q))
	{
		$next = $_url.'&year='.($year+1);
	}
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
