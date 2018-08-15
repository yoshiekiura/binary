<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');
$user_field = $db->getAssoc('SELECT `id`, `title` FROM `bbc_user_field` WHERE `active` = 1');

if (!empty($_POST))
{
	$limit = array();
	foreach ($_POST['limit'] as $key => $value)
	{
		if (is_numeric($value) && $value > 0)
		{
			$limit[$key] = $value;
		}
	}
	$_POST['limit'] = $limit;
	if (set_config('bin_fields', $_POST))
	{
		echo msg('Konfigurasi anda telah berhasil disimpan', 'success');
	}else{
		echo msg('Maaf, konfigurasi anda GAGAL disimpan', 'danger');
	}
}

$fields = config('bin_fields');
$r_field = user_field_group(config('plan_a', 'group_id'));
link_js(_LIB.'pea/includes/formIsRequire.js');
?>

<form action="" method="POST" role="form" class="formIsRequire">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Konfigurasi Member Fields</h3>
		</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover table-striped table-bordered">
					<thead>
						<tr>
							<th>Input Field</th>
							<th>Maksimum</th>
							<th>Editable</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($r_field as $field)
						{
							$checked = '';
							$maximum = '';
							if (!empty($fields['limit'][$field['title']]))
							{
								$maximum  = $fields['limit'][$field['title']];
							}
							?>
							<tr>
								<th><?php echo $field['title']; ?></th>
								<td>
									<input type="hidden" name="fields[<?php echo $field['title']; ?>]" class="form-control" value="<?php echo @$field['id']; ?>" />
									<input type="text" name="limit[<?php echo $field['title']; ?>]" class="form-control" value="<?php echo $maximum; ?>" placeholder="maximal digunakan registrasi" req="number false" />
								</td>
								<td>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="editable[]" value="<?php echo $field['title'] ?>"<?php echo in_array($field['title'], (array)@$fields['editable']) ? ' checked="true"' : '';?> /> Iya
										</label>
									</div>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" class="add_type btn btn-primary"><?php echo icon('floppy-disk').' SIMPAN'; ?></button>
		</div>
	</div>
</form>

