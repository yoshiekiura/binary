<!DOCTYPE html>
<html lang="end">
	<head><?php echo $sys->meta();?>
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div>
			<div class="sidebar" id="sidebar__slider">
			  <ul class="sidebar__wrapper">
			    <li class="logo">
			      <div class="logo__image">
			        <div class="img-responsive">
			          <?php echo $sys->block_show('logo'); ?>
			        </div>
			      </div>
			    </li>
			    <li class="flex flex_column avatar">
			      <div class="avatar__image">
			      	<a href="<?php echo site_url('bin/profile'); ?>">
				        <div class="img-responsive">
				        	<?php
				        	if (empty($user->image))
				        	{
				        		$user->image = $sys->template_url.'images/avatar.png';
				        	}
				        	?>
				          <img src="<?php echo $user->image; ?>" alt="" />
				        </div>
			      	</a>
			      </div>
			      <div class="avatar__text">
			        <div class="text-center">
			          <p><?php echo $Bbc->member['name']; ?></p>
			          <p><?php echo lang('Serial') ?> : <?php echo $Bbc->member['username']; ?></p>
			        </div>
			      </div>
			    </li>
			    <li class="flex flex_column sidebarmenu">
			    	<?php
			    	$config = array(
			    		'template' => 'menu-member',
			    		'cat_id'   => '3',
			    		'submenu'  => 'bottom right'
			    		);
						include _ROOT.'blocks/menu/_switch.php';
			    	?>
			    </li>
			  </ul>
			</div>
	    <div class="main-content">
	      <div class="main-content_wrapper">
					<section class="topnav flex">
					  <div class="flex">
					    <div class="topnav-item" id="sidebar__toggle">
					      <i class="fa fa-align-justify"></i>
					    </div>
					  </div>
					  <div class="flex">
					    <div class="topnav-item" id="alert" >
						  	<?php
						  	$block = new stdClass;
						  	$block->title = icon('fa-bell-o');
						  	include _ROOT.'blocks/layout/Notification.html.php';
						  	?>
					    </div>
					    <div class="topnav-item">
								<div class="dropdown topnav-welcome">
					        <a href="#" class="dropdown-toggle" id="menu-top" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					        	<?php echo lang('Hello, %s', strstr($Bbc->member['name'], ' ', true)); ?> <i class="fa fa-caret-down"></i>
					        </a>
								  <ul class="card notifications dropdown-menu dropdown-menu-right" aria-labelledby="menu-top">
						        <a href="<?php echo site_url('index.php?mod=user.password'); ?>">
						        	<li class="notification">
						        		<?php echo icon('fa-unlock-alt'); ?> <?php echo lang('Change Password') ?>
						        	</li>
						        </a>
						        <a href="<?php echo site_url('index.php?mod=user.logout'); ?>">
						        	<li class="notification">
						        		<?php echo icon('fa-sign-out'); ?> <?php echo lang('Log Out') ?>
							        </li>
							      </a>
								  </ul>
								</div>
					    </div>
					  </div>
					</section>
	        <section class="section__title">
	          <div class="esd-container">
	            <div class="wrapper section__title--background my-4">
	              <h2><?php echo $sys->nav_show();?></h2>
	            </div>
	          </div>
	        </section>
	        <section class="section__content">
	          <div class="esd-container">
	          	<div class="wrapper">
	          		<div class="row">
			            <?php echo trim($Bbc->content); ?>
	          		</div>
	          	</div>
	          </div>
	        </section>
	      </div>
	    </div>
		</div>

		<!-- Bootstrap JavaScript -->
		<script src="<?php echo _URL;?>templates/admin/bootstrap/js/bootstrap.min.js"></script>
		<?php
		$sys->link_js($sys->template_url.'js/application.js', false);
		// $sys->link_js($sys->template_url.'js/main.js', false);
		echo $sys->block_show('debug');
		?>
	</body>
</html>