<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if (empty($id))
{
	$id = $Bbc->member['id'];
}else{
	if ($id!=$Bbc->member['id'] && !bin_isDownline($id, $Bbc->member['id']))
	{
		$id = 0;
	}
}
if (empty($id))
{
	echo msg('Maaf, data yang anda akses bukan termasuk dalam jaringan anda', 'danger');
}else{
	$show_form = '';
	if ($id==$Bbc->member['id'])
	{
		$show_form = empty($user->image) ? 2 : 1;
	}
	if ($show_form)
	{
		if ($show_form == 2)
		{
			$show_form = $sys->button('bin/testimoni_image?return='.urlencode(seo_uri()), lang('Tentukan image profile'), 'fa-user');
		}else{
			include 'testimoni_edit.php';
			$show_form = $form1->edit->getForm();
		}
	}

	$form = _lib('pea','bin_testimonial');
	$form->initRoll('WHERE `bin_id`='.$id.' ORDER BY id DESC','id');

	$form->roll->addInput('header','header');
	$form->roll->input->header->setTitle("Data Testimony");

	$form->roll->addInput('detail','sqlplaintext');
	$form->roll->input->detail->setTitle('Testimoni');

	$form->roll->addInput('created','sqlplaintext');
	$form->roll->input->created->setTitle('Tanggal');
	$form->roll->input->created->setDateFormat();

	$form->roll->addInput('edit', 'editlinks');
	$form->roll->input->edit->setTitle( 'Edit' );
	$form->roll->input->edit->setFieldName( 'id AS edit' );
	$form->roll->input->edit->setGetName( 'testimonial_id' );
	$form->roll->input->edit->setLinks($Bbc->mod['circuit'].'.testimoni_edit',icon('edit'));

	// $form->roll->setDeleteTool(false);
	$form->roll->setSaveTool(false);
	$form->roll->action();
	echo $form->roll->getForm();
	echo $show_form;
}