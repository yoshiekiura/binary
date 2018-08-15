<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if (empty($id))
{
	$id = 1;
}
if (empty($_GET['is_ajax']))
{
	if (!empty($_POST['submit']))
	{
		if ($_POST['submit']=='reset')
		{
			redirect($Bbc->mod['circuit'].'.'.$Bbc->mod['task']);
		}
		$member = is_numeric($_POST['id']) ? bin_fetch_id($_POST['id']) : bin_fetch_username($_POST['id']);
		if (!empty($member))
		{
			redirect($Bbc->mod['circuit'].'.'.$Bbc->mod['task'].'&id='.$member['id']);
		}else{
			echo msg('Maaf, id atau username yang anda masukkan tidak tersedia', 'danger');
		}
	}
	link_js(_LIB.'pea/includes/FormTags.js');
	$token = array(
		'table'  => 'bin',
		'field'  => 'username',
		'id'     => 'id',
		'format' => 'CONCAT(username, " (", name, ")")',
		'expire' => strtotime('+2 HOURS'),
		);
	?>
	<form action="" method="POST" class="form-inline pull-right" role="form">
		<div class="form-group">
			<label class="sr-only">Member ID or Username</label>
			<input type="text" class="form-control" name="id" value="<?php echo $id; ?>" rel="ac" data-token="<?php echo encode(json_encode($token)); ?>" />
		</div>
		<button type="submit" class="btn btn-default" name="submit" value="search"><?php echo icon('fa-sitemap'); ?></button>
		<button type="submit" class="btn btn-default" name="submit" value="reset"><?php echo icon('remove-circle'); ?></button>
	</form>
	<?php
	$maxlevel = 0;
}else $maxlevel = 3;
echo _class('bin')->show($id, $maxlevel);
if (!empty($_GET['is_ajax']))
{
	?>
	<center>
		<a href="<?php echo $Bbc->mod['circuit'].'.'.$Bbc->mod['task'].'&id='.$id; ?>" class="btn btn-default" onclick="adminLink(this.href); return false;"><?php echo icon('fa-sitemap'); ?> Lihat Detail</a>
	</center>
	<?php
}