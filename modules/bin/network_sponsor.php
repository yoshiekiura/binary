<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea', 'bin');
$form->initSearch();
$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('username, location_name, name', false);

$form->search->addExtraField('sponsor_id', $Bbc->member['id']);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form->initRoll($add_sql.' ORDER BY id DESC', 'id');

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle(lang('Member Sponsor'));

$form->roll->addInput('member','sqllinks');
$form->roll->input->member->setLinks($Bbc->mod['circuit'].'.profile');
$form->roll->input->member->setfieldName('username AS member');
$form->roll->input->member->setModal();

$form->roll->addInput('name','sqlplaintext');
$form->roll->input->name->setTitle('nama');

$form->roll->addInput('depth_upline','sqlplaintext');
$form->roll->input->depth_upline->setTitle('Kedalaman');
$form->roll->input->depth_upline->setDisplayColumn(false);
$form->roll->input->depth_upline->setDisplayFunction(function($i) {
	global $Bbc;
	return money($i-$Bbc->member['depth_upline']);
});

$form->roll->addInput('total_sponsor','sqlplaintext');
$form->roll->input->total_sponsor->setTitle('Total Sponsor');
$form->roll->input->total_sponsor->setNumberFormat();
$form->roll->input->total_sponsor->setDisplayColumn(false);

$form->roll->addInput('total_downline','sqlplaintext');
$form->roll->input->total_downline->setTitle('Total Downline');
$form->roll->input->total_downline->setNumberFormat();
$form->roll->input->total_downline->setDisplayColumn(false);

$form->roll->addInput('total_left','sqlplaintext');
$form->roll->input->total_left->setTitle('Total Kiri');
$form->roll->input->total_left->setNumberFormat();
$form->roll->input->total_left->setDisplayColumn(false);

$form->roll->addInput('total_right','sqlplaintext');
$form->roll->input->total_right->setTitle('Total Kanan');
$form->roll->input->total_right->setNumberFormat();
$form->roll->input->total_right->setDisplayColumn(false);

$form->roll->addInput('position','select');
$form->roll->input->position->setTitle('Posisi');
$form->roll->input->position->addOption('Kanan', '1');
$form->roll->input->position->addOption('Kiri', '0');
$form->roll->input->position->setPlaintext(true);
$form->roll->input->position->setDisplayColumn(false);

// $form->roll->addInput('serial_pin','sqlplaintext');
// $form->roll->input->serial_pin->setTitle('Serial PIN');
// $form->roll->input->serial_pin->setDisplayColumn(false);

$form->roll->addInput('location_name','sqlplaintext');
$form->roll->input->location_name->setTitle('Lokasi');
$form->roll->input->location_name->setDisplayColumn(false);

$form->roll->addInput('location_address','sqlplaintext');
$form->roll->input->location_address->setTitle('Alamat');
$form->roll->input->location_address->setDisplayColumn(false);

$form->roll->addInput('created','sqlplaintext');
$form->roll->input->created->setTitle('Gabung');
$form->roll->input->created->setDateFormat();
$form->roll->input->created->setDisplayColumn(true);

$form->roll->addInput('active','select');
$form->roll->input->active->setTitle('Status');
$form->roll->input->active->addOption('Active', 1);
$form->roll->input->active->addOption('inActive', 0);
$form->roll->input->active->setPlaintext(true);
$form->roll->input->active->setDisplayColumn(true);

$form->roll->setSaveTool(false);
$form->roll->setDeleteTool(false);

echo $form->roll->getForm();
