<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);

if (empty($id))
{
	echo msg("Data Reward Tidak ditemukan");
}else{
	$form = _lib('pea',  'bin_reward');
	$form->initEdit('WHERE `id`="'.$id.'"');

	$form->edit->setSaveTool(true);
	$form->edit->setDeleteTool(false);

	$form->edit->addInput('header','header');
	$form->edit->input->header->setTitle('Edit Reward');

	$form->edit->addInput('name','text');
	$form->edit->input->name->setTitle('Title');
	$form->edit->input->name->setRequire();

	$form->edit->addInput('images','multifile');
	$form->edit->input->images->setTitle('Images');
	$form->edit->input->images->setFolder($Bbc->mod['dir'].'images'.'/'.$id.'/');
	$form->edit->input->images->setFirstField('image');

	$form->edit->addInput('description','textarea');
	$form->edit->input->description->setHtmlEditor();

	$form->edit->addInput('amount','text');
	$form->edit->input->amount->setTitle('amount ( nilai reward dalam bentuk uang )');
	$form->edit->input->amount->setRequire('number');
	$form->edit->input->amount->setNumberformat();

	$form->edit->addInput('total_sponsor','text');
	$form->edit->input->total_sponsor->setRequire('number');
	$form->edit->input->total_sponsor->setNumberformat();


	$form->edit->addInput('downline','multiinput');
	$form->edit->input->downline->setTitle('Total Downline ( Kiri - Kanan )');
	$form->edit->input->downline->addInput('total_left', 'text', 'Total Kiri');
	$form->edit->input->downline->addInput('total_right', 'text', 'Total Kanan');

	$form->edit->input->total_left->setTitle('Total Kiri');
	$form->edit->input->total_left->setRequire('number');
	$form->edit->input->total_left->setNumberformat();

	$form->edit->input->total_right->setTitle('Total Kanan');
	$form->edit->input->total_right->setRequire('number');
	$form->edit->input->total_right->setNumberformat();

	$form->edit->addInput('level_id','selecttable');
	$form->edit->input->level_id->setTitle('Minimum Level');
	$form->edit->input->level_id->addOption('Any Level', 0);
	$form->edit->input->level_id->setReferenceTable('bin_level ORDER BY id ASC');
	$form->edit->input->level_id->setReferenceField('name', 'id');

	if (config('plan_a', 'serial_use'))
	{
		$form->edit->addInput('serial_type_id','selecttable');
		$form->edit->input->serial_type_id->setTitle('Minimum Serial');
		$form->edit->input->serial_type_id->addOption('Any Serial', 0);
		$form->edit->input->serial_type_id->setReferenceTable('bin_serial_type ORDER BY id ASC');
		$form->edit->input->serial_type_id->setReferenceField('name', 'id');
	}

	$form->edit->addInput('accumulate','checkbox');
	$form->edit->input->accumulate->setTitle('Akumulasi');
	$form->edit->input->accumulate->setCaption('Yes');
	$form->edit->input->accumulate->addTip('Jika anda centang, maka reward selanjutnya bisa didapatkan kembali dengan mengakumulasi perhitungan sebelumnya. Tetapi jika tidak dicentang, maka perhitungan akan di reset setiap kali member mendapatkan reward');

	$form->edit->addInput('active','checkbox');
	$form->edit->input->active->setTitle('Status');
	$form->edit->input->active->setCaption('Active');

	if (config('plan_a', 'reward_use')=='1')
	{
		$form->edit->onSave('bin_config_reward');
		echo $form->edit->getForm();
		if (config('plan_a', 'reward_auto')=='1')
		{
			$msg = '<b>Auto Reward:</b> Member akan mendapatkan reward ketika sudah memenuhi persyaratan tanpa harus klaim';
		}else{
			$msg = 'ketika member telah memenuhi persyaratan, member tersebut tidak akan masuk ke dalam daftar reward jika tidak meng-klaim reward terlebih dahulu';
		}
		echo msg($msg, 'info');
	}else{
		echo msg('Maaf, marketplan anda saat ini tidak mengaktifkan fitur reward', 'danger');
	}
}

function bin_config_reward()
{
	global $db, $sys;
	$plan = config('plan_a');
	$plan['reward_list'] = $db->getCol("SELECT `name` FROM `bin_reward` WHERE 1 ORDER BY `id` ASC");
	set_config('plan_a', $plan);
	$sys->clean_cache();
}