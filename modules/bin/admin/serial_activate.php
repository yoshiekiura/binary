<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form          = '';
$buyer_bin_id  = 0;
$buyer_user_id = 0;
$member_token  = array(
	'table'  => 'bin',
	'field'  => 'username',
	'id'     => 'id',
	'sql'    => 'active=1',
	'expire' => strtotime('+2 HOURS')
	);
if (config('plan_a', 'serial_use')=='1')
{
	$form = _lib('pea', 'bin_serial_inactive');
	$form->initSearch();

	$form->search->addInput('type_id','selecttable');
	$form->search->input->type_id->addOption('--pilih type--', '');
	$form->search->input->type_id->setReferenceTable('`bin_serial_type` ORDER BY `id` ASC');
	$form->search->input->type_id->setReferenceField( 'name', 'id' );

	$form->search->addInput('keyword','keyword');
	$form->search->input->keyword->setTitle('Masukkan Serial ID');
	$form->search->input->keyword->addSearchField('code', false);

	$form->search->addExtraField('active', 0);

	$add_sql = $form->search->action();
	$keyword = $form->search->keyword();
}else{
	$add_sql = 'WHERE `active`=0';
}
if (!empty($_POST['submit_all']))
{
	if ($_POST['submit_all']=='activate_all')
	{
		$buyer = @bin_fetch($_POST['buyer_bin_id']);
		$q     = "UPDATE `bin_serial` SET
			`active`       = 1,
			`buyer_id`     = ".@intval($buyer['user_id']).",
			`buyer_bin_id` = ".@intval($buyer['id']).",
			`buyer_date`   = '".date('Y-m-d H:i:s')."'
			{$add_sql}";
		if ($db->Execute($q))
		{
			echo msg('Semua serial telah diaktifkan', 'success');
		}else{
			echo msg('Gagal mengaktifkan semua serial', 'danger');
		}
	}else
	if ($_POST['submit_all']=='download_all')
	{
		if (config('plan_a', 'serial_use')=='1')
		{
			$q = "SELECT code, pin, name AS tipe, created FROM `bin_serial` AS s LEFT JOIN `bin_serial_type` AS t ON(s.`type_id`=t.`id`) {$add_sql} ORDER BY s.`type_id` ASC, s.`id` ASC";
		}else{
			$q = "SELECT code, pin, created FROM `bin_serial` AS s LEFT JOIN `bin_serial_type` AS t ON(s.`type_id`=t.`id`) {$add_sql} ORDER BY s.`type_id` ASC, s.`id` ASC";
		}
		$r = $db->getAll($q);
		_func('download', 'excel', 'pending_serial-'.date('Y-m-d'), $r);
	}
}else{
	if (config('plan_a', 'serial_check')=='1')
	{
		if (!empty($_POST['roll_activate']) && !empty($_POST['roll_active']))
		{
			if (!empty($_POST['roll_buyer_bin_id']))
			{
				$buyer = bin_fetch($_POST['roll_buyer_bin_id']);
				if (!empty($buyer['id']))
				{
					$buyer_bin_id  = $buyer['id'];
					$buyer_user_id = $buyer['user_id'];
				}
			}
			if (empty($buyer_bin_id) || empty($_POST['ids']))
			{
				?>
				<form action="" method="POST" class="formIsRequire" role="form">
					<div class="panel panel-warning">
						<div class="panel-heading">
							<h3 class="panel-title">Masukkan username pembeli untuk mengaktifkan</h3>
						</div>
						<?php
						foreach ($_POST['roll_active'] as $i => $val)
						{
							if (!empty($_POST['roll_id'][$i]))
							{
								$val = $_POST['roll_id'][$i];
								?>
								<input type="hidden" name="ids[]" value="<?php echo $val; ?>" />
								<?php
							}
						}
						?>
						<div class="panel-body">
							<div class="form-group">
								<label>Username Pembeli : </label>
								<input value="<?php echo @$_POST['roll_buyer_bin_id']; ?>" name="roll_buyer_bin_id" class="form-control" req="any true" rel="ac" type="text" placeholder="username pembeli" data-token="<?php echo encode(json_encode($member_token)); ?>" />
							</div>
						</div>
						<div class="panel-footer">
							<button type="submit" name="roll_activate" value="activate_all" class="btn btn-warning"><span class="glyphicon glyphicon-ok"></span> Aktifkan Sekarang</button>
							<input type="hidden" name="roll_active" value="1" />
						</div>
					</div>
				</form>
				<?php
			}else{
				$ids = @$_POST['ids'];
				ids($ids);
				if (!empty($ids))
				{
					$db->Execute("UPDATE `bin_serial` SET `active`=1, `buyer_id`={$buyer_user_id}, `buyer_bin_id`={$buyer_bin_id}, `buyer_date`='".date('Y-m-d H:i:s')."' WHERE `id` IN({$ids})");
					echo msg('Serial yang anda pilih telah diaktifkan', 'success');
				}
			}
			$_POST = array();
		}
	}
}

