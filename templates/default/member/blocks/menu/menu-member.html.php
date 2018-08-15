<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$icons = array(
	'dashboard'          => 'fa-home',
	'profil-keanggotaan' => 'fa-id-card-o',
	'jaringan'           => 'fa-sitemap',
	'genealogy'          => 'fa-sitemap',
	'downline'           => 'fa-users',
	'sponsor'            => 'fa-address-book-o',
	'komisi'             => 'fa-bank',
	'status-komisi'      => 'fa-money',
	'komisi-bulanan'     => 'fa-bar-chart',
	'histori-komisi'     => 'fa-calendar-plus-o',
	'histori-transfer'   => 'fa-calendar-check-o',
	'reward'             => 'fa-gift',
	'data-reward'        => 'fa-trophy',
	'histori-reward'     => 'fa-history',
	'pesan'              => 'fa-envelope',
	'testimoni'          => 'fa-thumbs-up',
	'my-profile'         => 'fa-id-badge',
	'my-content'         => 'fa-comments-o',
	'create-content'     => 'fa-file-audio-o',
	'change-password'    => 'fa-unlock-alt',
	'logout'             => 'fa-sign-out',
	);
foreach ($menus as $i => $menu)
{
	$icon = 'fa-eercast';
	if (preg_match('~/([^/]+)\.html~is', $menu['link'], $m))
	{
		$seo = $m[1];
		if (!empty($icons[$seo]))
		{
			$icon = $icons[$seo];
		}
	}
	$menus[$i]['title'] = icon($icon).' '.$menu['title'];
}
echo menu_vertical($menus);
