<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$form = _lib('pea',  'bin_testimonial');
$form->initEdit(!empty($id) ? 'WHERE id='.$id : '');

$form->edit->addInput('header','header');
$form->edit->input->header->setTitle(!empty($id) ? 'Edit Testimonial' : 'Add Testimonial');

$form->edit->addInput('name','text');
$form->edit->input->name->setTitle('Name');

$form->edit->addInput('image','file');
$form->edit->input->image->setTitle('Image');
$form->edit->input->image->setImageClick();

$form->edit->addInput( 'location_id', 'selecttable' );
$form->edit->input->location_id->setTitle('Kecamatan / Kota');
$form->edit->input->location_id->setReferenceTable('bin_location');
$form->edit->input->location_id->setReferenceField( 'detail', 'id' );
$form->edit->input->location_id->setAutoComplete(true);

$form->edit->addInput('detail','textarea');
$form->edit->input->detail->setTitle('Detail Testimonial');
$form->edit->input->detail->setHtmlEditor();
$form->edit->input->detail->setToolbar('basic');

$form->edit->addInput( 'created', 'datetime' );
$form->edit->input->created->setPlaintext(true);

$form->edit->addInput( 'updated', 'datetime' );
$form->edit->input->updated->setPlaintext(true);

$form->edit->addInput( 'publish', 'checkbox' );
$form->edit->input->publish->setCaption('display this testimonial in public');

$form->edit->onSave('bin_testimoni_save');
echo $form->edit->getForm();
function bin_testimoni_save()
{
	global $db, $user, $id;
	$data     = $db->getRow("SELECT * FROM `bin_testimonial` WHERE `id`={$id}");
	$location = $db->getRow("SELECT * FROM `bin_location` WHERE id={$data['location_id']}");
	$fields   = array();
	if ($location['detail']!=$data['location_name'])
	{
		$fields[] = "`location_name`='{$location['detail']}'";
	}
	if (!empty($fields))
	{
		$db->Execute("UPDATE `bin_testimonial` SET ".implode(', ', $fields)." WHERE `id`={$id}");
	}
}