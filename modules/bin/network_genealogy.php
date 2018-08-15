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
	echo msg('Maaf, genealogy yang anda akses bukan termasuk dalam jaringan anda', 'danger');
}else{
	echo _class('bin')->show($Bbc->member['id'], 3);
	if (!empty($_GET['is_ajax']))
	{
		?>
		<center>
			<a href="<?php echo $Bbc->mod['circuit'].'.'.$Bbc->mod['task'].'&id='.$id; ?>" class="btn btn-default"><?php echo icon('fa-sitemap').' '.lang('Lihat Detail'); ?></a>
		</center>
		<?php
	}else{
		// Tidak menggunakan variable $user untuk jaga2 kalo dia edit profile nya posisi setelah login dan belum logout
		$dt = $db->getRow("SELECT * FROM `bbc_account` WHERE `user_id`={$user->id}");
		$params = config_decode($dt['params']);
		unset($params['serial'], $params['pin'], $params['sponsor'], $params['upline'], $params['position']);
		$cloner = array(
			'name' => $dt['name'],
			'params' => $params
			);
		?>
		<script type="text/javascript">
			var cloner = <?php echo json_encode($cloner); ?>;
		</script>
		<?php
	}
}
