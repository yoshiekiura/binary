<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$config = $output['config'];
$arr    = $output['data'];
$k      = 0;

foreach((array)$arr AS $i => $data)
{
	$j = $i ? $i/10 : 0;
	$k += $j;
	$l = $k ? ' data-wow-delay="'.$k.'s"' : '';
	?>
	<div class="col-md-4 col-sm-6 col-xs-12 wow fadeInUp"<?php echo $l; ?>>
		<div class="single-offer">
			<?php
			$link = content_link($data['id'], $data['title']);
			echo (!empty($config['thumbnail']) && !empty($data['image'])) ? '<a href="'.$link.'" title="'.$data['title'].'">'.content_src($data['image'], ' class="img-thumbnail icon" alt="'.$data['title'].'"').'</a>' : '';
			if($config['title'])
			{
				if($config['title_link'])
				{
					?>
					<h5><a href="<?php echo $link;?>" title="<?php echo $data['title'];?>" class="tran3s"><?php echo $data['title'];?></a></h5>
					<?php
				}else{
					?>
					<h5><?php echo $data['title'];?></h5>
					<?php
				}
			}
			if(	$config['created'] || $config['author'] )
			{
				?>
				<hr />
				<div class="row">
					<?php echo ($config['author']) ? '<div class="col-md-6"><span>'.lang('author').$data['created_by_alias'].'</span></div>' : '';?>
					<?php echo ($config['created']) ? '<div class="col-md-6 text-right"><span>'.lang('created').content_date($data['created']).'</span></div>' : '';?>
					<div class="clearfix"></div>
				</div>
				<?php
			}
			?>
			<p>
				<?php echo $data['content'];?>
				<?php echo ($config['read_more']) ? '<a href="'.$link.'" class="readmore">'.lang('Read more').'</a>' : '';?>
				<div class="clearfix"></div>
			</p>
			<?php
			if($config['tag'])
			{
				$r = content_category($data['id'], $config['tag_link']);
				echo '<div>'.lang('Tags').implode(' ', $r).'</div>';
			}
			if(	$config['rating'] || $config['modified'] )
			{
				?>
				<div class="row">
					<?php
					if ($config['rating'])
					{
						?>
						<div class="col-md-6 no-both">
							<?php echo rating($data['rating']); ?>
						</div>
						<?php
					}
					if(empty($data['revised']))
					{
						$config['modified'] = 0;
					}
					if (!empty($config['modified']))
					{
						?>
						<div class="col-md-6 no-left text-right">
							<em class="text-right pull-right"><?php echo lang('modified').content_date($data['modified']); ?></em>
						</div>
						<?php
					}
					?>
					<div class="clearfix"></div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}
