<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea', 'bin');
$form->initSearch();
$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('username, location_name, name', false);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();

echo $form->search->getForm();

$form = _lib('pea', 'bin' );
$form->initRoll($add_sql.' ORDER BY id DESC', 'id');

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Laporan Data Member');

$form->roll->addInput('member','multiinput');
$form->roll->input->member->setTitle('Member');
$form->roll->input->member->addInput('member1', 'editlinks');
$form->roll->input->member->addInput('member2', 'sqllinks');

$form->roll->input->member1->setCaption( '' );
$form->roll->input->member1->setModal();
$form->roll->input->member1->setFieldName( 'id AS member1' );
$form->roll->input->member1->setLinks(array(
	$Bbc->mod['circuit'].'.genealogy'        => icon('fa-sitemap').' Genealogy',
	$Bbc->mod['circuit'].'.bonus'            => icon('fa-usd').' Bonus',
	$Bbc->mod['circuit'].'.reward'           => icon('fa-trophy').' Reward',
	$Bbc->mod['circuit'].'.transfer_history' => icon('fa-money').' Transfer'
	));

$form->roll->input->member2->setModal();
$form->roll->input->member2->setFieldName( 'username AS member2' );
$form->roll->input->member2->setLinks($Bbc->mod['circuit'].'.list_detail');

$form->roll->addInput('name','sqlplaintext');
$form->roll->input->name->setTitle( 'Nama' );

$form->roll->addInput('sponsor','multiinput');
$form->roll->input->sponsor->setTitle('Sponsor');
$form->roll->input->sponsor->addInput('sponsor1', 'editlinks');
$form->roll->input->sponsor->addInput('sponsor2', 'selecttable');

$form->roll->input->sponsor1->setCaption( '' );
$form->roll->input->sponsor1->setModal();
$form->roll->input->sponsor1->setFieldName( 'sponsor_id AS sponsor1' );
$form->roll->input->sponsor1->setLinks(array(
	$Bbc->mod['circuit'].'.genealogy'        => icon('fa-sitemap').' Genealogy',
	$Bbc->mod['circuit'].'.bonus'            => icon('fa-usd').' Bonus',
	$Bbc->mod['circuit'].'.reward'           => icon('fa-trophy').' Reward',
	$Bbc->mod['circuit'].'.transfer_history' => icon('fa-money').' Transfer'
	));

$form->roll->input->sponsor2->setReferenceTable('bin');
$form->roll->input->sponsor2->setReferenceField('username', 'id');
$form->roll->input->sponsor2->setFieldName( 'sponsor_id AS sponsor2' );
$form->roll->input->sponsor2->setLinks($Bbc->mod['circuit'].'.list_detail');
$form->roll->input->sponsor2->setExtra('rel="editlinksmodal"');
$form->roll->input->sponsor2->setPlaintext(true);

$form->roll->addInput('upline','multiinput');
$form->roll->input->upline->setTitle('Upline');
$form->roll->input->upline->addInput('upline1', 'editlinks');
$form->roll->input->upline->addInput('upline2', 'selecttable');

$form->roll->input->upline1->setCaption( '' );
$form->roll->input->upline1->setModal();
$form->roll->input->upline1->setFieldName( 'upline_id AS upline1' );
$form->roll->input->upline1->setLinks(array(
	$Bbc->mod['circuit'].'.genealogy'        => icon('fa-sitemap').' Genealogy',
	$Bbc->mod['circuit'].'.bonus'            => icon('fa-usd').' Bonus',
	$Bbc->mod['circuit'].'.reward'           => icon('fa-trophy').' Reward',
	$Bbc->mod['circuit'].'.transfer_history' => icon('fa-money').' Transfer'
	));

$form->roll->input->upline2->setReferenceTable('bin');
$form->roll->input->upline2->setReferenceField('username', 'id');
$form->roll->input->upline2->setFieldName( 'upline_id AS upline2' );
$form->roll->input->upline2->setLinks($Bbc->mod['circuit'].'.list_detail');
$form->roll->input->upline2->setExtra('rel="editlinksmodal"');
$form->roll->input->upline2->setPlaintext(true);


$form->roll->addInput('created','sqlplaintext');
$form->roll->input->created->setTitle('Gabung');
$form->roll->input->created->setDateFormat();

$form->roll->addInput('active','select');
$form->roll->input->active->setTitle('Status');
$form->roll->input->active->addOption('Active', '1');
$form->roll->input->active->addOption('Inactive', '0');
$form->roll->input->active->setPlaintext(true);

$form->roll->setSaveTool(false);
$form->roll->setDeleteTool(false);

echo $form->roll->getForm();
