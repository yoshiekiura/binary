<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (_ADMIN=='')
{
	$sys->stop(false);
	$_URL     = _URL.'tools/bin-member';
	$is_admin = false;
}else{
	$_URL     = $Bbc->mod['circuit'].'.'.$Bbc->mod['task'];
	$is_admin = true;
}
$_url     = seo_uri();
$tbl_name = 'bin_aaaaaaaaaa';
$sponsor  = '';
$upline   = '';
$query    = array();
$seconds  = @intval($_SESSION['bin_member_seconds']);
_func('bin');
require_once _ROOT.'modules/bin/admin/_function.php';
if (!empty($_POST['submit']))
{
	if ($_POST['submit']=='reset')
	{
		$_POST['submit'] = 'SAVE';
		require_once _ROOT.'modules/bin/_setting.php';
		$sys->module_change('bin');
		require_once _ROOT.'modules/bin/admin/_function.php';
		$cfg   = array(
			'plan_a'  => config('plan_a'),
			'reward'  => $db->getAll("SELECT * FROM `bin_reward` WHERE 1 ORDER BY id ASC"),
			'level'   => $db->getAll("SELECT * FROM `bin_level` WHERE 1 ORDER BY id ASC"),
			'balance' => $db->getAll("SELECT * FROM `bin_balance_type` WHERE 1 ORDER BY id ASC")
			);
		file_write(_CACHE.'marketplan.json', json_encode($cfg));
		include _ROOT.'modules/bin/admin/config_setup-validation.php';
	}else
	if ($_POST['submit']=='add')
	{
		$sponsor = bin_fetch($_POST['sponsor']);
		$upline  = bin_fetch($_POST['upline']);
		$query[] = "INSERT INTO `{$tbl_name}` SET `type_id`={$_POST['type_id']}, `total`=1, `done`=0";
	}else{
		if (isset($_POST['seconds']))
		{
			$seconds = $_SESSION['bin_member_seconds'] = intval($_POST['seconds']);
		}
		foreach ($_POST['create'] as $type_id => $total)
		{
			if (is_numeric($total) && $total > 0)
			{
				$query[] = "INSERT INTO `{$tbl_name}` SET `type_id`={$type_id}, `total`={$total}, `done`=0";
			}
		}
	}
}
if (!empty($query))
{
	$db->Execute("CREATE TABLE `{$tbl_name}` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `type_id` int(11) DEFAULT '0',
	  `total` int(11) DEFAULT '0',
	  `done` int(11) DEFAULT '0',
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	foreach ($query as $q)
	{
		$db->Execute($q);
	}
	if ($_POST['submit']!='add')
	{
		$_url .= preg_match('~\?~s', $_url) ? '&' : '?';
		$_url .= 'simultaneous='.@intval($_POST['simultaneous']);
		redirect($_url);
	}
}

$ada = $db->getRow("SHOW TABLES LIKE '{$tbl_name}'");
$new = $ada ? $db->getRow("SELECT * FROM `{$tbl_name}` WHERE 1 ORDER BY RAND() LIMIT 1") : array();
if (empty($new))
{
	if ($ada)
	{
		unset($_SESSION['bin_member_seconds']);
		$db->Execute("DROP TABLE IF EXISTS `{$tbl_name}`");
		redirect($_URL);
	}
	$tabs = array();
	ob_start();
	link_js(_LIB.'pea/includes/formIsRequire.js');
	?>
	<form action="" method="POST" class="form-horizontal formIsRequire" role="form">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Generate Member Massal (<?php echo date('H:i:s'); ?>)</h3>
			</div>
			<div class="panel-body">
				<?php
				$r_type = $db->getAll("SELECT * FROM `bin_serial_type` WHERE 1 ORDER BY `id` ASC");
				foreach ($r_type as $d)
				{
					?>
					<div class="form-group">
						<label>Tipe <?php echo $d['name']; ?></label>
						<input type="number" name="create[<?php echo $d['id']; ?>]" class="form-control" placeholder="Jumlah member yang ingin dibuat" autocomplete="OFF" req="number false" />
					</div>
					<?php
				}
				if ($is_admin)
				{
					?>
					<input type="hidden" name="simultaneous" value="1" />
					<input type="hidden" name="seconds" value="1" />
					<?php
				}else{
					?>
					<div class="form-group">
						<label>Apakah anda ingin menjalankan secara simultan terus menerus?</label>
						<div class="form-inline">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="simultaneous" value="1" checked="checked" />
									iya /
								</label>
							</div>
							<input type="number" name="seconds" class="form-control" placeholder="ulangi per" value="1" autocomplete="OFF" req="number false" /> detik
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<div class="panel-footer">
				<?php
				if (!empty($_GET['return']))
				{
					echo $sys->button($_GET['return']);
				}
				if (isset($_GET['simultaneous']))
				{
					?>
					<span type="button" class="btn btn-sm btn-default" onclick="document.location.href='<?php echo $_URL; ?>';"><span class="glyphicon glyphicon-chevron-left"></span></span>
					<?php
				}
				?>
				<input type="hidden" name="uri" value="<?php echo $_url; ?>" />
				<button class="btn btn-sm btn-default" type="submit" name="submit" value="create"><span class="glyphicon glyphicon-console"></span> Generate Sekarang</button>
				<button class="btn btn-sm btn-danger" type="submit" name="submit"  value="reset" onclick="return confirm('Apakah anda yakin ingin menghapus semua member')"><span class="glyphicon glyphicon-trash"></span> Reset Member</button>
			</div>
		</div>
	</form>
	<?php
	$tabs['massal'] = ob_get_contents();
	ob_end_clean();
	ob_start();
	$last_upline  = $db->getRow("SELECT * FROM `bin` WHERE `active`=1 AND `total_downline`<2 ORDER BY `depth_upline` ASC, `id` ASC LIMIT 1");
	$last_sponsor = $db->getRow("SELECT * FROM `bin` WHERE `id`={$last_upline['upline_id']} LIMIT 1");
	?>
	<form action="" method="POST" class="form-horizontal formIsRequire" role="form">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Generate Satu Member</h3>
			</div>
			<div class="panel-body">
				<?php
				if (count($r_type) == 1)
				{
					?>
					<input type="hidden" name="type_id" value="<?php echo $r_type[0]['id']; ?>" />
					<?php
				}else{
					?>
					<div class="form-group">
						<label>Tipe Serial</label>
						<select name="type_id" class="form-control"><?php echo createOption($r_type); ?></select>
					</div>
					<?php
				}
				?>
				<div class="form-group">
					<label>Sponsor</label>
					<input type="text" class="form-control" name="sponsor" placeholder="Masukkan ID atau username" req="any true" />
					<div class="help-block">
						Contoh Sponsor : <?php echo $last_sponsor['username']; ?>
					</div>
				</div>
				<div class="form-group">
					<label>Upline</label>
					<input type="text" class="form-control" name="upline" placeholder="Masukkan ID atau username" req="any true" />
					<div class="help-block">Contoh Upline : <?php echo $last_upline['username']; ?></div>
				</div>
				<div class="form-group">
					<label>Posisi</label>
					<select name="position" class="form-control">
					<option value="auto">-- otomatis --</option>
					<option value="0">Kiri</option>
					<option value="1">Kanan</option>
					</select>
					<div class="help-block">
						Contoh Posisi : <?php echo $last_upline['total_left'] ? 'Kanan' : 'Kiri'; ?>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<button class="btn btn-sm btn-default" type="submit" name="submit" value="add"><span class="glyphicon glyphicon-console"></span> Buat Member</button>
			</div>
		</div>
	</form>
	<?php
	$tabs['Single'] = ob_get_contents();
	ob_end_clean();
	echo tabs($tabs, 1, '', true);
	$count = $db->getOne("SELECT COUNT(*) FROM `bin` WHERE `done`=0");
	if (!empty($count))
	{
		_func('date');
		$last = $db->getOne("SELECT `updated` FROM `bin` WHERE `done`=0 ORDER BY `updated` ASC LIMIT 1");
		echo msg(lang('Anda memiliki <b>%s</b> member yang belum selesai diproses oleh sistem terakhir update <b>%s lalu</b>. Jika dalam waktu lebih dari 5 menit proses masih belum selesai, langkah pertama yang bisa anda lakukan adalah dengan membuka halaman <a href="'._URL.'bin/fix">'._URL.'bin/fix</a>', money($count), timespan($last)), 'danger');
	}
}else{
	if (!empty($new))
	{
		if ($new['done']>=$new['total'])
		{
			$q = "DELETE FROM `{$tbl_name}` WHERE `id`=".$new['id'];
		}else{
			$q = "UPDATE `{$tbl_name}` SET `done`=(`done`+1) WHERE `id`=".$new['id'];
		}
		$db->Execute($q);
		$render_output = '';
		if ($new['done'] < $new['total'])
		{
			$output = bin_create_member($sponsor, $upline, $new['type_id'], @$_POST['position']);
		}else{
			redirect($_URL);
		}
		if (!empty($output['user_id']))
		{
			$member = bin_fetch_username($output['serial']);
			$_GET['id']      = $member['id'];
			$_GET['is_ajax'] = 1;
			ob_start();
				include _ROOT.'modules/bin/admin/list_detail.php';
				pr($new, $Bbc->debug, __FILE__.':'.__LINE__);
				$render_output = ob_get_contents();
			ob_end_clean();
			unset($_GET['is_ajax']);
		}else{
			pr($output['message'], $new, $Bbc->debug, __FILE__.':'.__LINE__);
		}
		if (preg_match('~'.preg_quote('color:#ff0000;font-weight: bold', '~').'~is', implode('<br />', $Bbc->debug)))
		{
			$_GET['simultaneous'] = 0;
		}
		if (empty($_GET['simultaneous']))
		{
			$_url1 = $_url;
			$_url1 .= preg_match('~\?~s', $_url) ? '&' : '?';
			?>
			<span type="button" class="btn btn-sm btn-default" id="btn-load" onclick="document.location.href='<?php echo $_url; ?>';"><span class="glyphicon glyphicon-refresh"></span></span>
			<span type="button" class="btn btn-sm btn-default" id="btn-play" onclick="document.location.href='<?php echo $_url1; ?>simultaneous=1';" ><span class="glyphicon glyphicon-play"></span></span>
			<span type="button" class="btn btn-sm btn-default" id="btn-stop"><span class="glyphicon glyphicon-stop"></span></span>
			<script type="text/javascript">
				_Bbc(function($){
					window.simultanID = window.setTimeout(function(){
						$("#btn-play").trigger("click");
					}, 1000);
					$("#btn-stop").on("click", function(e){
						e.preventDefault();
						window.clearTimeout(window.simultanID);
					});
				});
			</script>
			<?php
			echo $render_output;
		}else{
			echo $render_output;
			?>
			<script type="text/javascript">
			<?php
			if ($seconds > 0) {
				$seconds = $seconds*1000;
				?>
				setTimeout(function(){
					document.location.href='<?php echo $_url; ?>';
				}, <?php echo $seconds; ?>);
				<?php
			}else{
				?>
				document.location.href='<?php echo $_url; ?>';
				<?php
			}
			?>
			</script>
			<?php
		}
	}
}