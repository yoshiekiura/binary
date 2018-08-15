<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

?>
<div>
	<h1><?php echo $data['name']; ?></h1>
</div>
<div>
	<?php
	if (!empty($data['images']))
	{
		$images = @json_decode($data['images'], 1);
		if (!empty($images) && is_array($images))
		{
			$path     = 'images/modules/bin/reward/'.$data['id'].'/';
			$indicate = '';
			$inner    = '';
			foreach ($images as $i => $d)
			{
				if (is_file(_ROOT.$path.$d['image']))
				{
					$isActive = '';
					if (empty($hasActive))
					{
						$isActive  = 'active';
						$hasActive = 1;
					}
					$indicate .= '<li data-target="#content-gallery" data-slide-to="'.$i.'" class="'.$isActive.'"></li>';
					$inner .= '<div class="item '.$isActive.'">';
					$inner .= '<center><img src="'._URL.$path.$d['image'].'" alt="'.$d['title'].'" class="img-thumbnail img-responsive"></center>';
					if (!empty($d['title']) || !empty($d['description']))
					{
						$inner .= '<div class="carousel-caption">';
						if (!empty($d['title']))
						{
							$inner .= '<h3>'.$d['title'].'</h3>';
						}
						if (!empty($d['description']))
						{
							$inner .= '<p>'.$d['description'].'</p>';
						}
						$inner .= '</div>';
					}

					$inner .= '</div>';
				}
			}
			if (!empty($inner))
			{
				?>
				<div id="content-gallery" class="carousel slide" data-ride="carousel">
				  <ol class="carousel-indicators"><?php echo $indicate; ?></ol>
				  <div class="carousel-inner" role="listbox"><?php echo $inner; ?></div>
				  <?php
				  if (count($images) > 1)
				  {
				  	?>
					  <a class="left carousel-control" href="#content-gallery" role="button" data-slide="prev">
					    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
					    <span class="sr-only"><?php echo lang('Previous'); ?></span>
					  </a>
					  <a class="right carousel-control" href="#content-gallery" role="button" data-slide="next">
					    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
					    <span class="sr-only"><?php echo lang('Next'); ?></span>
					  </a>
				  	<?php
				  }
				  ?>
				</div>
				<?php
			}
		}
	}
	if (!empty($data['description']))
	{
		echo "<div style='margin-top:15px; margin-bottom:5px'>".$data['description']."</div>";
	}
	echo "<div style='margin-top:15px; margin-bottom:5px'><b>".lang('Syarat Mimimum')."</b></div>";
	?>
	<table class="table table-striped table-bordered table-sm">
		<tbody>
			<tr>
				<td><?php echo lang('Sponsor')?></td>
				<td><?php echo lang('Kiri'); ?></td>
				<td><?php echo lang('Kanan'); ?></td>
				<?php
				if (isset($data['level']))
				{
					?>
					<td><?php echo lang('Level'); ?></td>
					<?php
				}
				if (isset($data['serial']))
				{
					?>
					<td><?php echo lang('Serial'); ?></td>
					<?php
				}
				?>
				<td><?php echo lang('Akumulasi'); ?></td>
			</tr>
			<tr>
				<td><?php echo $data['total_sponsor']; ?></td>
				<td><?php echo $data['total_left']; ?></td>
				<td><?php echo $data['total_right']; ?></td>
				<?php
				if (isset($data['level']))
				{
					?>
					<td><?php echo $data['level']; ?></td>
					<?php
				}
				if (isset($data['serial']))
				{
					?>
					<td><?php echo $data['serial']; ?></td>
					<?php
				}
				?>
				<td><?php echo $data['accumulate']; ?></td>
			</tr>
		</tbody>
	</table>
	<?php
	if (isset($data['status']))
	{
		?>
		<span class="<?php echo $data['cstatus'] ?>"><?php echo $data['status']; ?></span>
		<?php
	}
	if (isset($data['claim_id']))
	{
		if (!empty($data['claim_id']))
		{
			?>
			<form action="" method="POST" class="form-inline pull-right" role="form">
				<button type="submit" name="claim_id" value="<?php echo @$data['claim_id']; ?>" class="btn btn-warning btn-sm">
					<?php echo icon('ok').' '.lang('Claim Reward'); ?>
				</button>
			</form>
			<?php
		}
	}
	?>
</div>