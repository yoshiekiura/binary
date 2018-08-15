<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if (!empty($id))
{
	if (!empty($_GET['is_ajax']))
	{
		unset($_GET['return'], $_GET['_return']);
	}
	$btn_link = '';
	if (_ADMIN!='')
	{
		ob_start();
		$ret = !empty($_GET['return']) ? '&return='.urlencode($_GET['return']) : '&return='.urlencode('index.php?mod=bin.list');
		?>
		<a href="<?php echo 'index.php?mod=bin.list_password&id='.$id.$ret; ?>" rel="admin_link" class="btn btn-xs btn-warning pull-right" style="margin: 0 5px;"><?php echo icon('fa-unlock-alt'); ?> Change Password</a>
		<a href="<?php echo 'index.php?mod=bin.list_edit&id='.$id.$ret; ?>" rel="admin_link" class="btn btn-xs btn-default pull-right" style="margin: 0 5px;"><?php echo icon('fa-user-circle'); ?> Change Profile</a>
		<?php
		$btn_link = ob_get_contents();
		ob_end_clean();
	}
	$form = _lib('pea',  'bin AS b LEFT JOIN `bbc_account` AS c ON (b.user_id=c.user_id)');
	$form->initEdit(!empty($id) ? 'WHERE b.id='.$id : '', 'b.id');
	$form->edit->setColumn(2);

	$form->edit->addInput('header','header');
	$form->edit->input->header->setTitle(!empty($id) ? 'Detail Member '.$btn_link : 'Add Member');

	$form->edit->addInput('username','sqlplaintext');
	$form->edit->input->username->setTitle('Serial');
	$form->edit->input->username->setFieldName('b.username AS username');

	$r = config('plan_a', 'serial_list');
	if (count($r) > 1)
	{
		$form->edit->addInput( 'serial_type_id', 'selecttable' );
		$form->edit->input->serial_type_id->setTitle('Tipe Serial');
		$form->edit->input->serial_type_id->setReferenceTable('bin_serial_type');
		$form->edit->input->serial_type_id->setReferenceField( 'name', 'id' );
		$form->edit->input->serial_type_id->setPlaintext( true );
	}

	$form->edit->addInput('name','sqlplaintext');
	$form->edit->input->name->setTitle('Nama');
	$form->edit->input->name->setFieldName('c.name AS name');

	$form->edit->addInput('total_sponsor','sqlplaintext');
	$form->edit->input->total_sponsor->setTitle('Total Sponsor');
	$form->edit->input->total_sponsor->setNumberFormat();

	$form->edit->addInput('downline','multiinput');
	$form->edit->input->downline->setTitle('Total Downline');
	$form->edit->input->downline->addInput('total_downline', 'sqlplaintext');
	$form->edit->input->downline->addInput('total_downline2', 'plaintext', ' (');
	$form->edit->input->downline->addInput('total_left', 'sqlplaintext');
	$form->edit->input->downline->addInput('total_downline3', 'plaintext', 'kiri, ');
	$form->edit->input->downline->addInput('total_right', 'sqlplaintext');
	$form->edit->input->downline->addInput('total_downline4', 'plaintext', 'kanan )');
	$form->edit->input->total_downline->setNumberFormat();
	$form->edit->input->total_left->setNumberFormat();
	$form->edit->input->total_right->setNumberFormat();

	$form->edit->addInput('depth','multiinput');
	$form->edit->input->depth->setTitle('Kedalaman Downline');
	$form->edit->input->depth->addInput('depth_left', 'sqlplaintext');
	$form->edit->input->depth->addInput('depth_left2', 'plaintext', ' kiri,');
	$form->edit->input->depth->addInput('depth_right', 'sqlplaintext');
	$form->edit->input->depth->addInput('depth_right2', 'plaintext', 'Kanan');
	$form->edit->input->depth_left->setNumberFormat();
	$form->edit->input->depth_right->setNumberFormat();

	$form->edit->addInput('level','multiinput');
	$form->edit->input->level->setTitle('Level Jaringan');
	$form->edit->input->level->addInput('depth_upline', 'sqlplaintext');
	$form->edit->input->level->addInput('depth_upline2', 'plaintext', ' Titik,');
	$form->edit->input->level->addInput('depth_sponsor', 'sqlplaintext');
	$form->edit->input->level->addInput('depth_sponsor2', 'plaintext', 'Sponsor');
	$form->edit->input->depth_upline->setNumberFormat();
	$form->edit->input->depth_sponsor->setNumberFormat();

	$form->edit->addInput('location_name','sqlplaintext');
	$form->edit->input->location_name->setTitle('Lokasi');

	$form->edit->addInput('location_address','sqlplaintext');
	$form->edit->input->location_address->setTitle('Alamat');

	$form->edit->addInput('location_latlong','sqlplaintext');
	$form->edit->input->location_latlong->setTitle('Latitude Longitude');

	$form->edit->addInput('serial_pin','sqlplaintext', 2);
	$form->edit->input->serial_pin->setTitle('PIN');

	if (_ADMIN!='')
	{
		$form->edit->addInput('balance','sqlplaintext', 2);
		$form->edit->input->balance->setTitle('Saldo');
		$form->edit->input->balance->setNumberFormat();
	}

	$form->edit->addInput('sponsor_id','selecttable', 2);
	$form->edit->input->sponsor_id->setTitle('Sponsor');
	$form->edit->input->sponsor_id->setReferenceTable('bin');
	$form->edit->input->sponsor_id->setReferenceField('username', 'id');
	$form->edit->input->sponsor_id->setPlaintext(true);

	$form->edit->addInput('upline_id','selecttable', 2);
	$form->edit->input->upline_id->setTitle('Upline');
	$form->edit->input->upline_id->setReferenceTable('bin');
	$form->edit->input->upline_id->setReferenceField('username', 'id');
	$form->edit->input->upline_id->setPlaintext(true);

	$form->edit->addInput('position','select', 2);
	$form->edit->input->position->setTitle('Posisi');
	$form->edit->input->position->addOption('Kanan', '1');
	$form->edit->input->position->addOption('Kiri', '0');
	$form->edit->input->position->setPlaintext(true);

	$params  = user_field_group(3);
	$removes = ['serial', 'pin', 'sponsor', 'upline', 'position', 'location_id', 'location_address', 'location_latlong'];
	foreach ($params as $i => $dt)
	{
		if (in_array($dt['title'], $removes))
		{
			unset($params[$i]);
		}else{
			$params[$i]['type'] = 'plain';
			unset($params[$i]['tips']);
		}
	}
	$form->edit->addInput('params', 'params', 2);
	$form->edit->input->params->setParams($params);
	$form->edit->input->params->setEncode(true);

	$form->edit->action();
	$form->edit->setSaveTool(false);
	$form->edit->setResetTool(false);
	echo $form->edit->getForm();
}