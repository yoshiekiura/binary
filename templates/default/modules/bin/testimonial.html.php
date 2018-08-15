<div class="theme-inner-banner">
	<div class="opacity">
		<h2 class="f-p-color"><?php echo lang('Testimonial Member'); ?></h2>
	</div>
</div>
<div class="our-service-style-one m-fix">
	<?php
	if (empty($total))
	{
		?>
		<div class="container error-page">
			<h2 class="f-p-color">404</h2>
			<h3><?php echo lang('no testimonial to publish'); ?></h3>
			<p><?php echo lang('Please return to this page anytime soon to get new info if any member post testimonial'); ?></p>
		</div>
		<?php
	}else
	if (!empty($datas))
	{
		?>
		<div class="container">
			<div class="row">
				<?php
				foreach ($datas as $data)
				{
					$image = '';
					$link  = 'index.php?mod=bin.testimonial_detail&id='.$data['id'];
					$more  = strlen($data['detail']) > 85 ? '...' : '';
					if (!empty($data['image']))
					{
						$src   = is_url($data['image'])? $data['image'] : _URL.'modules/bin/'.$data['image'];
						$image = '<img src="'.$src.'" alt="'.$data['name'].'" title="'.$data['name'].'" />';
					}
					?>
					<div class="col-md-3 col-xs-6 wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;height: 472px;overflow: hidden;">
						<div class="single-service tran3s">
							<center>
								<?php echo $image; ?>
								<a href="<?php echo $link; ?>" class="tran3s">
									<h5><?php echo $data['name']; ?></h5>
									<small class="help-block"><?php echo $data['location_name']; ?></small>
								</a>
							</center>
							<p><?php echo substr($data['detail'], 0, 85).$more; ?> </p>
							<a href="<?php echo $link; ?>" class="tran3s learn-more"><?php echo lang('More Detail'); ?> <i class="fa fa-angle-right" aria-hidden="true"></i></a>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}
	echo page_list($total, $limit, $page, 'page', $Bbc->mod['circuit'].'.'.$Bbc->mod['task']);
	?>
</div>