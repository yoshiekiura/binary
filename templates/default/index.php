<!DOCTYPE html>
<html lang="en">
	<head><?php echo $sys->meta();?>
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body class="finance-theme">
		<div class="main-page-wrapper">

			<!--
			<div id="loader-wrapper">
				<div id="loader"></div>
			</div>
			-->

			<header class="finance-header">
				<div class="top-header">
					<div class="container">
						<div class="col-md-5 no-left">
							<?php echo $sys->block_show('logo'); ?>
						</div>
						<div class="col-md-7 no-both">
							<?php echo $sys->block_show('intro'); ?>
						</div>
					</div>
				</div>

				<div class="theme-main-menu">
				   <div class="container">
				   		<div class="main-container">
				   			<?php echo $sys->block_show('top'); ?>
				   		</div>
				   </div>
				</div>
			</header>

			<div class="container">
				<div class="row">
					<div class="col-md-4">
						<?php echo $sys->block_show('left'); ?>
					</div>
					<div class="col-md-8">
						<?php echo $sys->block_show('header'); ?>
					</div>
				</div>
			</div>

			<section class="finance-offer blog-inner-page blog-list our-blog-one">
				<div class="container">
					<div class="title">
						<?php echo $sys->block_show('content_top'); ?>
					</div>

					<div class="row">
						<?php echo trim($Bbc->content); ?>
					</div>
				</div>
			</section>

			<div class="our-blog-one">
				<?php echo $sys->block_show('content_bottom'); ?>
			</div>

			<?php echo $sys->block_show('bottom'); ?>

			<footer class="default-footer finance-footer">
				<div class="container">
					<div class="top-footer row">
						<?php echo $sys->block_show('footer'); ?>
					</div>
				</div>

				<div class="bottom-footer">
					<div class="container">
						<p class="float-left">
							<?php echo config('site','footer');?>
						</p>
						<div class="float-right">
							<?php echo $sys->block_show('right'); ?>
						</div>
					</div>
				</div>
			</footer>
			<script src="<?php echo _URL;?>templates/admin/bootstrap/js/bootstrap.min.js"></script>
			<?php
			$sys->link_js($sys->template_url.'js/application.js', false);
			echo $sys->block_show('debug');
			?>
		</div>
	</body>
</html>
