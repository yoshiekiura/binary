<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed'); ?>
<div class="panel panel-<?php echo ($Bbc->mod['task']=='config_setup') ? 'danger' : 'default'; ?>">
	<div class="panel-heading">
		<h3 class="panel-title">
			<?php
			if ($Bbc->mod['task']=='config_setup')
			{
				echo 'Konfirmasi Perubahan Market Plan';
			}else{
				echo 'Market Plan';
			}
			?>
		</h3>
	</div>
	<div class="panel-body">
		<div class="form-group">
			<label>User Group yang akan diberlakukan sebagai Member MLM</label>
			<div class="form-control-static"><a href="index.php?mod=_cpanel.group&act=edit&id=<?php echo $config['group_id'];?>" rel="admin_link"><?php echo $db->getOne("SELECT `name` FROM `bbc_user_group` WHERE `id`=".$config['group_id']); ?></a></div>
		</div>
		<div class="form-group">
			<label>Prefix Serial</label>
			<div class="form-control-static"><?php echo $config['prefix']; ?></div>
			<div class="help-block">Ini adalah prefix atau awalan yang akan digunakan untuk setiap kartu serial bagi para calon member</div>
		</div>
		<div class="form-group">
			<label>Biaya Registrasi</label>
			<div class="form-control-static"><?php echo money($config['price']);echo (!empty($config['serial_use'])) ? ' (Biaya Termurah)' : ''; ?></div>
		</div>
		<?php
		if (!empty($config['serial_use']) && !empty($config['serial_list']))
		{
			?>
			<div class="form-group">
				<label>Tipe Serial</label>
				<ul>
					<?php
					$is_flush = (!empty($config['bonus_pair']) && !empty($config['serial_flushout_ok'])) ? 1 : 0;
					foreach ($config['serial_list'] as $i => $serial)
					{
						$add = $is_flush ? ' (Flushout '.money($config['serial_flushout'][$i]).')' : '';
						echo '<li>'.$serial.' -&gt; '.money($config['serial_price'][$i]).$add.'</li>';
					}
					if ($Bbc->mod['task']!='config_setup')
					{
						?>
						<li><a href="index.php?mod=bin.config_serialtype" class="btn btn-default btn-xs" rel="admin_link">Setup Tipe Serial <?php echo icon('fa-angle-double-right'); ?></a></li>
						<?php
					}
					?>
				</ul>
				<?php
				if ($config['serial_check'])
				{
					echo '<div class="help-block">Serial hanya bisa diaktifasi/digunakan apabila sudah terbeli oleh member</div>';
				}
				?>
			</div>
			<?php
		}
		?>
		<div class="form-group">
			<label>Kebijakan Transfer Bonus</label>
			<div class="form-control-static">
				Minimum Transfer: Rp. <?php echo money($config['min_transfer']); ?>
				( <?php echo $config['is_withdraw'] ? 'Member Harus withdraw' : 'Auto Withdraw'; ?> )
			</div>
			<?php
			if (!$config['is_withdraw'])
			{
				$txt = 'admin harus memproses transfer secara berkala';
			}else{
				$txt = 'Setiap member melakukan withdraw maka akan muncul';
			}
			?>
			<div class="help-block">
				<?php echo $txt; ?> pada menu "<a href="index.php?mod=bin.transfer_list" rel="admin_link">Transfer / Pending Transfer</a>" dengan: - mendownload file pending transfer - menandai yang sudah di transfer - lalu mengupload kembali
			</div>
		</div>
		<div class="form-group">
			<label>Potongan Biaya Transfer Bonus</label>
			<div class="form-control-static"><?php echo preg_match('~^[0-9]+$~is', $config['surcharge']) ? 'Rp. '.money($config['surcharge']) : $config['surcharge']; ?></div>
		</div>
		<?php
		if (!empty($config['surcharge_npwp']) || !empty($config['surcharge_npwp_no']))
		{
			?>
			<div class="form-group">
				<label>Potongan Biaya Transfer NPWP</label>
				<div class="form-control-static">
				Member Dengan NPWP: <?php echo preg_match('~^[0-9]+$~is', $config['surcharge_npwp']) ? 'Rp. '.money($config['surcharge_npwp']) : $config['surcharge_npwp']; ?><br />
				Member Tanpa NPWP: <?php echo preg_match('~^[0-9]+$~is', $config['surcharge_npwp_no']) ? 'Rp. '.money($config['surcharge_npwp_no']) : $config['surcharge_npwp_no']; ?>
				</div>
			</div>
			<?php
		}
		if (!empty($config['bonus_node']))
		{
			?>
			<div class="panel-group" id="accordionbonus_node">
				<div class="panel panel-<?php echo ($Bbc->mod['task']=='config_setup') ? 'warning' : 'default'; ?>">
					<div class="panel-heading">
						<a data-toggle="collapse" data-parent="#accordionbonus_node" href="#pea_isHideToolOnbonus_node">
							<h4 class="panel-title">
								Bonus Titik
							</h4>
						</a>
					</div>
					<div id="pea_isHideToolOnbonus_node" class="panel-collapse collapse in">
						<div class="panel-body">
							<strong><?php echo money($config['bonus_node'][0]); ?></strong>
							<?php
							if (count($config['bonus_node']) > 1)
							{
								?>
								<div class="form-group">
									<label>Bonus Level Titik :</label>
									<ul>
										<?php
										foreach ($config['bonus_node'] as $i => $bonus)
										{
											if ($i)
											{
												?>
												<li>Level <?php echo $i.' =&gt; '.money($bonus); ?></li>
												<?php
											}
										}
										?>
									</ul>
								</div>
								<?php
							}
							echo bin_check_func('bin_bonus_node');
							if (count($config['bonus_gen_node']) > 1)
							{
								?>
								<div class="form-group">
									<label>Bonus Generasi Titik :</label>
									<ul>
										<?php
										foreach ($config['bonus_gen_node'] as $i => $bonus)
										{
											if ($i)
											{
												?>
												<li>Level <?php echo $i.' =&gt; '.money($bonus); ?></li>
												<?php
											}
										}
										?>
									</ul>
								</div>
								<?php
							}
							echo bin_check_func('bin_bonus_gen_node');
							?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		if (!empty($config['bonus_sponsor']))
		{
			?>
			<div class="panel-group" id="accordionbonus_sponsor">
				<div class="panel panel-<?php echo ($Bbc->mod['task']=='config_setup') ? 'warning' : 'default'; ?>">
					<div class="panel-heading">
						<a data-toggle="collapse" data-parent="#accordionbonus_sponsor" href="#pea_isHideToolOnbonus_sponsor">
							<h4 class="panel-title">
								Bonus Sponsor
							</h4>
						</a>
					</div>
					<div id="pea_isHideToolOnbonus_sponsor" class="panel-collapse collapse in">
						<div class="panel-body">
							<strong><?php echo money($config['bonus_sponsor'][0]); ?></strong>
							<?php
							if (count($config['bonus_sponsor']) > 1)
							{
								?>
								<div class="form-group">
									<label>Bonus Generasi Sponsor :</label>
									<ul>
										<?php
										foreach ($config['bonus_sponsor'] as $i => $bonus)
										{
											if ($i)
											{
												?>
												<li>Level <?php echo $i.' =&gt; '.money($bonus); ?></li>
												<?php
											}
										}
										?>
									</ul>
								</div>
								<div class="form-group">

								</div>
								<?php
								$text_double = !empty($config['bonus_sponsor_double']) ? 'sponsor itu sendiri (bukan sponsor di atasnya)' : 'member yang berada di atas sponsor tersebut';
								?>
								<div class="form-group">
									Bonus Generasi Level 1 akan diterima oleh <?php echo $text_double; ?>
								</div>
								<?php
							}
							echo bin_check_func('bin_bonus_sponsor');
							?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		if (!empty($config['bonus_pair']))
		{
			?>
			<div class="panel-group" id="accordionbonus_pair">
				<div class="panel panel-<?php echo ($Bbc->mod['task']=='config_setup') ? 'warning' : 'default'; ?>">
					<div class="panel-heading">
						<a data-toggle="collapse" data-parent="#accordionbonus_pair" href="#pea_isHideToolOnbonus_pair">
							<h4 class="panel-title">
								Bonus Pasangan
							</h4>
						</a>
					</div>
					<div id="pea_isHideToolOnbonus_pair" class="panel-collapse collapse in">
						<div class="panel-body">
							<strong><?php echo money($config['bonus_pair'][0]); ?></strong>
							<?php
							if (count($config['bonus_pair']) > 1)
							{
								?>
								<div class="form-group">
									<label>Bonus Generasi Pasangan :</label>
									<ul>
										<?php
										foreach ($config['bonus_pair'] as $i => $bonus)
										{
											if ($i)
											{
												?>
												<li>Level <?php echo $i.' =&gt; '.money($bonus); ?></li>
												<?php
											}
										}
										?>
									</ul>
								</div>
								<?php
							}
							?>
							<div class="form-group">
								<label>Flushout</label>
								<div class="form-control">
									<?php
									echo money($config['flushout_total']);
									if (!empty($config['serial_flushout_ok']))
									{
										echo ' (nilai terendah)';
									}
									echo ' / '.money($config['flushout_time']).' '.$config['flushout_duration']; ?>
								</div>
							</div>
							<?php
							if (!empty($config['flushwait']))
							{
								?>
								<div class="form-group">
									<label>Titik Menunggu Hangus</label>
									<div class="form-control">
										<?php echo money($config['flushwait_time']).' '.$config['flushwait_duration']; ?>
									</div>
								</div>
								<?php
							}
							?>
							<?php echo bin_check_func('bin_bonus_pair'); ?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		?>
		<div class="form-group">
			<label>Member Reward</label>
			<?php
			if (empty($config['reward_use']))
			{
				echo '<div class="form-control-static">Tidak ada reward</div>';
			}else{
				?>
				<ul>
					<?php
					foreach ($config['reward_list'] as $i => $reward)
					{
						echo '<li>'.$reward.'</li>';
					}
					if ($Bbc->mod['task']!='config_setup')
					{
						?>
						<li><a href="index.php?mod=bin.config_reward" class="btn btn-default btn-xs" rel="admin_link">Setup Reward <?php echo icon('fa-angle-double-right'); ?></a></li>
						<?php
					}
					?>
				</ul>
				<?php
				if ($config['reward_auto'])
				{
					$msg = 'Reward di berikan otomatis oleh sistem tanpa harus member melakukan klaim';
				}else{
					$msg = 'Setiap reward hanya bisa di terima oleh member apabila sudah melakukan klaim dan disetujui oleh admin';
				}
				echo '<div class="help-block">'.$msg.bin_check_func('bin_reward').'</div>';
			}
			?>
		</div>
		<?php
		if (count($config['level_list']) > 1)
		{
			?>
			<div class="form-group">
				<label>Peringkat Member</label>
				<ul>
					<?php
					foreach ($config['level_list'] as $i => $level)
					{
						echo '<li>'.$level.'</li>';
					}
					if ($Bbc->mod['task']!='config_setup')
					{
						?>
						<li><a href="index.php?mod=bin.config_level" class="btn btn-default btn-xs" rel="admin_link">Setup Peringkat <?php echo icon('fa-angle-double-right'); ?></a></li>
						<?php
					}
					?>
				</ul>
				<?php echo bin_check_func('bin_level');?>
			</div>
			<?php
		}
		if ($Bbc->mod['task']=='config_setup')
		{
			?>
			<form action="" method="post" enctype="multipart/form-data" role="form" class="form-inline">
	      <span type="button" class="btn btn-default" onclick="document.location.href='index.php?mod=bin.config_setup&type=form'"><span class="glyphicon glyphicon-chevron-left"></span></span>
				<button type="submit" name="submit" value="SAVE" class="btn btn-danger"><span class="glyphicon glyphicon-floppy-disk" title="simpan perubahan"></span> Simpan Perubahan Dan Hapus Semua Jaringan</button>
			</form>
			<?php
		}else{
			if ($db->debug)
			{
				$url = '&return='.urlencode(seo_uri());
				echo '<a href="index.php?mod=bin.config_setup'.$url.'" class="btn btn-default">'.icon('fa-sitemap').' Ubah Market Plan</a> ';
				echo '<a href="index.php?mod=bin.config_marketplan_trial'.$url.'" class="btn btn-default">'.icon('fa-try').' Uji Market Plan</a>';
			}
		}
		?>
	</div>
</div>
