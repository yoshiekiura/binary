<div class="jumbotron">
	<div class="container">
		<h1><?php echo lang('Tentukan image profile') ?></h1>
		<p><?php echo lang('sebelum menggunakan fitur testimonial, anda harus menentukan gambar profile anda, dengan menggunakan salah satu sosial media yang anda miliki dibawah') ?></p>
		<p>
			<?php
			foreach ($accounts as $account)
			{
				$link = $base_url.$account;
				?>
				<div class="col-lg-4 col-md-6 col-xs-6 text-center">
					<div class="single-product">
						<div class="image">
							<a href="<?php echo $link; ?>">
								<i class="fa fa-<?php echo $account; ?> fa-5x fa-fw" aria-hidden="true"></i>
							</a>
						</div>
						<div class="info">
							<h6><a href="<?php echo $link; ?>" class="tran3s"><?php echo ucwords($account); ?></a></h6>
						</div>
						<p>&nbsp;</p>
					</div>
				</div>
				<?php
			}
			?>
			<p class="help-block"><small><?php echo lang('klik icon atau link diatas!'); ?></small></p>
		</p>
	</div>
</div>
<?php
icon('fa-google');