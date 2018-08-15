<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$form = _lib('pea',  'bbc_user');
$form->initEdit(!empty($id) ? 'WHERE id='.$id : '');

$form->edit->addInput('header','header');
$form->edit->input->header->setTitle(!empty($id) ? 'Edit Password' : 'Add Password');

$form->edit->addInput('username','sqlplaintext');

$form->edit->addInput('password', 'passwordConfirm');
$form->edit->input->password->actionType='add';
$form->edit->input->password->setRequire('any', 1);

/* FIELD INI DIGUNAKAN SUPAYA FIELD PASSWORD DIATAS BISA DIPROSES OLEH formIsRequire */
$form->edit->addInput('login_time', 'text');
$form->edit->input->login_time->setRequire('number');
$form->edit->input->login_time->setExtra('readonly');
$form->edit->input->login_time->setIsIncludedInUpdateQuery(false);

$form->edit->action();
echo $form->edit->getForm();