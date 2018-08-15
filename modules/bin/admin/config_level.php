<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea',  'bin_level');
$form->initRoll("WHERE 1 ORDER BY id ASC");

$form->roll->setSaveTool(true);
$form->roll->setDeleteTool(false);

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Peringkat Member');

$form->roll->addInput('name','text');
$form->roll->input->name->setTitle('Title');
$form->roll->input->name->setRequire();

$form->roll->addInput('total_sponsor','text');
$form->roll->input->total_sponsor->setRequire('number');

$form->roll->addInput('total_left','text');
$form->roll->input->total_left->setTitle('Total Kiri');
$form->roll->input->total_left->setRequire('number');

$form->roll->addInput('total_right','text');
$form->roll->input->total_right->setTitle('Total Kanan');
$form->roll->input->total_right->setRequire('number');

if (config('plan_a', 'serial_use'))
{
	$form->roll->addInput('serial_type_id','selecttable');
	$form->roll->input->serial_type_id->setTitle('Minimum Serial');
	$form->roll->input->serial_type_id->addOption('Any Serial', 0);
	$form->roll->input->serial_type_id->setReferenceTable('bin_serial_type ORDER BY id ASC');
	$form->roll->input->serial_type_id->setReferenceField('name', 'id');
}

$form->roll->onSave('bin_config_level', array(), true);
echo $form->roll->getForm();

function bin_config_level()
{
	global $db, $sys;
	$plan = config('plan_a');
	$plan['level_list'] = $db->getCol("SELECT `name` FROM `bin_level` WHERE 1 ORDER BY `id` ASC");
	set_config('plan_a', $plan);
	$sys->clean_cache();
}