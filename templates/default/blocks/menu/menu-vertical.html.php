<?php

if (!function_exists('default_menu_vertical'))
{
	function default_menu_vertical($menus, $level = -1, $id='')
	{
		$output = '';
		if (!empty($menus))
		{
			if ($level == -1)
			{
				$output = call_user_func(__FUNCTION__, menu_parse($menus), ++$level);
			}else
			if (empty($level))
			{
				global $Bbc;
				if (empty($Bbc))
				{
					$Bbc = new stdClass;
				}
				if (empty($Bbc->menu_vertical))
				{
					$Bbc->menu_vertical = 1;
				}else{
					$Bbc->menu_vertical++;
				}
				$id = 'menu_v'.$Bbc->menu_vertical;
				$out = '';
				foreach ($menus as $menu)
				{
					$sub = call_user_func(__FUNCTION__, $menu['child'], ++$level, $id);
					if (!empty($sub))
					{
						$out .= '<li><a href="#'.$id.$level.'" class="tran3s" data-toggle="collapse" data-parent="#'.$id.'" title="'.$menu['title'].'" aria-expanded="true">'.$menu['title'].' <span class="caret down"></span></a></li>';
						$out .= $sub;
					}else{
						$act = @$_GET['menu_id']==$menu['id'] ? ' active' : '';
						$out .= '<li><a href="'.$menu['link'].'" class="tran3s'.$act.'" data-parent="#'.$id.'" title="'.$menu['title'].'">'.$menu['title'].'</a></li>';
					}
				}
				$output = '<ul id="'.$id.'">'.$out.'</ul>';
			}else {
				$id .= $level;
				$out = '';
				foreach ($menus as $menu)
				{
					$sub = call_user_func(__FUNCTION__, $menu['child'], ++$level, $id);
					if (!empty($sub))
					{
						$out .= '<li><a href="#'.$id.$level.'" class="tran3s" data-toggle="collapse" data-parent="#'.$id.'" title="'.$menu['title'].'">'.$menu['title'].' <span class="caret down"></span></a></li>';
						$out .= $sub;
					}else{
						$act = @$_GET['menu_id']==$menu['id'] ? ' active' : '';
						$out .= '<li><a href="'.$menu['link'].'" class="tran3s'.$act.'" data-parent="#'.$id.'" title="'.$menu['title'].'">'.$menu['title'].'</a></li>';
					}
				}
				$output = '<ul id="'.$id.'" aria-expanded="false" class="collapse">'.$out.'</ul>';
			}
		}
		return $output;
	}
}
echo default_menu_vertical($menus);