<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($cat['list']) && is_array($cat['list']))
{
	$first = array_slice($cat['list'], 0, 2);
	$end   = array_slice($cat['list'], 2);

	?>
	<div class="row">
		<div class="blog-grid-post col-md-8 col-xs-12">
			<div class="row">
				<?php
				foreach ($first as $key => $data)
				{
					$edit_data = (content_posted_permission() && $user->id == $data['created_by']) ? 1 : 0;
					$link      = content_link($data['id'], $data['title']);
					$image     = (!empty($config['thumbnail']) && !empty($data['image'])) ? content_src($data['image'], true, false) : '';
					?>
					<div class="col-sm-6">
						<div class="single-blog">
							<div class="img">
								<?php echo $image ?>
							</div>
							
							<div class="post">
								<?php
								if(	!empty($config['created']) || !empty($config['author'] ))
								{
									?>
									<span>
										<?php 
											echo (!empty($config['created'])) ? content_date($data['created']) : '';
											echo (!empty($config['author'])) ? ' / '.$data['created_by_alias'] : '';
										?>
									</span>
									<?php
								}

								if(!empty($config['title']))
								{
									if(!empty($config['title_link']))
									{
										?>
										<h5><a href="<?php echo $link;?>" class="tran3s"><?php echo $data['title'];?></a></h5>
						        <?php
						      }else{
						      	?>
						      	<h5><?php echo $data['title'];?></h5>
						        <?php
						      }
								}
								?>
								<p><?php echo @$data[$config['intro']];?></p>
								<?php 
								echo (!empty($config['read_more'])) ? '<a href="'.$link.'" class="learn-more tran3s">'.lang('Read more').'</a>' : '';

								/*additional data*/
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
											echo ($config['modified']) ? '<div class="col-md-7 text-right"><span class="text-muted">'.lang('modified').content_date($data['modified']).'</span></div>' : '';?>
											<div class="clearfix"></div>
											<?php
										}
										?>
									</div>
									<?php
								}
								?>
							</div>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		</div>

		<div class="blog-side-post col-md-4 col-sm-6 col-xs-12">
			<div class="blog-side-carousel">
				<?php
				$r_datas = array_chunk($end, 4);

				foreach ($r_datas as $datas)
				{
					?>
					<div class="item">
						<?php
						foreach ($datas as $k => $data) 
						{
							$edit_data = (content_posted_permission() && $user->id == $data['created_by']) ? 1 : 0;
							$link      = content_link($data['id'], $data['title']);
							$image     = (!empty($config['thumbnail']) && !empty($data['image'])) ? content_src($data['image']) : '';

							?>
							<div class="post-wrapper">
								<div class="show-post clearfix">
									<img src="<?php echo $image ?>" alt="<?php echo $data['title'] ?>" class="float-left">
									<div class="post float-left">
										<?php 
										if(!empty($config['title']))
										{
											if(!empty($config['title_link']))
											{
												?>
												<h5><a href="<?php echo $link;?>" class="tran3s"><?php echo $data['title'];?></a></h5>
								        <?php
								      }else{
								      	?>
								      	<h5><?php echo $data['title'];?></h5>
								        <?php
								      }
										}
										?>

										<?php
										if(	!empty($config['created']) || !empty($config['author'] ))
										{
											?>
											<p>
												<?php 
													echo (!empty($config['created'])) ? content_date($data['created']) : '';
													echo (!empty($config['author'])) ? ' / '.$data['created_by_alias'] : '';
												?>
											</p>
											<?php
										}
										?>
									</div>
								</div>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>

	<?php
}