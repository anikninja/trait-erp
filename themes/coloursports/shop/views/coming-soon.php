<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<link href="https://fonts.googleapis.com/css?family=Nunito:100,200,600" rel="stylesheet">
<style>
	html, body {
		color: #fff;
		font-family: 'Nunito', sans-serif;
		font-weight: 200;
		height: 100vh;
		margin: 0;
		text-shadow: 1px 2px 10px rgba(0, 0, 0, 0.44);
	}
	
	body {
		background: url(<?php echo $assets; ?>coming-soon.jpg) no-repeat 50% 30%;
		background-size: cover;
	}
	
	.full-height {
		height: 100vh;
	}
	
	.flex-center {
		align-items: center;
		display: flex;
		justify-content: center;
	}
	
	.position-ref {
		position: relative;
	}
	
	.content {
		text-align: center;
	}
	
	.title {
		font-size: 50px;
		text-transform: uppercase;
		font-weight: 700;
		margin: 10px 0;
	}
	
	.sub-title {
		font-size: 30px;
		text-transform: uppercase;
	}
	
	.links > a {
		color: #fff;
		display: inline-block;
		font-size: 20px;
		font-weight: 600;
		letter-spacing: .1rem;
		text-decoration: none;
		text-transform: uppercase;
	}
	
	.links > a:hover {
		color: #d8d8d8;
	}
</style>
<div class="flex-center position-ref full-height">
	<div class="content">
		<div class="sub-title">Our new site is</div>
		<div class="title">Comming Soon</div>
		<div class="sub-title">Stay Tuned</div>
		<div class="links">
			<a class="stay-tuned" href="#"><i class="fa fa-envelope"></i></a>
		</div>
	</div>
</div>
