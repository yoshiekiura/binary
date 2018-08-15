<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (config('plan_a', 'reward_use')!='1')
{
	echo msg('Maaf, fitur reward tidak diaktifkan', 'danger');
}else{
	$is_multi_reward = count(config('plan_a', 'reward_list')) > 1 ? true : false;

	/* JIKA DIAKSES UNTUK POPUP MAKA $id > 0 */
	$id   = @intval($_GET['id']);
	$form = _lib('pea', 'bin_reward_member');
	if (empty($id))
	{
		$form->initSearch();

		if ($is_multi_reward)
		{
			$form->search->addInput('reward_id','selecttable');
			$form->search->input->reward_id->addOption('--pilih type--', '');
			$form->search->input->reward_id->setReferenceTable('`bin_reward` ORDER BY `id` ASC');
			$form->search->input->reward_id->setReferenceField( 'name', 'id' );
		}

		$form->search->addInput('keyword','keyword');
		$form->search->input->keyword->setTitle('Masukkan Serial ID');
		$form->search->input->keyword->addSearchField('username,reward_name', false);

		$form->search->addExtraField('received', '0');
		$form->search->addExtraField('active', '1');

		$add_sql = $form->search->action();
		$keyword = $form->search->keyword();
		echo $form->search->getForm();
		$member['username'] = '';
	}else{
		$add_sql = 'WHERE `bin_id`='.$id.' AND `active`=1';
		$keyword = array(
			'bin_id' => $id,
			'active' => 1
			);
		$member = bin_fetch_id($id);
	}

	if (@intval($_GET['list_id']))
	{
		$add_sql .= ' AND `list_id`='.$_GET['list_id'].'';
	}
	/* JIKA ACCEPTANCE DI SUBMIT MAKA DI PROSES KE FINANCE */
	if (empty($id) && !empty($_POST['roll_received']) && !empty($_POST['roll_list_id']))
	{
		$tmp_reward = array();
		foreach ($_POST['roll_received'] as $i => $v)
		{
			$list_id = $_POST['roll_list_id'][$i];
			$data    = $db->getRow("SELECT * FROM `bin_reward_member` WHERE `list_id`={$list_id} AND `active`=1");
			if (!empty($data) && empty($data['received']))
			{
				if (empty($tmp_reward[$data['reward_id']]))
				{
					$tmp_reward[$data['reward_id']] = $db->getRow("SELECT * FROM `bin_reward` WHERE `id`={$data['reward_id']}");
				}
				$reward = $tmp_reward[$data['reward_id']];
				if (!empty($reward))
				{
					// Penyerahan reward
					bin_finance(
						$data['bin_id'],
						12,
						$data['reward_amount'],
					  array(
							'username' => $data['username'],
							'reward'   => $reward['name']
						)
					);
				}
			}
		}
	}
	$form->initRoll("{$add_sql} ORDER BY list_id DESC", 'list_id' );

	$form->roll->addInput('header','header');
	$form->roll->input->header->setTitle('Reward Acceptance '.$member['username']);

	if (empty($keyword['bin_id']))
	{
		$form->roll->addInput('member','multiinput');
		$form->roll->input->member->setTitle('Member');
		$form->roll->input->member->addInput('member1', 'editlinks');
		$form->roll->input->member->addInput('member2', 'sqllinks');

		$form->roll->input->member1->setCaption( '' );
		$form->roll->input->member1->setModal();
		$form->roll->input->member1->setFieldName( 'bin_id AS member1' );
		$form->roll->input->member1->setLinks(array(
			$Bbc->mod['circuit'].'.genealogy'         => icon('fa-sitemap').' Genealogy',
			$Bbc->mod['circuit'].'.bonus'             => icon('fa-usd').' Bonus',
			$Bbc->mod['circuit'].'.reward'            => icon('fa-trophy').' Reward',
			$Bbc->mod['circuit'].'.transfer_history'  => icon('fa-money').' Transfer'
			));

		$form->roll->input->member2->setModal();
		$form->roll->input->member2->setFieldName( 'username AS member2' );
		$form->roll->input->member2->setLinks($Bbc->mod['circuit'].'.reward_list_detail');
	}

	$form->roll->addInput('reward_name','sqlplaintext');
	$form->roll->input->reward_name->setTitle('Reward');

	$form->roll->addInput('accumulate','select');
	$form->roll->input->accumulate->setTitle('Akumulasi');
	$form->roll->input->accumulate->addOption('Iya', '1');
	$form->roll->input->accumulate->addOption('Tidak', '0');
	$form->roll->input->accumulate->setPlaintext(true);
	$form->roll->input->accumulate->setDisplayColumn(false);

	$form->roll->addInput('total_sponsor','sqlplaintext');
	$form->roll->input->total_sponsor->setTitle('Sponsor');
	$form->roll->input->total_sponsor->setNumberFormat();
	$form->roll->input->total_sponsor->setDisplayColumn(false);

	$form->roll->addInput('total_left','sqlplaintext');
	$form->roll->input->total_left->setTitle('Kiri');
	$form->roll->input->total_left->setNumberFormat();
	$form->roll->input->total_left->setDisplayColumn(false);

	$form->roll->addInput('total_right','sqlplaintext');
	$form->roll->input->total_right->setTitle('Kanan');
	$form->roll->input->total_right->setNumberFormat();
	$form->roll->input->total_right->setDisplayColumn(false);

	if (empty($id))
	{
		$form->roll->addInput('received','checkbox');
		$form->roll->input->received->setTitle('Status');
		$form->roll->input->received->setCaption('Received');

		$form->roll->setDisableInput('received', 1); // jika sudah pernah menerima maka di disable
	}else{
		$form->roll->addInput('received','select');
		$form->roll->input->received->setTitle('Status');
		$form->roll->input->received->addOption('Diterima', '1');
		$form->roll->input->received->addOption('Belum Diterima', '0');
		$form->roll->input->received->setPlaintext(true);

		$form->roll->setSaveTool(false);
	}

	$form->roll->setDeleteTool(false);
	echo $form->roll->getForm();
}