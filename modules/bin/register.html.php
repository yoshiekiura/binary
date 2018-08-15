<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

link_js(_LIB.'pea/includes/FormTags.js', false);
link_js(_LIB.'pea/includes/formIsRequire.js', false);
$location_token = array(
	'table'  => 'bin_location',
	'field'  => 'detail',
	'id'     => 'id',
	'sql'    => 'publish=1 AND type=3',
	'expire' => strtotime('+2 HOURS'),
);
$current_fields = user_field_group(get_config('bin', 'plan_a', 'group_id'));
$remove_fields  = ['serial', 'pin', 'sponsor', 'upline', 'position', 'Phone', 'location_address', 'location_id', 'location_latlong'];
$custom_fields  = array();
$user_fields    = array();
foreach ($current_fields as $field)
{
	if (!in_array($field['title'], $remove_fields))
	{
		$user_fields[] = $field;
	}else{
		$custom_fields[$field['title']] = $field;
	}
}
$btn_link = '';
if (!empty($user->id) && _ADMIN=='')
{
	$sponsor = $db->getOne("SELECT `username` FROM `bin` WHERE `user_id`={$user->id}");
	if (!empty($sponsor))
	{
		$_POST['params']['sponsor'] = $sponsor;
		if (_ADMIN=='')
		{
			$_POST['params']['sponsor'] .= '" readonly="true';
		}
	}
	$btn_link = '<a class="btn btn-xs btn-default pull-right hidden" id="btn-duplicate">'.icon('duplicate').' '.lang('Clone Profile').'</a>';
}
?>
<form method="POST" action="" id="member_reg" name="member_reg" class="formIsRequire" enctype="multipart/form-data" role="form">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo lang('Registrasi Member').$btn_link; ?></h3>
		</div>
		<div class="panel-body">
  		<div class="col-sm-6">
				<div class="form-group">
					<label><?php echo lang('Serial')?></label>
					<input value="<?php echo @$_POST['params']['serial']; ?>" name="params[serial]" class="form-control" title="<?php echo lang('Serial'); ?>" placeholder="<?php echo lang('Serial'); ?>" req="any true" type="text" />
					<p class="help-block"> <?php echo lang($custom_fields['serial']['tips']) ?></p>
				</div>
				<div class="no-clonner">
					<div class="form-group">
						<label><?php echo lang('Sponsor')?></label>
						<input value="<?php echo @$_POST['params']['sponsor']; ?>" name="params[sponsor]" class="form-control" title="<?php echo lang('Member Sponsor'); ?>" placeholder="<?php echo lang('Member Sponsor'); ?>" req="any true" type="text" />
						<p class="help-block"> <?php echo lang($custom_fields['sponsor']['tips']) ?></p>
					</div>
	  			<div class="form-group">
						<label><?php echo lang('Nama Member')?></label>
						<input value="<?php echo @$_POST['name']; ?>" name="name" class="form-control" title="<?php echo lang('Nama Member')?>" placeholder="<?php echo lang('Nama Member')?>" req="any true" type="text" />
					</div>
					<div class="form-group">
						<label><?php echo lang('Phone')?></label>
						<input value="<?php echo @$_POST['params']['Phone']; ?>" name="params[Phone]" class="form-control" title="<?php echo lang($custom_fields['Phone']['title']) ?>" placeholder="<?php echo lang($custom_fields['Phone']['title']) ?>" req="phone false" type="text" />
						<p class="help-block"> <?php echo lang($custom_fields['Phone']['tips']) ?></p>
					</div>
					<div class="form-group">
						<label><?php echo lang('Alamat') ?></label>
						<input value="<?php echo @$_POST['params']['location_address']; ?>" name="params[location_address]" class="form-control" title="<?php echo lang('Alamat')?>" placeholder="<?php echo lang('Alamat')?>" req="any true" type="text" />
						<p class="help-block"> <?php echo lang($custom_fields['location_address']['tips']) ?></p>
					</div>
					<div class="form-group location_id">
						<label><?php echo lang('Lokasi') ?></label>
						<input value="<?php echo @$_POST['params']['location_id']; ?>" name="params[location_id]" class="form-control" req="any true" rel="ac" type="text" placeholder="<?php echo lang('Kecamatan / Kota') ?>" data-token="<?php echo encode(json_encode($location_token)); ?>" />
						<p class="help-block"> <?php echo lang($custom_fields['location_id']['tips']) ?></p>
					</div>
					<div class="form-group">
						<label><?php echo lang('Posisi Domisili') ?></label>
						<div class="form-inline">
							<input value="<?php echo @$_POST['params']['location_latlong']; ?>" name="params[location_latlong]" class="form-control" title="<?php echo lang('Latitude Longitude')?>" placeholder="<?php echo lang('Latitude Longitude')?>" req="any false" type="text" />
							<div class="input-group">
								<a href="#marker" class="btn btn-default btn-sm gmap_marker"> <i class="glyphicon glyphicon-map-marker" title="map marker"></i></a>
							</div>
						</div>
						<p class="help-block"> <?php echo lang($custom_fields['location_latlong']['tips']) ?></p>
					</div>
				</div>
  		</div>
  		<div class="col-sm-6">
				<div class="form-group">
					<label><?php echo lang('PIN Serial')?></label>
					<input value="<?php echo @$_POST['params']['pin']; ?>" name="params[pin]" class="form-control" title="<?php echo lang('PIN Serial')?>" placeholder="<?php echo lang('PIN Serial')?>" req="any true" type="text">
				</div>
				<div class="no-clonner">
					<div class="form-group">
						<label><?php echo lang('Upline');?></label>
						<input value="<?php echo @$_POST['params']['upline']; ?>" name="params[upline]" class="form-control" req="any true" type="text" placeholder="<?php echo lang('Member Upline') ?>" />
						<p class="help-block"> <?php echo lang($custom_fields['upline']['tips']) ?></p>
					</div>
					<div class="form-group">
						<label><?php echo lang('Posisi Titik')?></label>
						<div class="radio">
							<label>
								<input name="params[position]" value="0" type="radio"<?php echo is_checked(empty($_POST['params']['position'])); ?> id="position0" /><?php echo lang('Kiri')?></label>
							<label>
								<input name="params[position]" value="1" type="radio"<?php echo is_checked(!empty($_POST['params']['position']));?> id="position1" /><?php echo lang('Kanan')?></label>
						</div>
					</div>
					<?php
					$params = array(
						'title'       => 'Header of form or title',
						'table'       => 'bbc_account',
						'config_pre'  => array() ,
						'config'      => $user_fields,
						'name'        => 'params',
						'config_post' => array()
						);
					$f = _class('params', $params);
					echo $f->show_param($f->config, @$_POST['params'], $params['name']);
					?>
				</div>
  		</div>
		</div>
		<div class="clearfix"></div>
		<div class="panel-footer">
			<button type="submit" name="add_submit_add" value="Register" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-floppy-disk"></span> <?php echo lang('Register') ?></button>
			<button type="reset" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-repeat"></span> <?php echo lang('RESET') ?></button>
		</div>
	</div>
</form>
<script type="text/javascript">
	_Bbc(function ($) {
		window.LatLng = $("[name=params\\[location_latlong\\]]");
	  $(".gmap_marker").on("click", function (e) {
	    e.preventDefault();
	    var a = $(".location_id .ac_input").val() || "";
	    if (a == "") {
	      alert("masukkan nama kecamatan atau kota terlebih dahulu");
	      $(".location_id .ac_input").focus()
	    } else {
	      var b = encodeURIComponent($(window.LatLng).val());
	      var c = window.open(_URL+"bin/register?act=picker&id=" + encodeURIComponent(a) + "&latlong=" + b, "maps", "width=800, height=600, align=top, scrollbars=yes, status=no, resizable=yes");
	      c.focus()
	    }
	  });
	  window.LatLng.on("focus", function (e) {
	    e.preventDefault();
	    $(".gmap_marker").focus();
	    $(".gmap_marker").trigger("click")
	  });
	});
	var LatLng = {};
	function setPosition(a, b) {
	  LatLng.val(a + "," + b);
	  return false
	};
</script>