<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

?>
<div class="reward_list">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo lang('Data Reward'); ?></h3>
		</div>
		<div class="panel-body">
			<?php
	    foreach ($r_data as $key => $data)
	    {
        ?>
				<ul class="list-unstyled">
					<li>
						<div class="row" style="padding-bottom: 0px" >
							<div class="col-md-3 text-center no-both">
								<a href="<?php echo $data['link']; ?>">
									<img src="<?php echo $data['image']; ?>" class="img-thumbnail img-responsive" style="object-fit:contain;" />
								</a>
							</div>
							<div class="col-md-9">
								<div>
									<a href="<?php echo $data['link']; ?>" style="display: block;">
										<h5>
											<b><?php echo $data['name']; ?></b>
											<?php
											if (isset($data['status']))
											{
												?>
												<small class="<?php echo $data['cstatus'] ?> pull-right"><?php echo $data['status']; ?></small>
												<?php
											}
											?>
										</h5>
									</a>
								</div>
								<table class="table table-sm">
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
								if (isset($data['claim_id']))
								{
									if (!empty($data['claim_id']))
									{
										?>
										<form action="" method="POST" class="form-inline pull-right" role="form">
											<button type="submit" name="claim_id" value="<?php echo @$data['claim_id']; ?>" class="btn btn-warning btn-sm" style="margin-top: -25px;" >
												<?php echo icon('ok').' '.lang('Claim Reward'); ?>
											</button>
										</form>
										<?php
									}
								}
								?>
							</div>
						</div>
						<hr>
					</li>
				</ul>
				<?php
			}
		  ?>
		</div>
	</div>
</div>