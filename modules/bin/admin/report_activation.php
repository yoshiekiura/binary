<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea', 'bin_activation');
$form->initSearch();

$form->search->addInput('created','date');
$form->search->input->created->setTitle('Tanggal');
$form->search->input->created->setSearchQueryLike(false);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();
$add_sql .= ' ORDER BY id DESC';

$form->initRoll($add_sql);
$form->roll->setDeleteTool(false);
$form->roll->setSaveTool(false);

$form->roll->addInput('ondate','sqlplaintext');
$form->roll->input->ondate->setTitle('Tanggal');
$form->roll->input->ondate->setDateFormat();

$form->roll->addInput('total','sqlplaintext');
$form->roll->input->total->setTitle('Total');
$form->roll->input->total->setNumberFormat();

$form->roll->addReport();
echo $form->roll->getForm();