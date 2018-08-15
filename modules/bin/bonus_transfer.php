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
	$form = _lib('pea', 'bin_balance');
	$form->initRoll('WHERE `bin_id`='.$id.' AND `credit`=1 ORDER BY `id` DESC', 'id' );

	$form->roll->addInput('header','header');
	$form->roll->input->header->setTitle('Histori Transfer Yang Telah Diterima');

	$form->roll->addInput('created','sqlplaintext');
	$form->roll->input->created->setTitle('Tanggal');
	$form->roll->input->created->setDateFormat();

	$form->roll->addInput('title','sqlplaintext');
	$form->roll->input->title->setTitle('Judul');

	$form->roll->addInput('amount','sqlplaintext');
	$form->roll->input->amount->setTitle('Jumlah');
	$form->roll->input->amount->setNumberFormat();

	// $form->roll->addInput('total','sqlplaintext');
	// $form->roll->input->total->setNumberFormat();

	$form->roll->setDeleteTool(false);
	$form->roll->setSaveTool(false);
	echo $form->roll->getForm();
}
