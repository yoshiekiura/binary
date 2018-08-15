<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

foreach ($output['data'] as $data)
{
	?>
	<div class="single-news">
		<h5><a href="<?php echo $data['href']; ?>" class="tran3s"><?php echo $data['title'];?></a></h5>
	</div>
	<?php
}