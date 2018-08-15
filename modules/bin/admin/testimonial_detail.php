<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$form = _lib('pea',  'bin_testimonial');
$form->initEdit(!empty($id) ? 'WHERE id='.$id : '');

$form->edit->addInput('detail','sqlplaintext');
$form->edit->input->detail->setTitle('');

$form->edit->setSaveTool(false);
$form->edit->setResetTool(false);

echo $form->edit->getForm();