<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($_POST['add_title']) && !empty($_POST['add_amount']))
{
	$amount = intval($_POST['add_amount']);
	$_url = $Bbc->mod['circuit'].'.'.$Bbc->mod['task'].'&';
	if (!empty($amount))
	{
		bin_finance(0, 10, $amount, ['operasional' => $_POST['add_title']], $_POST['add_ondate']);
		$_url .= 'success='.urlencode('Pengeluaran senilai "'.money($amount).'" telah disimpan dalam database');
		redirect($_url);
	}else{
		echo msg('Maaf, nilai pengeluaran minimal diatas 1', 'danger');
	}
	$_POST = array();
}

if (!empty($_GET['is_ajax']))
{
	$id   = @intval(($_GET['id']));
	$bin  = $db->getRow("SELECT * FROM `bin` WHERE `id`={$id}");
	$data = array();
	if (!empty($bin))
	{
		$data['bonus']        = intval($bin['balance']);
		$data['reward']       = intval($db->getOne("SELECT SUM(`reward_amount`) FROM `bin_reward_member` WHERE `bin_id`={$bin['id']} AND `received` IN (0,2) AND `active`=1"));
		$data['amount']       = $data['bonus']+$data['reward'];
		$data['bin_username'] = $bin['username'];
		$data['bin_name']     = $bin['name'];
	}
	$output = array(
		'ok'     => !empty($data) ? 1 : 0,
		'msg'    => !empty($data) ? 'success' : 'Data tidak ditemukan',
		'result' => $data
		);
	output_json($output);
}


if (!empty($_GET['success']))
{
	echo msg($_GET['success'], 'success');
}
$form = _lib('pea',  'bin_claim');
$form->initEdit('');

$form->edit->addInput('header','header');
$form->edit->input->header->setTitle('Klaim Bonus Member');

$form->edit->addInput( 'bin_id', 'selecttable' );
$form->edit->input->bin_id->setTitle('Member yang ingin di Klaim');
$form->edit->input->bin_id->setCaption('Masukkan Member ID');
$form->edit->input->bin_id->setReferenceTable('bin');
$form->edit->input->bin_id->setReferenceField( 'username', 'id' );
$form->edit->input->bin_id->setAutoComplete(true);
$form->edit->input->bin_id->setRequire();
if (!empty($_GET['id']))
{
	$form->edit->input->bin_id->setDefaultValue($_GET['id']);
}

$form->edit->addInput('bonus','text');
$form->edit->input->bonus->setTitle('Bonus');
$form->edit->input->bonus->setCaption('Nilai Bonus');
$form->edit->input->bonus->setRequire('number');
$form->edit->input->bonus->setExtra('readonly');

$form->edit->addInput('reward','text');
$form->edit->input->reward->setTitle('Reward');
$form->edit->input->reward->setCaption('Nilai Reward');
$form->edit->input->reward->setRequire('number');
$form->edit->input->reward->setExtra('readonly');

$form->edit->addInput('amount','text');
$form->edit->input->amount->setTitle('Total Nilai');
$form->edit->input->amount->setRequire('number');
$form->edit->input->amount->setExtra('readonly');

$form->edit->onSave('bin_claim_bonus');
$form->edit->setResetTool(false);
$form->edit->setSaveButton('claim', 'Claim Bonus', 'usd');
echo $form->edit->getForm();
link_js('transfer_claim.js', false);

$form->initRoll("WHERE 1 ORDER BY id DESC");

$form->roll->setSaveTool(false);
$form->roll->setDeleteTool(false);

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Member terakhir di klaim');

$form->roll->addInput('member','multiinput');
$form->roll->input->member->setTitle('Username');
$form->roll->input->member->addInput('member1', 'editlinks');
$form->roll->input->member->addInput('member2', 'sqllinks');

$form->roll->input->member1->setCaption( '' );
$form->roll->input->member1->setModal();
$form->roll->input->member1->setFieldName( 'bin_id AS member1' );
$form->roll->input->member1->setLinks(array(
	$Bbc->mod['circuit'].'.genealogy'        => icon('fa-sitemap').' Genealogy',
	$Bbc->mod['circuit'].'.bonus'            => icon('fa-usd').' Bonus',
	$Bbc->mod['circuit'].'.reward'           => icon('fa-trophy').' Reward',
	$Bbc->mod['circuit'].'.transfer_history' => icon('fa-money').' Transfer',
	$Bbc->mod['circuit'].'.list_signin'      => icon('fa-sign-in').' Login'
	));

