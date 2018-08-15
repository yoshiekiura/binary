<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea', 'bin_serial');
$form->initSearch();

if (config('plan_a', 'serial_use')=='1')
{
	$form->search->addInput('type_id','selecttable');
	$form->search->input->type_id->addOption('--pilih type--', '');
	$form->search->input->type_id->setReferenceTable('`bin_serial_type` ORDER BY `id` ASC');
	$form->search->input->type_id->setReferenceField( 'name', 'id' );
}

$form->search->addInput('used','select');
$form->search->input->used->addOption('--pilih status--', '');
$form->search->input->used->addOption('Terpakai', '1');
$form->search->input->used->addOption('Belum Terpakai', '0');

$form->search->addInput('active','select');
$form->search->input->active->addOption('--aktifasi--', '');
$form->search->input->active->addOption('Active', '1');
$form->search->input->active->addOption('inactive', '0');

$form->search->addInput('created','date');
$form->search->input->created->setTitle('Tgl. Dibuat');
$form->search->input->created->setSearchQueryLike(false);

$form->search->addInput('user_date','date');
$form->search->input->user_date->setTitle('Tgl. Digunakan');
$form->search->input->user_date->setSearchQueryLike(false);

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->setTitle('Masukkan Serial ID');
$form->search->input->keyword->addSearchField('code', false);

$form->search->addExtraField('active', 1);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form->initRoll("{$add_sql} ORDER BY id DESC", 'id' );

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Laporan Data Serial');

$form->roll->addInput('code','sqlplaintext');
$form->roll->input->code->setTitle('Serial');

if (config('plan_a', 'serial_use')=='1')
{
	$form->roll->addInput('type_id', 'selecttable');
	$form->roll->input->type_id->setTitle('Type');
	$form->roll->input->type_id->setReferenceTable('bin_serial_type');
	$form->roll->input->type_id->setReferenceField('name', 'id');
	$form->roll->input->type_id->setPlaintext(true);
}

$form->roll->addInput('buyer_bin_id','selecttable');
$form->roll->input->buyer_bin_id->setTitle('Pembeli');
$form->roll->input->buyer_bin_id->setReferenceTable('bin');
$form->roll->input->buyer_bin_id->setReferenceField('username', 'id');
$form->roll->input->buyer_bin_id->setLinks($Bbc->mod['circuit'].'.list_detail');
$form->roll->input->buyer_bin_id->setModal();
$form->roll->input->buyer_bin_id->setPlaintext(true);

$form->roll->addInput('buyer_date','sqlplaintext');
$form->roll->input->buyer_date->setTitle('Terjual');
$form->roll->input->buyer_date->setDateFormat();

$form->roll->addInput('user_bin_id','selecttable');
$form->roll->input->user_bin_id->setTitle('Pengguna');
$form->roll->input->user_bin_id->setReferenceTable('bin');
$form->roll->input->user_bin_id->setReferenceField('username', 'id');
$form->roll->input->user_bin_id->setLinks($Bbc->mod['circuit'].'.list_detail');
$form->roll->input->user_bin_id->setModal();
$form->roll->input->user_bin_id->setPlaintext(true);

$form->roll->addInput('user_date','sqlplaintext');
$form->roll->input->user_date->setTitle('Digunakan');
$form->roll->input->user_date->setDateFormat();

$form->roll->addInput('created','sqlplaintext');
$form->roll->input->created->setTitle('Dibuat');
$form->roll->input->created->setDateFormat();

if (!isset($keyword['used']))
{
	$form->roll->addInput('used','select');
	$form->roll->input->used->setTitle('Status');
	$form->roll->input->used->addOption('Terpakai', '1');
	$form->roll->input->used->addOption('Belum Terpakai', '0');
	$form->roll->input->used->setPlaintext(true);
}

$form->roll->addInput('active','select');
$form->roll->input->active->setTitle('Active');
$form->roll->input->active->addOption('Active', '1');
$form->roll->input->active->addOption('Inactive', '0');
$form->roll->input->active->setPlaintext(true);

$form->roll->setDeleteTool(false);
$form->roll->setSaveTool(false);

$form->roll->addReport();

echo $form->roll->getForm();