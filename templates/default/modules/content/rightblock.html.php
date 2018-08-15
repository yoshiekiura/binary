<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

?>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 blog-sidebar theme-blog-sidebarOne">
	<?php
	$q = "SELECT a.`id`, t.`title` FROM ".lang_sql('bbc_content_cat', 'cat_id')." WHERE a.`publish`=1 ORDER BY RAND() LIMIT 10";
	$r = $db->cacheGetAssoc($q);
	if (!empty($r))
	{
		?>
		<div class="sidebar-categories">
			<h4><?php echo lang('Categories'); ?></h4>
			<ul>
				<?php
				foreach ($r as $i => $t)
				{
					?>
					<li><a href="<?php echo content_cat_link($i, $t) ?>" title="<?php echo $t; ?>" class="tran3s"><?php echo $t; ?></a></li>
					<?php
				}
				?>
			</ul>
		</div> <!-- /.sidebar-categories -->
		<?php
	}
	?>

	<div class="sidebar-recent-news">
		<h4><?php echo lang('Recent News'); ?></h4>
		<?php
		$q = "SELECT a.`id`, a.`image`, a.`created`, t.`title` FROM ".lang_sql('bbc_content', 'content_id')." WHERE a.`publish`=1 ORDER BY id DESC LIMIT 4";
		$r = $db->cacheGetAssoc($q);
		if (!empty($r))
		{
			?>
			<ul>
				<?php
				foreach ($r as $i => $d)
				{
					$ok = $Bbc->mod['task']=='detail' ? ($i!=$id ? true : false) : true;
					if ($ok)
					{
						?>
						<li class="clearfix">
							<img src="<?php echo content_src($d['image'], false); ?>" alt="<?php echo $d['title']; ?>" class="float-left">
							<div class="post float-left">
								<h6><a href="<?php echo content_link($i, $d['title']); ?>" title="<?php echo $d['title']; ?>" class="tran3s"><?php echo $d['title']; ?></a></h6>
								<span><?php echo content_date($d['created']); ?></span>
							</div> <!-- /.post -->
						</li>
						<?php
					}
				}
				?>
			</ul>
			<?php
		}
		?>
	</div> <!-- /.sidebar-recent-news -->
	<?php
	$q = "SELECT `id`, `title` FROM `bbc_content_tag` WHERE 1 ORDER BY `total` DESC LIMIT 10";
	$r = $db->cacheGetAssoc($q);
	if (!empty($r))
	{
		?>
		<div class="sidebar-keywords">
			<h4><?php echo lang('Keyword'); ?></h4>
			<ul>
				<?php
				foreach ($r as $i => $t)
				{
					?>
					<li><a href="<?php echo content_tag_link($i, $t) ?>" title="<?php echo $t; ?>" class="tran3s"><?php echo $t; ?></a></li>
					<?php
				}
				?>
			</ul>
		</div> <!-- /.sidebar-keywords -->
		<?php
	}
	?>
</div> <!-- /.blog-sidebar -->
