<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id   = @intval($_GET['id']);
$form = _lib('pea', 'bin_balance');
if (empty($id))
{
	$form->initSearch();

	$form->search->addInput('keyword','keyword');
	$form->search->input->keyword->setTitle('Pencarian');
	$form->search->input->keyword->addSearchField('username, title, description, amount, total, created', false);

	$form->search->addExtraField('credit','1');
	$form->search->addExtraField('type_id','2');

	$add_sql = $form->search->action();
	$keyword = $form->search->keyword();
	echo $form->search->getForm();
	$add_sql           .= ' ORDER BY id DESC';
	$member['username'] = '';
}else{
	$add_sql = 'WHERE bin_id='.$id.' AND `credit`=1 ORDER BY id ASC';
	$member  = bin_fetch_id($id);
}

$form->initRoll($add_sql, 'id' );

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('History Transfer '.$member['username']);

if (empty($id))
{
	$form->roll->addInput('bin_id','selecttable');
	$form->roll->input->bin_id->setTitle('Username');
	$form->roll->input->bin_id->setReferenceTable('bin');
	$form->roll->input->bin_id->setReferenceField('username', 'id');
	$form->roll->input->bin_id->setLinks($Bbc->mod['circuit'].'.list_detail');
	$form->roll->input->bin_id->setModal();
	$form->roll->input->bin_id->setPlaintext(true);
}else{
	$count = $db->getOne("SELECT COUNT(*) FROM `bin_balance` {$add_sql}");
	$limit = 100;
	if ($count > $limit)
	{
		$_GET['page'] = ceil($count/$limit);
	}
	$form->roll->setNumRows($limit);
}

$form->roll->addInput('title','sqlplaintext');

$form->roll->addInput('amount','sqlplaintext');
$form->roll->input->amount->setNumberFormat();

$form->roll->addInput('created','sqlplaintext');
$form->roll->input->created->setTitle('Dibuat');
$form->roll->input->created->setDateFormat();

$form->roll->setDeleteTool(false);
$form->roll->setSaveTool(false);
echo $form->roll->getForm();