$form->roll->input->member2->setModal();
$form->roll->input->member2->setFieldName( 'bin_username AS member2' );
$form->roll->input->member2->setLinks($Bbc->mod['circuit'].'.transfer_claim_detail');

// $form->roll->addInput('bin_username','sqlplaintext');
// $form->roll->input->bin_username->setTitle('Username');

$form->roll->addInput('bin_name','sqlplaintext');
$form->roll->input->bin_name->setTitle('Name');

$form->roll->addInput('bonus','sqlplaintext');
$form->roll->input->bonus->setNumberFormat();

$form->roll->addInput('reward','sqlplaintext');
$form->roll->input->reward->setNumberFormat();

$form->roll->addInput('amount','sqlplaintext');
$form->roll->input->amount->setTitle('total');
$form->roll->input->amount->setNumberFormat();

$form->roll->addInput('created','sqlplaintext');
$form->roll->input->created->setTitle('Date');
$form->roll->input->created->setDateFormat();

$form->roll->action();
echo $form->roll->getForm();

function bin_claim_bonus($id)
{
	global $db;
	$claim = $db->getRow("SELECT * FROM `bin_claim` WHERE `id`={$id} ");
	if (!empty($claim))
	{
		$bin = $db->getRow("SELECT `id`, `username`, `name`, `balance` FROM `bin` WHERE id={$claim['bin_id']}");
		if (!empty($bin))
		{
			$claim['bonus']        = $bin['balance'];
			$claim['reward']       = intval($db->getOne("SELECT SUM(`reward_amount`) FROM `bin_reward_member` WHERE `bin_id`={$bin['id']} AND `received` IN (0,2) AND `active`=1"));
			$claim['amount']       = $claim['bonus']+$claim['reward'];
			$claim['bin_username'] = $bin['username'];
			$claim['bin_name']     = $bin['name'];

			$db->Update('bin_claim', $claim, $claim['id']);
			bin_finance($bin['id'], 13, $claim['bonus'], ['member' => $bin['username']]);

			if ($claim['reward'] > 0)
			{
				$title = 'Klaim Reward Perusahaan';
				$date  = date('Y-m-d');
				$day   = date('j');
				$month = date('n');
				$year  = date('Y');

				/* FINANCE TIDAK PERLU DIMASUKKAN KARENA KLAIM REWARD TIDAK MENAMBAH / MENGURANGI PENDAPATAN */
				// bin_finance
				// $last = $db->getOne("SELECT `total` FROM `bin_finance` WHERE 1 ORDER BY id DESC LIMIT 1");
				// $db->Insert('bin_finance', array(
				// 	'title'        => $title,
				// 	'ondate'       => $date,
				// 	'credit'       => 1,
				// 	'amount'       => $claim['reward'],
				// 	'total'        => $claim['reward']+$last,
				// 	'create_day'   => $day,
				// 	'create_month' => $month,
				// 	'create_year'  => $year
				// 	));

				// REWARD QUALIFIED
				$qualified = intval($db->getOne("SELECT SUM(`reward_amount`) FROM `bin_reward_member` WHERE `bin_id`={$bin['id']} AND `received`=0 AND `active`=1"));
				if ($qualified > 0)
				{
					// Finance Monthly
					$data = $db->getRow("SELECT * FROM `bin_finance_monthly` WHERE `type_id`=11 AND `month`={$month} AND `year`={$year}");
					if (!empty($data))
					{
						$db->Update('bin_finance_monthly', ['amount' => ($data['amount']-$qualified)], $data['id']);
					}else{
						$db->Insert('bin_finance_monthly', [
							'type_id' => 11,
							'credit'  => 1,
							'finance' => 0,
							'amount'  => '-'.$qualified,
							'month'   => $month,
							'year'    => $year
							]);
					}
					// Finance All
					$data = $db->getRow("SELECT * FROM `bin_finance_all` WHERE `type_id`=11");
					if (!empty($data))
					{
						$db->Update('bin_finance_all', ['amount' => ($data['amount']-$qualified)], $data['id']);
					}else{
						$db->Insert('bin_finance_all', [
							'type_id' => 11,
							'credit'  => 1,
							'finance' => 0,
							'amount'  => '-'.$qualified
							]);
					}
				}
			}
			$db->Execute("UPDATE `bin_reward_member` SET `active`=0 WHERE `bin_id`={$bin['id']} AND `received` IN (0,2) AND `active`=1");
		}
	}
}