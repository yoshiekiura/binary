<h2><?php echo lang('Testimonial Member'); ?></h2>
<?php
if (empty($total))
{
	?>
	<div class="jumbotron">
	  <h1><?php echo lang('no testimonial to publish'); ?></h1>
		<p><?php echo lang('Please return to this page anytime soon to get new info if any member post testimonial'); ?></p>
	</div>
	<?php
}else{
	if (!empty($datas))
	{
		?>
		<div class="media-list">
		<?php
		foreach ($datas as $data)
		{
			$image = '';
			$link  = 'index.php?mod=bin.testimonial_detail&id='.$data['id'];
			if (!empty($data['image']))
			{
				$src   = is_url($data['image'])? $data['image'] : _URL.'modules/bin/'.$data['image'];
				$image = '<img class="media-object" src="'.$src.'" alt="'.$data['name'].'" title="'.$data['name'].'" />';
			}
			?>
			<div class="media">
			  <div class="media-left media-middle" style="min-width: 64px;">
			    <a href="<?php echo $link; ?>">
			      <?php echo $image; ?>
			    </a>
			  </div>
			  <div class="media-body">
					<h4 class="media-heading">
						<a href="<?php echo $link; ?>">
							<?php echo $data['name']; ?>
						</a>
					</h4>
			  	<small class="help-block"><?php echo $data['location_name']; ?></small>
			    <?php echo $data['detail']; ?>
			  </div>
			</div>
			<?php
		}
		?>
		</div>
		<?php
	}
	echo page_list($total, $limit, $page, 'page', $Bbc->mod['circuit'].'.'.$Bbc->mod['task']);
}