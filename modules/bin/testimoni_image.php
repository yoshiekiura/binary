<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @$_GET['id'];
if (!empty($id) || !empty($_GET['authcode']))
{
	$data = $sys->login($id);
	if (!empty($data['image']))
	{
		$q = "UPDATE `bbc_account` SET `image`='{$data['image']}' WHERE `user_id`={$user->id}";
		if ($db->Execute($q))
		{
			$_SESSION[bbcAuth]['image'] = $data['image'];
			redirect();
		}else{
			echo msg(lang('Image profile anda tidak bs di update', 'danger'));
		}
	}else{
		echo msg(lang('Maaf, terjadi kesalahn oleh system dalam mengambil data image anda, mohon ulangi lagi'), 'warning');
	}
}
$accounts = ['facebook', 'twitter', 'google', 'instagram', 'linkedin', 'yahoo'];
$base_url = seo_uri();
$base_url.= preg_match('~\?~s', $base_url) ? '&' : '?';
$base_url.= 'id=';
include tpl('testimoni_image.html.php');
