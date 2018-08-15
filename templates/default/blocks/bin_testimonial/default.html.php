<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($r_data))
{
	foreach ($r_data as $data)
	{
		if (!empty($data['image']))
		{
			$src = is_url($data['image'])? $data['image'] : _URL.'modules/bin/'.$data['image'];
		}else{
			$src = '';
		}
		$link = 'index.php?mod=bin.testimonial_detail&id='.$data['id'];
		?>
		<div class="item">
			<div class="wrapper clearfix">
				<a href="<?php echo $link; ?>">
					<div class="icon f-p-bg-color"><i class="fa fa-quote-left" aria-hidden="true"></i></div>
					<?php
					if (!empty($src))
					{
						?>
						<img src="<?php echo $src; ?>" alt="<?php echo $data['name'];?>" class="float-left round-border">
						<?php
					}
					?>
					<div class="text float-left">
						<h5><?php echo $data['name'];?></h5>
						<h6><?php echo $data['location_name'];?></h6>
						<p><?php echo substr(strip_tags($data['detail']), 0, 150);?></p>
					</div>
				</a>
			</div>
		</div>
		<?php
	}
}
