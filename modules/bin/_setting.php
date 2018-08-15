<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$plan_a = get_config('bin', 'plan_a');
if (!empty($user->id))
{
	$Bbc->member = $db->getRow("SELECT * FROM `bin` WHERE `user_id`={$user->id}");
}
if (empty($Bbc->member))
{
	if (!in_array($Bbc->mod['task'], ['fetch', 'fix', 'testimonial', 'testimonial_detail']))
	{
		$Bbc->mod['task'] = 'register';
	}
}else{
	/* CHANGE TEMPLATE IF ALREADY LOGIN AS MEMBER */
	if (in_array($plan_a['group_id'], $user->group_ids))
	{
		$template = $sys->layout_fetch();
		if ($template!='default/' && file_exists(_ROOT.'templates/'.$template.'member/'))
		{
			$_CONFIG['template'] = 'default';
		}
		$sys->template_url = _URL.'templates/'.$_CONFIG['template'].'/member/';
		$sys->template_dir = _ROOT.'templates/'.$_CONFIG['template'].'/member/';
		$sys->set_layout();
		$sys->link_set($sys->template_url.'css/style.css', 'css');
		$sys->link_set($sys->template_url.'js/script.js', 'js');
	}
}
