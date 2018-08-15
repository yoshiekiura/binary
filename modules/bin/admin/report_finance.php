<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea',  'bin_finance_all');
$form->initRoll("WHERE 1 ORDER BY type_id ASC");

$form->roll->setDeleteTool(false);
$form->roll->setSaveTool(false);

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Laporan Keuangan Global');

$form->roll->addInput( 'type_id', 'selecttable' );
$form->roll->input->type_id->setTitle('Nama');
$form->roll->input->type_id->setReferenceTable('bin_balance_type');
$form->roll->input->type_id->setReferenceField( 'name', 'id' );
$form->roll->input->type_id->setPlaintext(true);

$form->roll->addInput('amount','sqlplaintext');
$form->roll->input->amount->setTitle('Total');
$form->roll->input->amount->setNumberFormat();

$form->roll->action();
echo $form->roll->getForm();

$types  = $db->getAll("SELECT * FROM `bin_balance_type` WHERE `finance`=1");
$omset  = array();
$payout = array();
foreach ($types as $type)
{
	if (empty($type['credit']))
	{
		$omset[] = $type['id'];
	}else{
		$payout[] = $type['id'];
	}
}

$t_omset = $db->getOne("SELECT SUM(`amount`) FROM `bin_finance_all` WHERE `type_id` IN (".implode(',', $omset).")");
$t_rest  = $t_omset;
$table   = array();
if ($t_omset)
{
	$table   = array(
		array(
			lang('Total Pemasukan'),
			money($t_omset).' [ + ]',
			''
			)
		);
}

$t_expense = $db->getOne("SELECT SUM(`amount`) FROM `bin_finance_all` WHERE `type_id` IN (".implode(',', $payout).")");
if ($t_expense > 0)
{
	$t_expense_p = !empty($t_expense) ? bin_percent($t_expense,$t_omset) : '';
	$t_rest     -= $t_expense;
	$table[]     = array(
		lang('Total Pengeluaran'),
		money($t_expense).' [ - ]',
		$t_expense_p
		);
}

$t_unpaid = $db->getOne("SELECT SUM(`balance`) FROM `bin` WHERE `balance`>0");
if ($t_unpaid > 0)
{
	$t_unpaid_p = !empty($t_unpaid) ? bin_percent($t_unpaid,$t_omset) : '';
	$t_rest    -= $t_unpaid;
	$table[]    = array(
		lang('Bonus Belum Terbayar'),
		money($t_unpaid).' [ - ]',
		$t_unpaid_p
		);
}

if (config('plan_a', 'reward_use'))
{
	$t_wait = $db->getOne("SELECT SUM(`reward_amount`) FROM `bin_reward_member` WHERE `received`=0 AND `active`=1");
	if ($t_wait > 0)
	{
		$t_wait_p = !empty($t_wait) ? bin_percent($t_wait, $t_omset) : '';
		$t_rest  -= $t_wait;
		$table[]  = array(
			lang('Reward Menunggu'),
			money($t_wait).' [ - ]',
			$t_wait_p
			);
	}
	if (!config('plan_a', 'reward_auto'))
	{
		$t_potential = $db->getOne("SELECT SUM(`reward_amount`) FROM `bin_reward_member` WHERE `received`=2 AND `active`=1");
		if ($t_potential > 0)
		{
			$t_potential_p = !empty($t_potential) ? bin_percent($t_potential, $t_omset) : '';
			$t_rest       -= $t_potential;
			$table[]       = array(
				lang('Potensi Reward'),
				money($t_potential).' [ - ]',
				$t_potential_p
				);
		}
	}
}
if ($t_omset)
{
	$t_rest_p = !empty($t_rest) ? bin_percent($t_rest,$t_omset) : '';
	$table[]  = array(
		lang('Total Pendapatan'),
		money($t_rest),
		$t_rest_p
		);
}
echo table(
	$table,
	array(lang('Keterangan'), lang('Nominal'), lang('Prosentase'))
);

