<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add(lang('Tarik Dana'));
if (!empty($plan_a['is_withdraw']))
{
	if ($Bbc->member['balance'] >= $plan_a['min_transfer'])
	{
		if (!empty($Bbc->member['active']))
		{
			$plan_a['surcharge2'] = !empty($user->params['NPWP']) ? $plan_a['surcharge_npwp'] : $plan_a['surcharge_npwp_no'];
			link_js(_LIB.'pea/includes/formIsRequire.js');
			$show_form = true;
			if (!empty($_POST['withdraw']))
			{
				if ($_POST['withdraw'] <= $Bbc->member['balance'])
				{
					if ($_POST['withdraw'] >= $plan_a['min_transfer'])
					{
						$surcharge = bin_charge($_POST['withdraw'], $plan_a['surcharge'].'+'.$plan_a['surcharge2']);
						if (!empty($_POST['withdraw_ok']))
						{
							$id       = $db->getOne("SELECT `id` FROM `bin_withdraw` WHERE `bin_id`={$Bbc->member['id']} AND `done`=0");
							$transfer = $_POST['withdraw']-$surcharge;
							if ($id)
							{
								$q = "UPDATE `bin_withdraw` SET
									`username`  = '{$Bbc->member['username']}',
									`name`      = '{$Bbc->member['name']}',
									`bank_name` = '{$Bbc->member['bank_name']}',
									`bank_no`   = '{$Bbc->member['bank_no']}',
									`total`     = '{$_POST['withdraw']}',
									`surcharge` = '{$surcharge}',
									`transfer`  = '{$transfer}',
									`done`      = 0
									WHERE `id`={$id}";
							}else{
								$q = "INSERT INTO `bin_withdraw` SET
									`bin_id`    = '{$Bbc->member['id']}',
									`username`  = '{$Bbc->member['username']}',
									`name`      = '{$Bbc->member['name']}',
									`bank_name` = '{$Bbc->member['bank_name']}',
									`bank_no`   = '{$Bbc->member['bank_no']}',
									`total`     = '{$_POST['withdraw']}',
									`surcharge` = '{$surcharge}',
									`transfer`  = '{$transfer}',
									`done`      = 0";
							}
							if ($db->Execute($q))
							{
								_func('alert');
								$message = lang('Jumlah yang ditarik %s dengan potongan %s. Total transfer adalah %s', money($_POST['withdraw']), money($surcharge), money($_POST['withdraw']-$surcharge));
								alert_add('Penarikan Dana Oleh '.$Bbc->member['username'], $message, array('url_admin'=>'index.php?mod=bin.transfer_list'), 'admin');
								echo msg(lang('Permintaan penarikan dana telah masuk ke data kami, silahkan menunggu untuk di proses oleh admin'), 'success');
							}else{
								echo msg(lang('Maaf, permintaan anda untuk penarikan dana gagal disimpan'), 'danger');
							}
						}else{
							?>
							<form action="" method="POST" class="form formIsRequire" role="form" enctype="multipart/form-data" id="bonus_withdraw">
								<div class="form-group">
									<label><?php echo lang('Total Biaya Yang akan Diterima'); ?></label>
									<input type="hidden" name="withdraw" value="<?php echo $_POST['withdraw']; ?>" />
									<div class="form-control-static">
									<?php echo lang('Jumlah dana yang anda tarik adalah %s dengan potongan %s<br />sehingga total yang anda terima adalah Rp. %s', money($_POST['withdraw']), money($surcharge), money($_POST['withdraw']-$surcharge)); ?>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="withdraw_ok" value="1" req="number true" />
											<?php echo lang('Saya Menyetujui'); ?>
										</label>
									</div>
								</div>
								<button type="submit" name="submit" value="withdraw" class="btn btn-default"><?php echo icon('fa-check-circle').' '.lang('Setuju'); ?></button>
							<?php
						}
						$show_form = false;
					}else{
						echo msg(lang('Minimal dana yang bisa anda tarik adalah %s', money($plan_a['min_transfer'])), 'danger');
					}
				}else{
					echo msg(lang('Dana yang anda tarik tidak boleh melebihi saldo yang anda miliki, silahkan masukkan kembali!'), 'danger');
				}
			}
			if ($show_form)
			{
				?>
				<form action="" method="POST" class="form formIsRequire" role="form" enctype="multipart/form-data" id="bonus_withdraw">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><?php echo lang('Penarikan Dana'); ?></h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label><?php echo lang('Masukkan Jumlah Dana Yang Ingin Diambil'); ?></label>
								<input type="text" name="withdraw" class="form-control" value="<?php echo @$_POST['withdraw']; ?>" placeholder="Jumlah yang ingin ditarik" req="number true" data-max="<?php echo $Bbc->member['balance']; ?>" data-min="<?php echo $plan_a['min_transfer']; ?>" />
								<div class="help-block">
									<?php echo lang('Total komisi anda saat ini adalah: %s', money($Bbc->member['balance'])); ?><br />
									<?php echo lang('Potongan biaya transfer: %s', (preg_match('~^[0-9]+$~is', $plan_a['surcharge']) ? 'Rp. '.money($plan_a['surcharge']) : $plan_a['surcharge'])); ?><br />
									<?php echo lang('Ditambah potongan biaya: %s', (preg_match('~^[0-9]+$~is', $plan_a['surcharge2']) ? 'Rp. '.money($plan_a['surcharge2']) : $plan_a['surcharge2'])); ?>
									<?php
									if (!empty($user->params['NPWP']))
									{
										echo lang('(Untuk member dengan NPWP)');
									}else{
										echo lang('(Untuk member tanpa NPWP)');
									}
									?>
								</div>
							</div>
						</div>
						<div class="panel-footer">
							<button type="submit" name="submit" value="withdraw" class="btn btn-default"><?php echo icon('fa-paper-plane-o').' '.lang('Tarik Dana'); ?></button>
						</div>
					</div>
				</form>
				<script type="text/javascript">
					_Bbc(function($){
						$("#bonus_withdraw").on("submit", function(e){
							var a = $("input[name=withdraw]", $(this));
							if (a.val() > a.data("max")) {
								e.preventDefault();
								alert("<?php echo lang('Dana yang anda tarik tidak boleh melebihi saldo yang anda miliki, silahkan masukkan kembali!'); ?>");
								a.select();
							}else
							if (a.val() < a.data("min")) {
								e.preventDefault();
								alert("<?php echo lang('Minimal dana yang bisa anda tarik adalah %s!', money($plan_a['min_transfer'])); ?>");
								a.select();
							}
						})
					});
				</script>
				<?php
			}
		}else{
			echo msg(lang('Maaf, saat ini anda tidak memiliki akses untuk menarik bonus anda'), 'danger');
		}
	}else{
		echo msg(lang('Maaf, komisi yang anda miliki belum memenuhi minimal tarik dana yaitu %s sedangkan komisi anda saat ini %s', money($plan_a['min_transfer']), money($Bbc->member['balance'])), 'warning');
	}
}else{
	echo msg(lang('Auto withdraw kondisi aktif, anda tidak perlu melakukan penarikan dana'), 'danger');
}