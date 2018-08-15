<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea','bin_testimonial');
$form->initRoll('WHERE 1 ORDER BY id DESC','id');

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle(lang('Testimonial Member'));

$form->roll->addInput('image','file');
$form->roll->input->image->setTitle('Image');
$form->roll->input->image->setImageClick();
$form->roll->input->image->setPlaintext(true);
$form->roll->input->image->setDisplayColumn(false);

$form->roll->addInput('name','sqllinks');
$form->roll->input->name->setLinks($Bbc->mod['circuit'].'.testimonial_edit');
$form->roll->input->name->setDisplayColumn(true);

$form->roll->addInput('location_name','sqlplaintext');
$form->roll->input->location_name->setTitle('Location');
$form->roll->input->location_name->setDisplayColumn(false);

$form->roll->addInput('detail','sqllinks');
$form->roll->input->detail->setTitle('Testimoni');
$form->roll->input->detail->setLinks($Bbc->mod['circuit'].'.testimonial_detail');
$form->roll->input->detail->setModal();
$form->roll->input->detail->setSubStr(0, 80);

$form->roll->addInput('created','sqlplaintext');
$form->roll->input->created->setTitle('Tanggal');
$form->roll->input->created->setDateFormat();
$form->roll->input->created->setDisplayColumn(true);

$form->roll->addInput( 'publish', 'checkbox' );
$form->roll->input->publish->setTitle('Publish');
// $form->roll->input->publish->setCaption('');

// $form->roll->setDeleteTool(false);
// $form->roll->setSaveTool(false);
$form->roll->action();
echo $form->roll->getForm();
