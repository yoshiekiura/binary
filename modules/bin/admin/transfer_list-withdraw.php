<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$separator    = ' >= ';
$min_transfer = intval($config['min_transfer']);

$form = _lib('pea', 'bin_withdraw');
$form->initRoll("WHERE `done`=0 ORDER BY `created` ASC");

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Daftar Yang Belum Ditransfer');

$form->roll->addInput('member','multiinput');
$form->roll->input->member->setTitle('Username');
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
$form->roll->input->member2->setLinks($Bbc->mod['circuit'].'.list_detail');

$form->roll->addInput('name','sqlplaintext');
$form->roll->input->name->setTitle('nama');
$form->roll->input->name->setFieldName('name AS name');

$form->roll->addInput('bank_name','sqlplaintext');
$form->roll->input->bank_name->setTitle('Bank');

$form->roll->addInput('bank_no','sqlplaintext');
$form->roll->input->bank_no->setTitle('No Rek');

$form->roll->addInput('total','sqlplaintext');
$form->roll->input->total->setTitle('Amount');
$form->roll->input->total->setNumberFormat();

$form->roll->setSaveTool(false);
$form->roll->setDeleteTool(false);

if (!empty($_POST['transfer']))
{
	$is_surcharge = !empty($config['surcharge']) || !empty($config['surcharge_npwp']) || !empty($config['surcharge_npwp_no']);
	if ($_POST['transfer'] == 'download')
	{
		_func('download');
		if ($is_surcharge)
		{
			$q = "SELECT `bin_id` AS `memberID`, `username`, `name` AS `nama`, `bank_name` AS `bank`, `bank_no` AS `no_rek`, `total`, `surcharge` AS `potongan`, `transfer`, `done` FROM `bin_withdraw` WHERE `done`=0 ORDER BY `created` ASC";
		}else{
			$q = "SELECT `bin_id` AS `memberID`, `username`, `name` AS `nama`, `bank_name` AS `bank`, `bank_no` AS `no_rek`, `total`, `done` FROM `bin_withdraw` WHERE `done`=0 ORDER BY `created` ASC";
		}
		$r = $db->getAll($q);
		if (!empty($r))
		{
			download_excel('Transfer ('.date('Y-m-d').')', $r, date('H-i-s'));
		}else{
			echo msg('Maaf, tidak ada file yg bisa di download', 'danger');
		}
	}else
	if ($_POST['transfer']=='upload')
	{
		$error = '';
		if (@is_uploaded_file($_FILES['excel']['tmp_name']))
		{
			// Check nama file
			if (substr($_FILES['excel']['name'], -5)=='.xlsx')
			{
				// Save data
				$dst_file = _CACHE.'transfer.xlsx';
				if (@move_uploaded_file($_FILES['excel']['tmp_name'], $dst_file))
				{
					// Check file
					$output = _lib('excel')->read($dst_file)->sheet(1)->fetch();
					@unlink($dst_file);
					if (!empty($output) && is_array($output))
					{
						// Check column
						$headers = array(
							'A' => 'MemberID',
							'B' => 'Username',
							'C' => 'Nama',
							'D' => 'Bank',
							'E' => 'No Rek',
							'F' => 'Total',
							'G' => 'Done'
							);
						if ($is_surcharge)
						{
							$headers['G'] = 'Potongan';
							$headers['H'] = 'Transfer';
							$headers['I'] = 'Done';
						}
						if (bin_check_transfer_file($headers, $output[1]))
						{
							// Check which done=1
							$data = array();
							foreach ($output as $i => $row)
							{
								$G = $is_surcharge ? 'I' : 'G';
								if ($row[$G] == '1')
								{
									$data[] = array(
										'id'        => $row['A'],
										'username'  => $row['B'],
										'name'      => $row['C'],
										'bank_name' => $row['D'],
										'bank_no'   => $row['E'],
										'total'     => $row['F']
										);
								}
							}
							if (!empty($data))
							{
								// Execute
								foreach ($data as $dt)
								{
									$db->Execute("UPDATE `bin_withdraw` SET `done`=1 WHERE `bin_id`=".$dt['id']);
									if ($dt['total'] > 0)
									{
										$params = array(
											'bank_info' => 'ditransfer ke rekening '.$dt['bank_name'].' di no.rek '.$dt['bank_no']
											);
										// bin_finance($dt['id'], 2, $dt['total'], $params);
										_class('async')->run('bin_finance', [$dt['id'], 2, $dt['total'], $params]);
									}
								}
								echo msg('Data transfer yang anda upload telah dieksekusi', 'success');
							}else{
								$error = 'Maaf, sepertinya anda belum menandai mana yang sudah ditransfer atau belum. atau bisa juga data masih kosong';
							}
						}else{
							$error = 'Maaf, mohon tidak melakukan perubahan pada kolom selain kolom Total dan Done';
						}
					}else{
						$error = 'Maaf, file yang anda upload tidak terbaca';
					}
				}else{
					$error = 'Maaf, file yang anda upload gagal disimpan';
				}
			}else{
				$error = 'Mohon upload file dengan format yang benar (.xlsx)';
			}
		}else{
			$error = 'Silahkan upload file hasil download dari form "Export Data Transfer" di bawah!';
		}
		if (!empty($error))
		{
			echo msg($error, 'danger');
		}
	}
}
echo $form->roll->getForm();

if ($db->Affected_rows() > 0)
{
	?>
	<div class="col-md-6 col-sm-6 col-xs-6">
		<form action="" method="POST" class="form" role="form">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Export Data Transfer</h3>
				</div>
				<div class="panel-body">
					<div class="help-block">
						Untuk melakukan transfer silahkan download semua data transfer tertunda diatas kedalam file excel. Lalu ubah kolom "Done" menjadi 1 <br/ >
						Jika proses transfer sudah selesai dengan mengubah kolom "Done" menjadi 1 maka upload kembali ke form setelah ini. Untuk diproses oleh system
						dengan mem-verifikasi bahwa transfer tersebut telah dilakukan
					</div>
				</div>
				<div class="panel-footer">
					<button type="submit" name="transfer" value="download" class="btn btn-default"><?php echo icon('fa-file-excel-o') ?> Download Data</button>
				</div>
			</div>
		</form>
	</div>
	<div class="col-md-6 col-sm-6 col-xs-6">
		<form action="" method="POST" class="form" role="form" enctype="multipart/form-data">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Upload Data Transfer</h3>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label>Upload file hasil transfer</label>
						<input type="file" name="excel" class="form-control" placeholder="upload di sini!" />
						<div class="help-block">
							Upload kembali file download dari form sebelumnya setelah di proses
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<button type="submit" name="transfer" value="upload" class="btn btn-default"><?php echo icon('fa-upload') ?> Upload Data</button>
				</div>
			</div>
		</form>
	</div>
	<?php
}
function bin_check_transfer_file($headers, $rows)
{
	$output = true;
	foreach ($headers as $key => $value)
	{
		if ($rows[$key]!=$value)
		{
			$output = false;
			break;
		}
	}
	return $output;
}