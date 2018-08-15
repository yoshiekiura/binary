<div class="theme-inner-banner">
	<div class="opacity">
		<h2><?php echo $cat['title'];?></h2>
	</div>
</div>
<div class="blog-grid-post">
	<div class="row">
		<?php
		$item = 0;
		foreach((array)$cat['list'] AS $data)
		{
			$item++;
			$edit_data = (content_posted_permission() && $user->id == $data['created_by']) ? 1 : 0;
			$link      = content_link($data['id'], $data['title']);
			?>
			<div class="col-md-4 col-xs-6">
				<div class="single-blog">
					<?php
					if (!empty($config['thumbnail']))
					{
						?>
						<a href="<?php echo $link;?>" title="<?php echo $data['title'];?>">
							<div class="img">
								<img src="<?php echo content_src($data['image'], false, true); ?>" alt="<?php echo $data['title']; ?>" />
							</div>
						</a>
						<?php
					}
					?>
					<div class="post">
						<span>
							<?php
							if(	!empty($config['created']) || !empty($config['author'] ))
							{
								echo (!empty($config['created'])) ? content_date($data['created']) : '';
								echo (!empty($config['author'])) ? ' / '.$data['created_by_alias'] : '';
							}
							?>
						</span>
						<h5>
							<?php
							if(!empty($config['title']))
							{
								if(!empty($config['title_link']))
								{
									?>
									<a href="<?php echo $link;?>" title="<?php echo $data['title'];?>" class="tran3s"><?php echo $data['title'];?></a>
					        <?php
					      }else{
					      	echo $data['title'];
					      }
							}
							?>
						</h5>
						<p><?php echo substr(strip_tags($data['intro']), 0, 80); ?></p>
						<?php
						echo (!empty($config['read_more'])) ? ' <a href="'.$link.'" class="learn-more tran3s">'.lang('Read more').'</a>' : '';
						if( !empty($config['tag']) )
						{
							?>
							<div class="text-left">
								<?php
								$r = content_category($data['id'], $config['tag_link']);
								echo lang('Tags').implode(' ', $r);
								?>
							</div>
							<?php
						}
						if(empty($data['revised']))
						{
							$config['modified'] = 0;
						}
						if(!empty($config['rating']) || !empty($config['modified']) || !empty($edit_data))
						{
							?>
							<div class="row">
								<?php
								if($config['rating'])
								{
									echo '<div class="col-md-5">'.rating($data['rating']).'</div>';
								}
								if(!empty($edit_data))
								{
									?>
									<div class="col-md-7 text-right">
										<?php echo ($config['modified']) ? '<span class="text-muted">'.lang('modified').content_date($data['modified']).'</span>' : '';?>
										<a href="<?php echo $Bbc->mod['circuit'].'.posted_form&id='.$data['id'];?>" title="<?php echo lang('edit content');?>"><?php echo icon('edit');?></a>
									</div>
									<?php
								}	else {
									echo ($config['modified']) ? '<div class="col-md-7 text-right"><span class="text-muted">'.content_date($data['modified']).'</span></div>' : '';?>
									<div class="clearfix"></div>
									<?php
								}
								?>
							</div>
							<?php
						}
						?>
					</div> <!-- /.post -->
				</div> <!-- /.single-blog -->
			</div> <!-- /.col- -->
			<?php
			if ($item%3 == 0) echo '<div class="clearfix"></div>';
		}
		?>
	</div> <!-- /.row -->
	<?php echo str_replace('class="pagination"', 'class="page-pagination center"', page_list($cat['total'], $config['tot_list'], $page, 'page', $cat['link'])); ?>
</div> <!-- /.blog-grid-post -->