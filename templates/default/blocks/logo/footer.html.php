<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$title = $_CONFIG['site']['title'];
?>
<div class="col-md-3 col-sm-5 col-xs-12 footer-logo">
	<?php
	if(!empty($config['is_link']))
	{
	?>
		<a href="<?php echo $output['link'];?>" title="<?php echo $title;?>"<?php echo $output['attribute']; ?>>
			<?php echo image($output['image'], $output['size'], 'alt="'.$title.'" title="'.$title.'"');?>
		</a>
	<?php
	}else{
		echo image($output['image'], $output['size'], 'alt="'.$title.'" title="'.$title.'"'.$output['attribute']);
	}
	?>
	<p style="text-align: justify;"><?php echo $output['title']?></p>
</div>