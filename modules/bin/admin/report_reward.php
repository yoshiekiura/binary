<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (config('plan_a', 'reward_use')!='1')
{
	echo msg('Maaf, fitur reward tidak diaktifkan', 'danger');
}else{
	$form = _lib('pea',  'bin_reward_member');
	$form->initSearch();

	$is_multi_reward = count(config('plan_a', 'reward_list')) > 1 ? true : false;
	if ($is_multi_reward)
	{
		$form->search->addInput('reward_id','selecttable');
		$form->search->input->reward_id->addOption('--pilih reward--', '');
		$form->search->input->reward_id->setReferenceTable('`bin_reward` ORDER BY `id` ASC');
		$form->search->input->reward_id->setReferenceField( 'name', 'id' );
	}

	$form->search->addInput('keyword','keyword');
	$form->search->input->keyword->setTitle('Masukkan Kata Kunci');
	$form->search->input->keyword->addSearchField('username,reward_name', false);

	$form->search->addExtraField('received', '1');
	$form->search->addExtraField('active', '1');

	$add_sql = $form->search->action();
	$keyword = $form->search->keyword();
	echo $form->search->getForm();

	$form->initRoll("{$add_sql} ORDER BY `list_id` DESC", 'list_id');

	$form->roll->setSaveTool(false);
	$form->roll->setDeleteTool(false);

	$form->roll->addInput('header','header');
	$form->roll->input->header->setTitle('Data Reward Yang Telah Diterima Member');

	$form->roll->addInput('member','multiinput');
	$form->roll->input->member->setTitle('Username');
	$form->roll->input->member->addInput('member1', 'editlinks');
	$form->roll->input->member->addInput('member2', 'selecttable');
	$form->roll->input->member1->setCaption( '' );
	$form->roll->input->member1->setModal();
	$form->roll->input->member1->setFieldName( 'bin_id AS member1' );
	$form->roll->input->member1->setLinks(array(
		$Bbc->mod['circuit'].'.genealogy'         => icon('fa-sitemap').' Genealogy',
		$Bbc->mod['circuit'].'.bonus'             => icon('fa-usd').' Bonus',
		$Bbc->mod['circuit'].'.reward'            => icon('fa-trophy').' Reward',
		$Bbc->mod['circuit'].'.transfer_history'  => icon('fa-money').' Transfer'
		));
	$form->roll->input->member2->setTitle('Username');
	$form->roll->input->member2->setFieldName('bin_id AS member2');
	$form->roll->input->member2->setReferenceTable('bin');
	$form->roll->input->member2->setReferenceField('username', 'id');
	$form->roll->input->member2->setLinks($Bbc->mod['circuit'].'.list_detail');
	$form->roll->input->member2->setModal();
	$form->roll->input->member2->setPlaintext(true);

	$form->roll->addInput('reward_name','sqlplaintext');
	$form->roll->input->reward_name->setTitle('Reward');

	$form->roll->addInput('reward_amount','sqlplaintext');
	$form->roll->input->reward_amount->setTitle('Amount');
	$form->roll->input->reward_amount->setNumberFormat();

	$form->roll->addInput('total_sponsor','sqlplaintext');
	$form->roll->input->total_sponsor->setTitle('Sponsor');
	$form->roll->input->total_sponsor->setNumberFormat();

	$form->roll->addInput('total_left','sqlplaintext');
	$form->roll->input->total_left->setTitle('Kiri');
	$form->roll->input->total_left->setNumberFormat();

	$form->roll->addInput('total_right','sqlplaintext');
	$form->roll->input->total_right->setTitle('Kanan');
	$form->roll->input->total_right->setNumberFormat();

	$form->roll->addInput('accumulate','select');
	$form->roll->input->accumulate->setTitle('Akumulasi');
	$form->roll->input->accumulate->addOption('Iya', '1');
	$form->roll->input->accumulate->addOption('Tidak', '0');
	$form->roll->input->accumulate->setPlaintext(true);

	$form->roll->addInput('updated','sqlplaintext');
	$form->roll->input->updated->setTitle('Tanggal');
	$form->roll->input->updated->setDateFormat();

	$form->roll->addreport();
	echo $form->roll->getForm();
}