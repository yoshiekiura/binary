<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id   = @intval($_GET['id']);
$form = _lib('pea', 'bin_balance');
$form->initSearch();

$types = bin_bonus_list();
$form->search->addInput('type_id','select');
$form->search->input->type_id->addOption('--pilih type--', '');
foreach ($types as $type_id => $type)
{
	$form->search->input->type_id->addOption($type['name'], $type_id);
}

$form->search->addInput('created','date');
$form->search->input->created->setTitle('Tanggal');
$form->search->input->created->setSearchQueryLike(false);

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->setTitle('Masukkan Kata Kunci');
$form->search->input->keyword->addSearchField('username, title, description, amount, total', false);

$form->search->addExtraField('credit','0');

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();
$add_sql .= ' ORDER BY id DESC';

$form->initRoll($add_sql, 'id' );

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Laporan Catatan Bonus');

$form->roll->addInput('bin_id','selecttable');
$form->roll->input->bin_id->setTitle('Username');
$form->roll->input->bin_id->setReferenceTable('bin');
$form->roll->input->bin_id->setReferenceField('username', 'id');
$form->roll->input->bin_id->setLinks($Bbc->mod['circuit'].'.list_detail');
$form->roll->input->bin_id->setModal();
$form->roll->input->bin_id->setPlaintext(true);

$form->roll->addInput('title','sqlplaintext');

$form->roll->addInput('amount','sqlplaintext');
$form->roll->input->amount->setNumberFormat();

$form->roll->addInput('created','sqlplaintext');
$form->roll->input->created->setTitle('Dibuat');
$form->roll->input->created->setDateFormat();

$form->roll->setDeleteTool(false);
$form->roll->setSaveTool(false);
$form->roll->addReport();
echo $form->roll->getForm();