<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if (!empty($id))
{
	$user_id  = $db->getOne("SELECT `user_id` FROM `bin` WHERE id={$id}");

	$form = _lib('pea',  'bin');

	$form->initEdit('WHERE id='.$id);
	$form->edit->setColumn(2);

	$form->edit->addInput('header','header');
	$form->edit->input->header->setTitle('Member Data');

	$form->edit->addInput('username','sqlplaintext');
	$form->edit->input->username->setTitle('Serial');

	$form->edit->addInput('serial_pin','sqlplaintext', 2);
	$form->edit->input->serial_pin->setTitle('PIN');

	$r = config('plan_a', 'serial_list');
	if (count($r) > 1)
	{
		$form->edit->addInput( 'serial_type_id', 'selecttable' );
		$form->edit->input->serial_type_id->setTitle('Tipe Serial');
		$form->edit->input->serial_type_id->setReferenceTable('bin_serial_type');
		$form->edit->input->serial_type_id->setReferenceField( 'name', 'id' );
		$form->edit->input->serial_type_id->setPlaintext( true );
	}
	$form->edit->addInput('total_sponsor','sqlplaintext');
	$form->edit->input->total_sponsor->setTitle('Total Sponsor');
	$form->edit->input->total_sponsor->setNumberFormat();

	$form->edit->addInput('downline','multiinput', 2);
	$form->edit->input->downline->setTitle('Total Downline');
	$form->edit->input->downline->addInput('total_downline', 'sqlplaintext');
	$form->edit->input->downline->addInput('total_downline2', 'plaintext', ' (');
	$form->edit->input->downline->addInput('total_left', 'sqlplaintext');
	$form->edit->input->downline->addInput('total_downline3', 'plaintext', 'kiri, ');
	$form->edit->input->downline->addInput('total_right', 'sqlplaintext');
	$form->edit->input->downline->addInput('total_downline4', 'plaintext', 'kanan )');
	$form->edit->input->total_downline->setNumberFormat();
	$form->edit->input->total_left->setNumberFormat();
	$form->edit->input->total_right->setNumberFormat();

	$form->edit->addInput('depth','multiinput');
	$form->edit->input->depth->setTitle('Kedalaman Downline');
	$form->edit->input->depth->addInput('depth_left', 'sqlplaintext');
	$form->edit->input->depth->addInput('depth_left2', 'plaintext', ' kiri,');
	$form->edit->input->depth->addInput('depth_right', 'sqlplaintext');
	$form->edit->input->depth->addInput('depth_right2', 'plaintext', 'Kanan');
	$form->edit->input->depth_left->setNumberFormat();
	$form->edit->input->depth_right->setNumberFormat();

	$form->edit->addInput('level','multiinput', 2);
	$form->edit->input->level->setTitle('Level Jaringan');
	$form->edit->input->level->addInput('depth_upline', 'sqlplaintext');
	$form->edit->input->level->addInput('depth_upline2', 'plaintext', ' Titik,');
	$form->edit->input->level->addInput('depth_sponsor', 'sqlplaintext');
	$form->edit->input->level->addInput('depth_sponsor2', 'plaintext', 'Sponsor');
	$form->edit->input->depth_upline->setNumberFormat();
	$form->edit->input->depth_sponsor->setNumberFormat();

	$form->edit->setSaveTool(false);
	$form->edit->setResetTool(false);
	echo $form->edit->getForm();

	function _user_change($form)
	{
		global $user_id;
		user_call_func('user_change', $user_id);
	}
	$fields   = user_field($user_id);
	$editable = config('bin_fields', 'editable');
	$hidden   = array('serial','pin', 'sponsor', 'upline', 'position');
	link_js(_LIB.'pea/includes/FormTags.js', false);
	$token = array(
		'table'  => 'bin_location',
		'field'  => 'detail',
		'id'     => 'id',
		'expire' => strtotime('+2 HOURS'),
		);

	foreach ($fields as $i => $field)
	{
		if (in_array($field['title'], $hidden))
		{
			unset($fields[$i]);
		}else{
			if (!in_array($field['title'], $editable))
			{
				$fields[$i]['type'] = 'plain';
				$fields[$i]['help'] = '';
				$fields[$i]['tips'] = '';
			}else
			if ($field['title']=='location_id')
			{
				$fields[$i]['attr'] = ' rel="ac" data-token="'.encode(json_encode($token)).'"';
			}else
			if ($field['title']=='location_latlong')
			{
				$fields[$i]['add'] = '<a href="#marker" class="btn btn-default btn-xs gmap_marker"> <i class="glyphicon glyphicon-map-marker" title="map marker"></i></a>';
			}
		}
	}
	if (in_array(1, $user->group_ids))
	{
		$btn_link = '<a href="index.php?mod=_cpanel.user&act=edit&id='.$user_id.'&return='.urlencode(seo_url()).'" rel="admin_link" class="btn btn-xs btn-danger pull-right">'.icon('fa-hashtag').' Change Data</a>';
	}else{
		$btn_link = '';
	}
	$params = array(
		'title'       => 'Member Profile'.$btn_link,
		'table'       => 'bbc_account',
		'config_pre'  => array(),
		'config'      => $fields,
		'config_post' => array(),
		'post_func'   => '_user_change',
		'name'        => 'params',
		'id'          => $db->getOne("SELECT `id` FROM `bbc_account` WHERE `user_id`={$user_id}")
		);
	$params['config_pre'] = array(
		'name' => array(
			'text'      => 'Name',
			'type'      => 'text',
			'mandatory' => '1'
			),
		'email' => array(
			'text'      => 'Email',
			'type'      => 'text',
			'mandatory' => 1,
			'checked'   => 'email'
			)
		);
	if (empty($db->debug))
	{
		$params['config_post'] = array(
			'vcode' => array(
				'text' => 'Validation Code',
				'type' => 'captcha'
				)
			);
	}

	$form = _class('params');
	$form->set($params);
	echo $form->show();
	?>
	<script type="text/javascript">
		_Bbc(function($){
			var a = $("#imgLogin");
			var b = a.closest(".input-group");
			var c = $(".form-control", b);
			c.prop("id", "userimage")
			$("a", a).on("click", function(e){
				e.preventDefault();
				var d = $(this).attr("href").substr(1);
				window.open(_URL+"user/account_image/"+d+"?i=userimage", "userimage", "width=640,height=480,menubar=no,location=no,resizable=no,scrollbars=no,status=no");
			});
			window.LatLng = $("[name=params\\[location_latlong\\]]");
		  $(".gmap_marker").on("click", function (e) {
		    e.preventDefault();
		    var a = $(".ac_input").val() || "";
		    if (a == "") {
		      alert("masukkan nama kecamatan atau kota terlebih dahulu");
		      $(".ac_input").focus()
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
	<?php
}
