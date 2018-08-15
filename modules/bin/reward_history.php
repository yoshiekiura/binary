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
	$form = _lib('pea', 'bin_reward_member');
	$form->initRoll('WHERE `bin_id`='.$id.' AND `received`=1 AND `active`=1 ORDER BY `list_id` DESC', 'list_id' );

	$form->roll->addInput('header','header');
	$form->roll->input->header->setTitle('Histori Reward Yang Telah Diterima');

	$form->roll->addInput('reward_name','sqllinks');
	$form->roll->input->reward_name->setTitle('Reward');
	$form->roll->input->reward_name->setLinks($Bbc->mod['circuit'].'.reward_list_detail');

	$form->roll->addInput('updated','sqlplaintext');
	$form->roll->input->updated->setTitle('Tanggal');
	$form->roll->input->updated->setDateFormat();

	$form->roll->setDeleteTool(false);
	$form->roll->setSaveTool(false);
	echo $form->roll->getForm();
}
