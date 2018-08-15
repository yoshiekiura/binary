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
if (!empty($_GET['success']))
{
	echo msg($_GET['success'], 'success');
}
$form = _lib('pea',  'bin_finance');
$form->initEdit('');

$form->edit->addInput('header','header');
$form->edit->input->header->setTitle('Tambahkan Pengeluaran');

$form->edit->addInput('title','text');
$form->edit->input->title->setTitle('Keperluan');
$form->edit->input->title->setRequire();

$form->edit->addInput('amount','text');
$form->edit->input->amount->setTitle('Nilai');
$form->edit->input->amount->setRequire('number');

$form->edit->addInput('ondate','date');
$form->edit->input->ondate->setTitle('Tanggal');
$form->edit->input->ondate->setRequire();
$form->edit->input->ondate->setDefaultValue('now');

$form->edit->action();
echo $form->edit->getForm();

$form->initRoll("WHERE `credit`=1 ORDER BY id DESC");

$form->roll->setSaveTool(false);
$form->roll->setDeleteTool(false);

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Pengeluaran Terakhir');

$form->roll->addInput('title','sqlplaintext');
$form->roll->input->title->setTitle('Keperluan');

$form->roll->addInput('amount','sqlplaintext');
$form->roll->input->amount->setTitle('Nilai');
$form->roll->input->amount->setNumberFormat();

$form->roll->addInput('ondate','date');
$form->roll->input->ondate->setTitle('Tanggal');
$form->roll->input->ondate->setPlaintext(true);

$form->roll->action();
echo $form->roll->getForm();