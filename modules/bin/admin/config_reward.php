<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');
$form = _lib('pea',  'bin_reward');
$form->initRoll("WHERE 1 ORDER BY id ASC");

$form->roll->setSaveTool(true);
$form->roll->setDeleteTool(false);

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Reward List');

$form->roll->addInput('image','file');
$form->roll->input->image->setTitle('Image');
$form->roll->input->image->setFolder($Bbc->mod['dir'].'images'.'/');
$form->roll->input->image->setFieldName('CONCAT(id,"/",image) AS image');
$form->roll->input->image->setImageClick(true);
$form->roll->input->image->setPlainText(true);

$form->roll->addInput('name','sqllinks');
$form->roll->input->name->setTitle('Title');
$form->roll->input->name->setPlainText(true);
$form->roll->input->name->setFieldName('name');
$form->roll->input->name->setLinks($Bbc->mod['circuit'].'.config_reward_edit');

$form->roll->addInput('amount','text');
$form->roll->input->amount->setRequire('number');
$form->roll->input->amount->setNumberformat();

$form->roll->addInput('total_sponsor','text');
$form->roll->input->total_sponsor->setTitle('sponsor');
$form->roll->input->total_sponsor->setRequire('number');
$form->roll->input->total_sponsor->setNumberformat();

$form->roll->addInput('total_left','text');
$form->roll->input->total_left->setTitle('Kiri');
$form->roll->input->total_left->setRequire('number');
$form->roll->input->total_left->setNumberformat();

$form->roll->addInput('total_right','text');
$form->roll->input->total_right->setTitle('Kanan');
$form->roll->input->total_right->setRequire('number');
$form->roll->input->total_right->setNumberformat();

$form->roll->addInput('level_id','selecttable');
$form->roll->input->level_id->setTitle('Min. Level');
$form->roll->input->level_id->addOption('Any Level', 0);
$form->roll->input->level_id->setReferenceTable('bin_level ORDER BY id ASC');
$form->roll->input->level_id->setReferenceField('name', 'id');

if (config('plan_a', 'serial_use'))
{
	$form->roll->addInput('serial_type_id','selecttable');
	$form->roll->input->serial_type_id->setTitle('Min. Serial');
	$form->roll->input->serial_type_id->addOption('Any Serial', 0);
	$form->roll->input->serial_type_id->setReferenceTable('bin_serial_type ORDER BY id ASC');
	$form->roll->input->serial_type_id->setReferenceField('name', 'id');
}

$form->roll->addInput('accumulate','checkbox');
$form->roll->input->accumulate->setTitle('Akumulasi');
$form->roll->input->accumulate->setCaption('Yes');

$form->roll->addInput('active','checkbox');
$form->roll->input->active->setTitle('Status');
$form->roll->input->active->setCaption('Active');

if (config('plan_a', 'reward_use')=='1')
{
	$form->roll->onSave('bin_config_reward', array(), true);
	echo $form->roll->getForm();
	if (config('plan_a', 'reward_auto')=='1')
	{
		$msg = '<b>Auto Reward:</b> Member akan mendapatkan reward ketika sudah memenuhi persyaratan tanpa harus klaim';
	}else{
		$msg = 'ketika member telah memenuhi persyaratan, member tersebut tidak akan masuk ke dalam daftar reward jika tidak meng-klaim reward terlebih dahulu';
	}
	echo msg($msg, 'info');
}else{
	echo msg('Maaf, marketplan anda saat ini tidak mengaktifkan fitur reward', 'warning');
}

function bin_config_reward()
{
	global $db, $sys;
	$plan = config('plan_a');
	$plan['reward_list'] = $db->getCol("SELECT `name` FROM `bin_reward` WHERE 1 ORDER BY `id` ASC");
	set_config('plan_a', $plan);
	$sys->clean_cache();
}