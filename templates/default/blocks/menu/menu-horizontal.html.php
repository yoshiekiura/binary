<?php

if (!function_exists('default_menu_horizontal'))
{
	function default_menu_horizontal($menus, $y='', $x='', $level = -1) // $y = 'down' || 'top' AND $x = 'right'|| 'left'
	{
		$output = '';
		if (!empty($menus))
		{
			if ($level == -1)
			{
				$output = call_user_func(__FUNCTION__, menu_parse($menus), $y,$x,++$level);
			}else
			if (empty($level))
			{
				$cls = !empty($y) ? ' nav-'.$y : '';
				$cls.= !empty($x) ? ' nav-'.$x : '';
				$out = '';
				foreach ($menus as $menu)
				{
					$sub = call_user_func(__FUNCTION__, $menu['child'], $y,$x,++$level);
					if (!empty($sub))
					{
						$out .= '<li><a href="'.$menu['link'].'" title="'.$menu['title'].'">'.$menu['title'].'</a><ul class="dropdown">'.$sub.'</ul></li>';
					}else{
						$act = @$_GET['menu_id']==$menu['id'] ? ' class="active"' : '';
						$out.= '<li'.$act.'><a href="'.$menu['link'].'" title="'.$menu['title'].'">'.$menu['title'].'</a></li>';
					}
				}
				$output = '
				<nav id="mega-menu-holder">
					<ul class="clearfix'.$cls.'">
					'.$out.'
					</ul>
				</nav>';
			}else {
				$out = '';
				foreach ($menus as $menu)
				{
					$sub = call_user_func(__FUNCTION__, $menu['child'], $y,$x,++$level);
					if (!empty($sub))
					{
						$out .= '<li><a href="'.$menu['link'].'" title="'.$menu['title'].'">'.$menu['title'].'</a><ul class="dropdown">'.$sub.'</ul></li>';
					}else{
						$act = @$_GET['menu_id']==$menu['id'] ? ' class="active"' : '';
						$out.= '<li'.$act.'><a href="'.$menu['link'].'" title="'.$menu['title'].'">'.$menu['title'].'</a></li>';
					}
				}
				$output = '<ul class="clearfix">'.$out.'</ul>';
			}
		}
		return $output;
	}
}
$r = explode(' ', $config['submenu']);
$y = @$r[0]=='top' ? 'top' : '';
$x = @$r[1]=='left' ? 'left' : '';
echo default_menu_horizontal($menus, $y, $x);
