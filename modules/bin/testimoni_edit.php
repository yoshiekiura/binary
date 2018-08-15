<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$testimonial_id = @intval($_GET['testimonial_id']);
$sql            = $testimonial_id ? 'WHERE `id`='.$testimonial_id : '';

if (empty($user->image))
{
	redirect('bin/testimoni_image?return='.urlencode(seo_uri()));
}
$form1 = _lib('pea','bin_testimonial');
$form1->initEdit($sql);

$form1->edit->addInput('header','header');
$form1->edit->input->header->setTitle(!empty($testimonial_id) ? 'Ubah Testimoni' : 'Tambah Testimoni');

$form1->edit->addInput( 'name', 'text' );
$form1->edit->input->name->setTitle(lang('name'));
$form1->edit->input->name->setDefaultValue($user->name);

$form1->edit->addInput( 'image', 'file' );
$form1->edit->input->image->setTitle(lang('profile'));
$form1->edit->input->image->setImageClick();
$form1->edit->input->image->setDefaultValue($user->image);

$form1->edit->addInput( 'location_id', 'selecttable' );
$form1->edit->input->location_id->setTitle(lang('location'));
$form1->edit->input->location_id->setReferenceTable('bin_location');
$form1->edit->input->location_id->setReferenceField( 'detail', 'id' );
$form1->edit->input->location_id->setAutoComplete(true);
$form1->edit->input->location_id->setDefaultValue(@$Bbc->member['location_id']);

$form1->edit->addInput( 'detail', 'textarea' );
$form1->edit->input->detail->setTitle('Detail Testimonial');

$form1->edit->addInput( 'detail', 'textarea' );
$form1->edit->input->detail->setTitle('Detail Testimonial');
$form1->edit->addExtraField('bin_id', $db->getOne('SELECT `id` FROM `bin` WHERE `user_id`='.$user->id));

$form1->edit->addExtraField('publish', '1');
$form1->edit->onSave('bin_testimoni_save');
if (!empty($testimonial_id))
{
	echo $form1->edit->getForm();
}
function bin_testimoni_save($id)
{
	global $db, $user;
	$data     = $db->getRow("SELECT * FROM `bin_testimonial` WHERE `id`={$id}");
	$location = $db->getRow("SELECT * FROM `bin_location` WHERE id={$data['location_id']}");
	$fields   = array();
	if ($location['detail']!=$data['location_name'])
	{
		$fields[] = "`location_name`='{$location['detail']}'";
	}
	if (!empty($id))
	{
		if (empty($data['image']))
		{
			$fields[] = "`image`='{$user->image}'";
		}
	}
	if (!empty($fields))
	{
		$db->Execute("UPDATE `bin_testimonial` SET ".implode(', ', $fields)." WHERE `id`={$id}");
	}
}