<!DOCTYPE html>
<html lang="en">
	<head><?php echo $sys->meta();?>
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body style="background: #fff;">
		<?php echo trim($Bbc->content); ?>
		<script src="<?php echo _URL;?>templates/admin/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
		<?php echo $sys->link_js($sys->template_url.'js/application.js', false); ?>
	</body>
</html>