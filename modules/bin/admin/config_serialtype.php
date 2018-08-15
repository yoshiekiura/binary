<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea',  'bin_serial_type');
$form->initRoll("WHERE 1 ORDER BY id ASC");

$form->roll->setSaveTool(true);
$form->roll->setDeleteTool(false);

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Tipe Kartu Serial');

$form->roll->addInput('name','text');
$form->roll->input->name->setTitle('Title');
$form->roll->input->name->setRequire();

$form->roll->addInput('price','text');
$form->roll->input->price->setTitle('Harga');
$form->roll->input->price->setRequire('number');

if (config('plan_a', 'serial_flushout_ok'))
{
	$form->roll->addInput('flushout','text');
	$form->roll->input->flushout->setRequire('number');
}else{
	$form->roll->addInput('flushout','sqlplaintext');
	$form->roll->input->flushout->setNumberFormat();
}
if (config('plan_a', 'serial_use')=='1')
{
	$form->roll->onSave('bin_config_serial', array(), true);
	echo $form->roll->getForm();
	if (config('plan_a', 'serial_check')=='1')
	{
		echo msg('Hanya serial yang telah dibeli oleh member saja yang bisa diaktifkan', 'info');
	}
}else{
	echo msg('Maaf, marketplan anda saat ini tidak mengaktifkan fitur tipe serial', 'danger');
}

function bin_config_serial()
{
	global $db, $sys;
	$plan = config('plan_a');
	$data = $db->getAll("SELECT * FROM `bin_serial_type` WHERE 1 ORDER BY `id` ASC");
	// Null kan dahulu value nya sebelum diisi ulang
	$plan['serial_list']     = array();
	$plan['serial_price']    = array();
	$plan['serial_flushout'] = array();
	foreach ($data as $dt)
	{
		$plan['serial_list'][]     = $dt['name'];
		$plan['serial_price'][]    = $dt['price'];
		$plan['serial_flushout'][] = $dt['flushout'];
	}
	$plan['price'] = $plan['serial_price'][0];
	set_config('plan_a', $plan);
	$sys->clean_cache();
}