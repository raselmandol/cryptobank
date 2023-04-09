<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- CSRF Token -->
		<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

		<title>Installation</title>
		<!-- Google font -->
		<link href="https://fonts.googleapis.com/css?family=Lato:700%7CMontserrat:400,600" rel="stylesheet">

		<!-- Bootstrap -->
		<link type="text/css" rel="stylesheet" href="<?php echo e(asset('public/install_asset/css/bootstrap.min.css')); ?>"/>

		<link type="text/css" rel="stylesheet" href="<?php echo e(asset('public/install_asset/css/select2.css')); ?>"/>

		<!-- Custom stlylesheet -->
		<link type="text/css" rel="stylesheet" href="<?php echo e(asset('public/install_asset/css/style.css')); ?>"/>

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

    </head>
	<body>

		<div class="container">
			<div class="row">
			    <div class="col-md-6 offset-md-3">
					<div class="install-container">
						<?php echo $__env->yieldContent('content'); ?>
					</div>
				</div>
			</div>
		</div>

		<!-- jQuery Plugins -->
		<script type="text/javascript" src="<?php echo e(asset('public/install_asset/js/jquery-3.6.0.min.js')); ?>"></script>
		<script type="text/javascript" src="<?php echo e(asset('public/install_asset/js/bootstrap.min.js')); ?>"></script>
		<script type="text/javascript" src="<?php echo e(asset('public/install_asset/js/select2.min.js')); ?>"></script>
		<?php echo $__env->yieldContent('js-script'); ?>
	</body>
</html><?php /**PATH /home/k3lobc2fo9t4/blockchain/resources/views/install/layout.blade.php ENDPATH**/ ?>