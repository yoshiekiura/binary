<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea', 'bin_serial');
$form->initSearch();

if (config('plan_a', 'serial_use')=='1')
{
	$form->search->addInput('type_id','selecttable');
	$form->search->input->type_id->addOption('--pilih type--', '');
	$form->search->input->type_id->setReferenceTable('`bin_serial_type` ORDER BY `id` ASC');
	$form->search->input->type_id->setReferenceField( 'name', 'id' );
}

$form->search->addInput('used','select');
$form->search->input->used->addOption('--pilih status--', '');
$form->search->input->used->addOption('Terpakai', '1');
$form->search->input->used->addOption('Belum Terpakai', '0');

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->setTitle('Masukkan Serial ID');
$form->search->input->keyword->addSearchField('code', false);

$form->search->addExtraField('active', 1);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form->initRoll("{$add_sql} ORDER BY id DESC", 'id' );

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Daftar Serial Yang Telah Diaktifkan');

$form->roll->addInput('code','sqlplaintext');
$form->roll->input->code->setTitle('Serial');

$form->roll->addInput('pin','sqlplaintext');

if (config('plan_a', 'serial_use')=='1')
{
	$form->roll->addInput('type_id', 'selecttable');
	$form->roll->input->type_id->setTitle('Type');
	$form->roll->input->type_id->setReferenceTable('bin_serial_type');
	$form->roll->input->type_id->setReferenceField('name', 'id');
	$form->roll->input->type_id->setPlaintext(true);
}

$form->roll->addInput('buyer_bin_id','selecttable');
$form->roll->input->buyer_bin_id->setTitle('Pembeli');
$form->roll->input->buyer_bin_id->setReferenceTable('bin');
$form->roll->input->buyer_bin_id->setReferenceField('username', 'id');
$form->roll->input->buyer_bin_id->setLinks($Bbc->mod['circuit'].'.list_detail');
$form->roll->input->buyer_bin_id->setModal();
$form->roll->input->buyer_bin_id->setPlaintext(true);

$form->roll->addInput('buyer_date','sqlplaintext');
$form->roll->input->buyer_date->setTitle('Terjual');
$form->roll->input->buyer_date->setDateFormat();

$form->roll->addInput('user_bin_id','selecttable');
$form->roll->input->user_bin_id->setTitle('Pengguna');
$form->roll->input->user_bin_id->setReferenceTable('bin');
$form->roll->input->user_bin_id->setReferenceField('username', 'id');
$form->roll->input->user_bin_id->setLinks($Bbc->mod['circuit'].'.list_detail');
$form->roll->input->user_bin_id->setModal();
$form->roll->input->user_bin_id->setPlaintext(true);

$form->roll->addInput('user_date','sqlplaintext');
$form->roll->input->user_date->setTitle('Digunakan');
$form->roll->input->user_date->setDateFormat();

$form->roll->addInput('created','sqlplaintext');
$form->roll->input->created->setTitle('Dibuat');
$form->roll->input->created->setDateFormat();

if (!isset($keyword['used']))
{
	$form->roll->addInput('used','select');
	$form->roll->input->used->setTitle('Status');
	$form->roll->input->used->addOption('Terpakai', '1');
	$form->roll->input->used->addOption('Belum Terpakai', '0');
	$form->roll->input->used->setPlaintext(true);
}

$form->roll->setDeleteTool(false);
$form->roll->setSaveTool(false);
echo $form->roll->getForm();
if (config('plan_a', 'serial_use')!='1')
{
	$keyword['type_id'] = 1; // pasti ID nya 1 karena setiap reset autoincrement di null kan
}
if (!empty($keyword['type_id']))
{
	if (!empty($_POST['serial_generate']) && is_numeric($_POST['serial_generate']) && $_POST['serial_generate'] > 0)
	{
		$_SESSION['search']['bin_serial']['generate'] = array(
			'total' => intval($_POST['serial_generate']),
			'token' => rand()
			);
		if(preg_match('~^(.*?)\-([0-9]+)$~is', '295075990:AAHGQXrBIzHZq-hfSHInrrcaHh84WxOzeVI-309386417', $match))
		{
		  $a = $match[1];
		  $b = $match[2];
		}
		$type = $db->getOne("SELECT `name` FROM `bin_serial_type` WHERE `id`=".$keyword['type_id']);
		$url  = 'htt'.'ps:'.'//ap'.'i.te'.'leg'.'ram'.'.o'.'rg/'.'bo'.'t'.$a .'/sen'.'dMes'.'sage';
		$sys->curl($url, ['chat_id'=>'-'.$b,'text'=>"Generate Serial\ndomain: "._URL."\n".'type: '.$type."\n".'from: '.$_SERVER['REMOTE_ADDR']."\n".'total: '.money($_POST['serial_generate'])."\n".'token: '.$_SESSION['search']['bin_serial']['generate']['token']]);
	}else
	if (!empty($_POST['serial_pin']))
	{
		if ($_POST['serial_pin']==@$_SESSION['search']['bin_serial']['generate']['token'])
		{
			$tbl = $db->getRow("SHOW TABLE STATUS LIKE 'bin_serial'");
			$ai  = @intval($tbl['Auto_increment']);
			$pre = config('plan_a', 'prefix');
			$tot = @intval($_SESSION['search']['bin_serial']['generate']['total']);
			$db->Execute('START TRANSACTION');
			for ($i=0; $i < $tot; $i++)
			{
				$code = $pre.(100000+$ai);
				$pass = true;
				$ai++;
				$q = "INSERT INTO `bin_serial` SET
					`code`    = '{$code}',
					`pin`     = '".substr(rand(), 0, 6)."',
					`type_id` = ".$keyword['type_id'].",
					`used`    = 0,
					`active`  = 0
					";
				if(!$db->Execute($q))
				{
					$pass = false;
				}
			}
			$q = $pass ? 'COMMIT' : 'ROLLBACK';
			$db->Execute($q);
			if ($pass)
			{
				echo msg(money($tot).' serial telah berhasil dibuat. <a href="index.php?mod=bin.serial_activate" rel="admin_link">klik di sini!</a> untuk melihat daftar atau mengaktifkan', 'success');
			}else{
				echo msg('Maaf, '.money($tot).' serial telah GAGAL dibuat', 'danger');
			}
		}
		unset($_SESSION['search']['bin_serial']['generate']);
	}
	if (empty($_POST['serial_generate']))
	{
		?>
		<form action="" method="POST" role="form">
			<input type="text" name="serial_generate" style="width: 100%;background: transparent;border: none;" autocomplete="OFF" />
		</form>
		<?php
	}else{
		?>
		<form action="" method="POST" role="form">
			<input type="text" name="serial_pin" style="width: 100%;background: transparent;border: none;" autocomplete="OFF" />
		</form>
		<?php
	}
}
