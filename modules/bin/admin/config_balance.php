<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea',  'bin_balance_type');
$form->initRoll("WHERE `active`=1 ORDER BY `id` ASC");

$form->roll->setSaveTool(true);
$form->roll->setDeleteTool(false);

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Pesan transaksi dalam eWallet');

$form->roll->addInput('nama','multiinput');
$form->roll->input->nama->addInput('name', 'sqlplaintext');
$form->roll->input->nama->addInput('delimeter', 'plaintext', '<br />(Contoh: ');
$form->roll->input->nama->addInput('description', 'sqlplaintext');
$form->roll->input->nama->addInput('delimeter2', 'plaintext', ')');

$form->roll->addInput('message','textarea');
$form->roll->input->message->setTitle('Pesan');

echo $form->roll->getForm();
