<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$cfg = get_config('village', 'village');
if (!empty($cfg))
{
	?>
	<ul>
		<li class="address">
			<div class="round-icon"><i class="icon flaticon-placeholder"></i></div>
			<h6><?php echo lang('Kantor Desa'); ?></h6>
			<p><?php echo $cfg['office']; ?></p>
		</li>
		<li class="address">
			<div class="round-icon"><i class="icon flaticon-multimedia"></i></div>
			<h6><?php echo $cfg['contact']; ?></h6>
			<p><?php echo lang('Jam Buka'); ?>: <?php echo $cfg['openhour']; ?></p>
		</li>
		<li><a href="contact" class="tran3s button-one"><?php echo lang('Hubungi Kami'); ?></a></li>
	</ul>
	<?php
}

