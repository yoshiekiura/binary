<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if (!empty($id))
{
	$sys->set_layout('blank');
	$bin = _class('bin');
	$bin->setMaxlevel(3);
	$out = $bin->fetch($id);
	if (!empty($out))
	{
		if (@$_GET['type']=='json')
		{
			output_json($out);
		}else{
			echo $bin->child($out['downline'], $out['current']);
		}
		die();
	}
}