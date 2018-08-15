<?php
if (empty($data))
{
	?>
	<div class="jumbotron">
	  <h1><?php echo lang('no data found'); ?></h1>
		<p><?php echo lang('Please return to this page anytime soon to get new update'); ?></p>
	</div>
	<?php
}else{
	$image = '';
	if (!empty($data['image']))
	{
		$src   = is_url($data['image'])? $data['image'] : _URL.'modules/bin/'.$data['image'];
		$image = '<img src="'.$src.'" alt="'.$data['name'].'" title="'.$data['name'].'" class="float-left" />';
	}
	?>
	<div class="media-list">
		<div class="media">
		  <div class="media-left media-middle" style="min-width: 128px;text-align: center;">
		    <a href="<?php echo $link; ?>">
		      <?php echo $image; ?>
		    </a>
		  </div>
		  <div class="media-body">
				<h4 class="media-heading">
						<?php echo $data['name']; ?>
				</h4>
		  	<small class="help-block"><?php echo $data['location_name']; ?></small>
		    <?php echo $data['detail']; ?>
		  </div>
		</div>
	</div>
	<?php
}