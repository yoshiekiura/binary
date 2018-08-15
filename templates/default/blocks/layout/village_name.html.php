<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$cfg = get_config('village', 'village');
if (!empty($cfg['address']))
{
	?>
	<h2><?php echo $cfg['address']; ?></h2>
	<?php
}