if (config('plan_a', 'serial_use')=='1')
{
	echo $form->search->getForm();
}

$form = _lib('pea',  'bin_serial' );
$form->initRoll("{$add_sql} ORDER BY `id` DESC", 'id' );

$form->roll->addInput('header','header');
$form->roll->input->header->setTitle('Serial yang ingin di aktifkan agar bisa digunakan');

$form->roll->addInput('code','sqlplaintext');
$form->roll->input->code->setTitle('Serial');

if (config('plan_a', 'serial_use')=='1')
{
	$form->roll->addInput('type_id', 'selecttable');
	$form->roll->input->type_id->setTitle('Type');
	$form->roll->input->type_id->setReferenceTable('bin_serial_type');
	$form->roll->input->type_id->setReferenceField('name', 'id');
	$form->roll->input->type_id->setPlaintext(true);
}

$form->roll->addInput('created','sqlplaintext');
$form->roll->input->created->setTitle('Dibuat');
$form->roll->input->created->setDateFormat();

$form->roll->addInput('expired','sqlplaintext');
$form->roll->input->expired->setTitle('Expired');
$form->roll->input->expired->setDateFormat();

$form->roll->addInput('active','checkbox');
$form->roll->input->active->setTitle('action');
$form->roll->input->active->setCaption('activate');

// $form->roll->setDeleteTool(false);
// $form->roll->setSaveTool(false);
$form->roll->setSaveButton('activate', 'AKTIFKAN', 'ok');

$count = $db->getOne("SELECT COUNT(*) FROM `bin_serial` {$add_sql}");
if ($count > 0)
{
	?>
	<form action="" method="POST" class="form-inline formIsRequire" role="form">
		<?php
		if (config('plan_a', 'serial_check')=='1')
		{
			?>
			<div class="form-group">
				<label>Username Pembeli : </label>
				<input value="<?php echo @$_POST['buyer_bin_id']; ?>" name="buyer_bin_id" class="form-control" req="any true" rel="ac" type="text" placeholder="username pembeli" data-token="<?php echo encode(json_encode($member_token)); ?>" />
			</div>

			<?php
		}
		?>
		<button type="submit" name="submit_all" value="activate_all" class="btn btn-default"><span class="glyphicon glyphicon-ok"></span> AKTIFKAN SEMUA</button>
		<button type="submit" name="submit_all" value="download_all" class="btn btn-default" id="download_all"><span class="glyphicon glyphicon-download-alt"></span> DOWNLOAD SEMUA</button>
	</form>
	<p></p>
	<script type="text/javascript">
		_Bbc(function($){
			$('#download_all').on("click", function(){
				// alert("sdfsd");
				var a = $("#buyer_bin_id").val();
				if (a=="") {
					$("#buyer_bin_id").val("1");
					setTimeout(function(){
						$("#buyer_bin_id").val("");
					}, 1000)
				}
			});
		});
	</script>
	<?php
	link_js(_LIB.'pea/includes/FormTags.js', false);
	link_js(_LIB.'pea/includes/formIsRequire.js', false);
}
echo $form->roll->getForm();
