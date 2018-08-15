<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');?>
<div class="theme-inner-banner">
	<div class="opacity">
		<h2><?php echo lang('Contact Form');?></h2>
	</div>
</div>

<div class="container contact-us-page theme-contact-page-styleOne">
	<div class="row">
		<div class="col-md-6 col-sm-12 col-xs-12 wow fadeInLeft">
			<div class="contact-us-form">
				<?php echo $contact_form; ?>
			</div> <!-- /.contact-us-form -->
		</div> <!-- /.col- -->

		<div class="col-md-6 col-sm-12 col-xs-12 wow fadeInRight">
			<div class="contactUs-address">
				<?php echo get_config('contact', 'form', 'address');?>
			</div> <!-- /.our-address -->
		</div>
	</div> <!-- /.row -->
</div> <!-- /.container -->