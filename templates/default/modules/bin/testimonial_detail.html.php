<?php
if (empty($data))
{
	?>
	<div class="container error-page">
		<h2 class="f-p-color">404</h2>
		<h3><?php echo lang('no data found'); ?></h3>
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
	<div class="shop-details">
		<div class="single-product-details clearfix">
			<?php echo $image; ?>
			<div class="product-order-details float-left">
				<h3><?php echo $data['name']; ?> </h3>
				<ul class="tag">
					<li class="help-block"><?php echo $data['location_name']; ?></li>
				</ul>
				<p><?php echo $data['detail']; ?></p>
			</div>
		</div>
	</div>
	<?php
}